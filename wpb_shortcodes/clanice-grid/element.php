<?php
/**
 * Članice Grid WPBakery element definition
 *
 * Displays an AJAX-filtered, paginated grid of 'osnazena' CPT members.
 *
 * ACF fields on each 'osnazena' post:
 *   naslovna_slika       – image (attachment ID)
 *   ime_i_prezime_vlasnice – text – owner full name
 *   titula               – text – job title / occupation
 *   zvezdica             – text – badge tier: 'zlatna' (gold) | 'srebrna' (silver) | empty
 *
 * Taxonomies:
 *   drzava     – country
 *   delatnost  – industry
 */

// ---------- WPBakery map ----------------------------------------

$map = [
    'name'        => __( 'Članice Grid', 'dealsdot-child' ),
    'base'        => 'wpb_clanice_grid',
    'description' => __( 'Filterable grid of Osnažena members', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [
        [
            'type'        => 'textfield',
            'heading'     => __( 'Posts per page', 'dealsdot-child' ),
            'param_name'  => 'posts_per_page',
            'value'       => '12',
            'description' => __( 'Members per page (default 12).', 'dealsdot-child' ),
        ],
    ],
];

$template_rel = 'wpb_shortcodes/clanice-grid/templates/template.php';

// ---------- Render callback -------------------------------------

$render = function( $atts, $content = '' ) use ( $template_rel ) {
    $atts = shortcode_atts( [
        'posts_per_page' => '12',
    ], $atts, 'wpb_clanice_grid' );

    $per_page = max( 1, (int) $atts['posts_per_page'] );

    // Enqueue widget JS
    $script_rel = '/wpb_shortcodes/clanice-grid/assets/clanice-grid.js';
    $script_abs = get_stylesheet_directory() . $script_rel;
    $script_uri = get_stylesheet_directory_uri() . $script_rel;
    wp_enqueue_script(
        'osn-clanice-grid',
        $script_uri,
        [],
        file_exists( $script_abs ) ? (string) filemtime( $script_abs ) : null,
        true
    );
    wp_localize_script( 'osn-clanice-grid', 'OsnClanice', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'osn_clanice_grid' ),
        'cities'  => osn_clanice_grid_get_city_options(),
    ] );

    // Taxonomy terms for filter dropdowns
    $drzava_terms    = get_terms( [ 'taxonomy' => 'drzava',    'hide_empty' => true ] );
    $delatnost_terms = get_terms( [ 'taxonomy' => 'delatnost', 'hide_empty' => true ] );
    if ( is_wp_error( $drzava_terms ) )    $drzava_terms    = [];
    if ( is_wp_error( $delatnost_terms ) ) $delatnost_terms = [];

    // Initial server-side query
    $result       = osn_clanice_grid_do_query( '', 0, 0, '', '', 1, $per_page );
    $posts        = $result['posts'];
    $total        = $result['total'];
    $pages        = $result['pages'];
    $current_page = 1;

    // Unique instance ID for aria/JS hooks
    static $instance_counter = 0;
    $instance_id = 'osn-clanice-' . ( ++$instance_counter );

    ob_start();
    $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
    if ( file_exists( $template_abs ) ) {
        include $template_abs;
    }
    return ob_get_clean();
};

// ---------- City taxonomy helpers -------------------------------

if ( ! function_exists( 'osn_clanice_grid_city_taxonomies' ) ) {
    function osn_clanice_grid_city_taxonomies() {
        return [
            'mesto'              => [ 'srbija', 'serbia' ],
            'mesto-austrija'     => [ 'austrija', 'austria' ],
            'mesto-bih'          => [ 'bih', 'bosna-i-hercegovina', 'bosna-hercegovina' ],
            'mesto-crna-gora'    => [ 'crna-gora', 'montenegro' ],
            'mesto-hrvatska'     => [ 'hrvatska', 'croatia' ],
            'mesto-madarska'     => [ 'madarska', 'hungary' ],
            'mesto-makedonija'   => [ 'makedonija', 'macedonia', 'severna-makedonija' ],
            'mesto-nemacka'      => [ 'nemacka', 'germany' ],
            'mesto-slovenija'    => [ 'slovenija', 'slovenia' ],
            'mesto-svajcarska'   => [ 'svajcarska', 'switzerland' ],
            'mesto-usa-canada'   => [ 'usa-canada', 'usa-and-canada', 'kanada', 'canada' ],
        ];
    }
}

