<?php
/**
 * Tim Grid – WPBakery element
 *
 * CPT: tim
 * ACF fields:
 *   order  (number)            – display order (ASC)
 *   link   (url or ACF array)  – optional card link
 *
 * Role/position is stored in post_content.
 *
 * Member selection uses a custom 'tim_selector' param type: a native
 * <select multiple> rendered in PHP at form-load time. The selected
 * post IDs are stored as a comma-separated string in a hidden text
 * input that WPBakery serialises normally.  Empty = show all members.
 */

// ── Custom param type: tim_selector ────────────────────────────────────
// Must be registered before vc_map() runs (vc_before_init fires first).

add_action( 'vc_before_init', function () {

    if ( ! function_exists( 'vc_add_shortcode_param' ) ) {
        return;
    }

    vc_add_shortcode_param(
        'tim_selector',
        function ( $settings, $value ) {

            // Query tim posts at form-render time (CPTs are registered by now)
            $posts = get_posts( [
                'post_type'      => 'tim',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'meta_key'       => 'order',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ] );

            $param_name   = $settings['param_name'];
            $selected_ids = array_filter( array_map( 'intval', explode( ',', (string) $value ) ) );
            // Unique ID scoped to this render so the JS closure is safe
            $uid = 'tim-sel-' . substr( md5( $param_name . microtime() ), 0, 8 );

            ob_start();
            ?>
            <div class="osn-tim-selector">
                <?php if ( empty( $posts ) ) : ?>
                    <p style="color:#999;font-size:12px;"><?php esc_html_e( 'Nema objavljenih članova tima.', 'dealsdot-child' ); ?></p>
                <?php else : ?>
                    <select
                        multiple
                        size="<?php echo min( count( $posts ), 10 ); ?>"
                        style="width:100%;border:1px solid #ddd;border-radius:3px;padding:4px 6px;font-size:13px;"
                        onchange="(function(s){var h=document.getElementById('<?php echo esc_attr( $uid ); ?>');h.value=Array.from(s.selectedOptions).map(function(o){return o.value}).join(',');jQuery(h).trigger('change');})(this)"
                    >
                        <?php foreach ( $posts as $pid ) : ?>
                        <option
                            value="<?php echo esc_attr( $pid ); ?>"
                            <?php selected( in_array( $pid, $selected_ids, true ) ); ?>
                        ><?php echo esc_html( get_the_title( $pid ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p style="font-size:11px;color:#aaa;margin:4px 0 0;">
                        <?php esc_html_e( 'Ctrl / Cmd za višestruki izbor. Bez odabira = svi članovi (sortiranje po polju "order").', 'dealsdot-child' ); ?>
                    </p>
                <?php endif; ?>

                <?php /* WPBakery reads values via [name=param_name].wpb_vc_param_value */ ?>
                <input
                    type="hidden"
                    id="<?php echo esc_attr( $uid ); ?>"
                    name="<?php echo esc_attr( $param_name ); ?>"
                    class="wpb_vc_param_value"
                    value="<?php echo esc_attr( implode( ',', $selected_ids ) ); ?>"
                >
            </div>
            <?php
            return ob_get_clean();
        }
    );
} );

// ── WPBakery map ────────────────────────────────────────────────────────

$map = [
    'name'        => __( 'Tim Grid', 'dealsdot-child' ),
    'base'        => 'wpb_tim_grid',
    'description' => __( 'Grid of team member cards (Tim CPT)', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [
        [
            'type'        => 'tim_selector',
            'heading'     => __( 'Članovi tima', 'dealsdot-child' ),
            'param_name'  => 'member_ids',
            'value'       => '',
            'description' => '',
        ],
        [
            'type'        => 'checkbox',
            'heading'     => __( 'Feature first row for mobile display', 'dealsdot-child' ),
            'param_name'  => 'feature_first_mobile',
            'value'       => [ __( 'Da', 'dealsdot-child' ) => 'yes' ],
            'std'         => 'yes',
            'description' => __( 'Uključeno: prva kartica je istaknuta (horizontalna), ostale u dve kolone. Isključeno: sve kartice su horizontalne, naslagane jedna ispod druge. Važi samo za mobilni prikaz (<768px).', 'dealsdot-child' ),
        ],
    ],
];

$template_rel = 'wpb_shortcodes/tim-grid/templates/template.php';

// ── Helper: URL from ACF link field (string or array) ───────────────────

$_extract_link = function ( $raw ) {
    if ( empty( $raw ) ) {
        return '';
    }
    if ( is_array( $raw ) ) {
        return ! empty( $raw['url'] ) ? (string) $raw['url'] : '';
    }
    return (string) $raw;
};

// ── Render callback ─────────────────────────────────────────────────────

$render = function ( $atts, $content = '' ) use ( $template_rel, $_extract_link ) {

    $atts = shortcode_atts(
        [
            'member_ids'           => '',
            // Default 'yes' keeps the featured-first mobile layout for grids
            // saved before this option existed.
            'feature_first_mobile' => 'yes',
        ],
        $atts,
        'wpb_tim_grid'
    );

    $feature_first_mobile = ! in_array( (string) $atts['feature_first_mobile'], [ '', 'false', '0' ], true );

    $selected_ids = array_values(
        array_filter( array_map( 'intval', explode( ',', (string) $atts['member_ids'] ) ) )
    );

    $query_args = [
        'post_type'      => 'tim',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_key'       => 'order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ];

    if ( ! empty( $selected_ids ) ) {
        $query_args['post__in'] = $selected_ids;
    }

    $q       = new WP_Query( $query_args );
    $members = [];

    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $pid       = get_the_ID();
            $members[] = [
                'name'    => get_the_title(),
                'role'    => wp_strip_all_tags( get_the_content() ),
                'link'    => $_extract_link( get_post_meta( $pid, 'link', true ) ),
                'img_url' => get_the_post_thumbnail_url( $pid, 'large' ) ?: '',
            ];
        }
        wp_reset_postdata();
    }

    ob_start();
    $tpl = trailingslashit( get_stylesheet_directory() ) . $template_rel;
    if ( file_exists( $tpl ) ) {
        include $tpl;
    }
    return ob_get_clean();
};

// ── Registration ────────────────────────────────────────────────────────

wpb_register_element( [
    'base'   => 'wpb_tim_grid',
    'map'    => $map,
    'render' => $render,
] );