if ( ! function_exists( 'osn_clanice_grid_normalize_key' ) ) {
    function osn_clanice_grid_normalize_key( $value ) {
        return sanitize_title( remove_accents( (string) $value ) );
    }
}

if ( ! function_exists( 'osn_clanice_grid_city_country_ids' ) ) {
    function osn_clanice_grid_city_country_ids() {
        $country_ids = [];
        foreach ( osn_clanice_grid_city_taxonomies() as $taxonomy => $aliases ) {
            $country_ids[ $taxonomy ] = [];
        }

        $drzava_terms = get_terms( [
            'taxonomy'   => 'drzava',
            'hide_empty' => false,
        ] );
        if ( is_wp_error( $drzava_terms ) || empty( $drzava_terms ) ) {
            return $country_ids;
        }

        foreach ( $drzava_terms as $term ) {
            $keys = [
                osn_clanice_grid_normalize_key( $term->name ),
                osn_clanice_grid_normalize_key( $term->slug ),
            ];

            foreach ( osn_clanice_grid_city_taxonomies() as $taxonomy => $aliases ) {
                if ( array_intersect( $keys, $aliases ) ) {
                    $country_ids[ $taxonomy ][] = (int) $term->term_id;
                }
            }
        }

        return $country_ids;
    }
}

if ( ! function_exists( 'osn_clanice_grid_get_city_options' ) ) {
    function osn_clanice_grid_get_city_options() {
        $city_options = [];
        $country_ids  = osn_clanice_grid_city_country_ids();

        foreach ( array_keys( osn_clanice_grid_city_taxonomies() ) as $taxonomy ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            $terms = get_terms( [
                'taxonomy'   => $taxonomy,
                'hide_empty' => true,
            ] );
            if ( is_wp_error( $terms ) || empty( $terms ) ) {
                continue;
            }

            foreach ( $terms as $term ) {
                $city_options[] = [
                    'name'        => $term->name,
                    'taxonomy'    => $taxonomy,
                    'term_id'     => (int) $term->term_id,
                    'value'       => $taxonomy . ':' . (int) $term->term_id,
                    'country_ids' => $country_ids[ $taxonomy ] ?? [],
                ];
            }
        }

        usort( $city_options, function( $a, $b ) {
            return strcasecmp( $a['name'], $b['name'] );
        } );

        return $city_options;
    }
}

if ( ! function_exists( 'osn_clanice_grid_parse_city_filter' ) ) {
    function osn_clanice_grid_parse_city_filter( $filter_mesto ) {
        $filter_mesto = sanitize_text_field( (string) $filter_mesto );
        if ( empty( $filter_mesto ) || strpos( $filter_mesto, ':' ) === false ) {
            return null;
        }

        list( $taxonomy, $term_id ) = explode( ':', $filter_mesto, 2 );
        $taxonomy = sanitize_key( $taxonomy );
        $term_id  = (int) $term_id;

        if ( ! in_array( $taxonomy, array_keys( osn_clanice_grid_city_taxonomies() ), true ) || $term_id <= 0 ) {
            return null;
        }

        if ( ! term_exists( $term_id, $taxonomy ) ) {
            return null;
        }

        return [
            'taxonomy' => $taxonomy,
            'term_id'  => $term_id,
        ];
    }
}

// ---------- Shared query helper ---------------------------------

if ( ! function_exists( 'osn_clanice_grid_do_query' ) ) {
    function osn_clanice_grid_do_query( $filter_ime, $filter_drzava, $filter_delatnost, $filter_mesto, $filter_bedz, $page, $per_page = 12 ) {
        $args = [
            'post_type'      => 'osnazena',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => max( 1, (int) $page ),
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_order',
            'order'          => 'ASC',
        ];

        $tax_query = [];
        if ( ! empty( $filter_drzava ) ) {
            $tax_query[] = [
                'taxonomy' => 'drzava',
                'field'    => 'term_id',
                'terms'    => (int) $filter_drzava,
            ];
        }
        if ( ! empty( $filter_delatnost ) ) {
            $tax_query[] = [
                'taxonomy' => 'delatnost',
                'field'    => 'term_id',
                'terms'    => (int) $filter_delatnost,
            ];
        }
        $city_filter = osn_clanice_grid_parse_city_filter( $filter_mesto );
        if ( ! empty( $city_filter ) ) {
            $tax_query[] = [
                'taxonomy' => $city_filter['taxonomy'],
                'field'    => 'term_id',
                'terms'    => $city_filter['term_id'],
            ];
        }
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        $meta_query = [];
        if ( ! empty( $filter_ime ) ) {
            $meta_query[] = [
                'key'     => 'ime_i_prezime_vlasnice',
                'value'   => sanitize_text_field( $filter_ime ),
                'compare' => 'LIKE',
            ];
        }

        $badge_map    = [
            'gold'    => [ 'gold', 'zlatna' ],
            'silver'  => [ 'silver', 'srebrna' ],
            'starter' => [ 'starter' ],
        ];
        $badge_values = $badge_map[ sanitize_key( (string) $filter_bedz ) ] ?? [];
        if ( ! empty( $badge_values ) ) {
            $meta_query[] = [
                'key'     => 'zvezdica',
                'value'   => $badge_values,
                'compare' => 'IN',
            ];
        }

        if ( count( $meta_query ) > 1 ) {
            $meta_query['relation'] = 'AND';
        }
        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        $query = new WP_Query( $args );
        return [
            'posts' => $query->posts,
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ];
    }
}

// ---------- AJAX handler ----------------------------------------

if ( ! function_exists( 'osn_clanice_grid_ajax_handler' ) ) {
    function osn_clanice_grid_ajax_handler() {
        check_ajax_referer( 'osn_clanice_grid', 'nonce' );

        $filter_ime       = sanitize_text_field( wp_unslash( $_POST['ime']        ?? '' ) );
        $filter_drzava    = (int) ( $_POST['drzava']    ?? 0 );
        $filter_delatnost = (int) ( $_POST['delatnost'] ?? 0 );
        $filter_mesto     = sanitize_text_field( wp_unslash( $_POST['mesto']      ?? '' ) );
        $filter_bedz      = sanitize_key( wp_unslash( $_POST['bedz']       ?? '' ) );
        $page             = max( 1, (int) ( $_POST['paged']    ?? 1 ) );
        $per_page         = max( 1, min( 48, (int) ( $_POST['per_page'] ?? 12 ) ) );

        $result       = osn_clanice_grid_do_query( $filter_ime, $filter_drzava, $filter_delatnost, $filter_mesto, $filter_bedz, $page, $per_page );
        $posts        = $result['posts'];
        $total        = $result['total'];
        $pages        = $result['pages'];
        $current_page = $page;

        ob_start();
        $partial = get_stylesheet_directory() . '/wpb_shortcodes/clanice-grid/templates/partials/cards.php';
        if ( file_exists( $partial ) ) {
            include $partial;
        }
        $html = ob_get_clean();

        wp_send_json_success( [
            'html'  => $html,
            'total' => $total,
            'pages' => $pages,
            'page'  => $current_page,
        ] );
    }
}

add_action( 'wp_ajax_osn_clanice_grid_filter',        'osn_clanice_grid_ajax_handler' );
add_action( 'wp_ajax_nopriv_osn_clanice_grid_filter', 'osn_clanice_grid_ajax_handler' );

// ---------- Register element ------------------------------------

wpb_register_element( [
    'base'   => 'wpb_clanice_grid',
    'map'    => $map,
    'render' => $render,
] );
