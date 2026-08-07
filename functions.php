<?php

/**s
 * functions.php
 * @package WordPress
 * @subpackage Dealsdot
 * @since Dealsdot 1.0
 * 
 */

add_action( 'wp_enqueue_scripts', 'dealsdot_enqueue_styles', 99 );
function dealsdot_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_script(
        'osn-mobile-menu',
        get_stylesheet_directory_uri() . '/assets/js/mobile-menu.js',
        [],
        filemtime( get_stylesheet_directory() . '/assets/js/mobile-menu.js' ),
        true
    );
}

// Remove col-lg-6 wrapping added by parent theme for CF7 name/email fields.
// setTimeout(0) ensures this runs after the parent's nested $(function(){}) wrap code.
add_action( 'wp_enqueue_scripts', function () {
    wp_add_inline_script( 'dealsdot-scripts', "
jQuery(document).ready(function($){
    setTimeout(function(){
        var \$form = $('.wpcf7-form');
        \$form.find('.col-lg-6').removeClass('col-lg-6');
        \$form.find('.form-group').removeClass('form-group');
        \$form.find('.row > p').unwrap();
    }, 0);
});
" );
}, 100 );

// Replace click-to-toggle dropdown with hover-open; click on top-level item follows its link.
add_action( 'wp_enqueue_scripts', function () {
    wp_add_inline_script( 'dealsdot-scripts', "
jQuery(document).ready(function($){
    setTimeout(function(){
        if ( ! window.matchMedia('(min-width: 1025px)').matches ) {
            return;
        }

        var \$nav = $('.navbar-nav');

        // Remove parent theme's click handler that prevents default and toggles submenu.
        \$nav.find('.dropdown > .dropdown-toggle').off('click').on('click', function(e){
            var href = \$(this).attr('href');
            if ( href && href !== '#' && href !== '' ) {
                window.location.href = href;
            }
        });

        // Open / close submenu on hover.
        \$nav.find('.dropdown').on('mouseenter', function(){
            \$(this).children('.dropdown-menu').stop(true, true).slideDown(200);
        }).on('mouseleave', function(){
            \$(this).children('.dropdown-menu').stop(true, true).slideUp(200);
        });
    }, 0);
});
" );
}, 100 );

/* WP Bakery shortcodes / widgets */
 

/**
 * Reusable WPBakery element loader
 * - Minimal registration in this file
 * - Element specifics live under wpb_shortcodes/<slug>/element.php
 * - Templates live under wpb_shortcodes/<slug>/templates/
 */

if ( ! function_exists( 'wpb_register_element' ) ) {
  function wpb_register_element( array $config ) {
    if ( empty( $config['base'] ) ) return;
    if ( empty( $config['map'] ) || ! is_array( $config['map'] ) ) return;
    global $wpb_registered_elements;
    if ( ! is_array( $wpb_registered_elements ) ) $wpb_registered_elements = [];
    $wpb_registered_elements[ $config['base'] ] = $config;
  }
}

// Load element definitions from wpb_shortcodes/*/element.php
add_action( 'after_setup_theme', function () {
  $dir = get_stylesheet_directory() . '/wpb_shortcodes';
  if ( ! is_dir( $dir ) ) return;
  foreach ( glob( $dir . '/*/element.php' ) as $file ) {
    require_once $file;
  }
}, 20 );

/* -----------------------------------------------------------------------
 * Shared edukacija card renderer (used by archive and page templates).
 * ----------------------------------------------------------------------- */
if ( ! function_exists( 'osn_edu_render_card' ) ) :
    function osn_edu_render_card( int $post_id, bool $is_featured = false ): void {
        $title      = get_the_title( $post_id );
        $img_url    = get_the_post_thumbnail_url( $post_id, 'large' );
        $post_link  = get_permalink( $post_id );
        $datum_ts   = (int) get_post_meta( $post_id, 'datum', true );
        $datum_str  = $datum_ts ? date_i18n( 'd.m.Y.', $datum_ts ) : '';
        $format     = get_post_meta( $post_id, 'format_edukacije', true );
        $sponsor_id = get_post_meta( $post_id, 'logo-sponzora', true );
        $excerpt    = get_post_field( 'post_excerpt', $post_id );
        $card_class = 'osn-edu-card' . ( $is_featured ? ' osn-edu-card--featured' : '' );
        ?>
        <a href="<?php echo esc_url( $post_link ); ?>" class="<?php echo esc_attr( $card_class ); ?>">
            <?php if ( $img_url ) : ?>
            <div class="osn-edu-card__img-wrap">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
            </div>
            <?php endif; ?>
            <div class="osn-edu-card__body">
                <?php if ( ! $is_featured && ( $datum_str || $format ) ) : ?>
                <div class="osn-edu-card__meta">
                    <?php echo $datum_str ? '<span class="osn-edu-card__date">' . esc_html( $datum_str ) . '</span>' : ''; ?>
                    <?php echo $format ? '<span class="osn-edu-card__format">' . esc_html( $format ) . '</span>' : ''; ?>
                </div>
                <?php endif; ?>
                <h3 class="osn-edu-card__title"><?php echo esc_html( $title ); ?></h3>
                <?php if ( $is_featured && $excerpt ) : ?>
                <p class="osn-edu-card__subtitle"><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>
                <?php if ( ! $is_featured && $sponsor_id ) :
                    $sponsor_img = wp_get_attachment_image( $sponsor_id, [ 137, 50 ], false, [
                        'class'   => 'osn-edu-card__sponsor-img',
                        'loading' => 'lazy',
                    ] );
                    if ( $sponsor_img ) : ?>
                <div class="osn-edu-card__sponsor">
                    <span class="osn-edu-card__sponsor-label"><?php esc_html_e( 'Sponzor edukacije', 'dealsdot-child' ); ?></span>
                    <?php echo $sponsor_img; ?>
                </div>
                    <?php endif;
                endif; ?>
            </div>
        </a>
        <?php
    }
endif;

/* -----------------------------------------------------------------------
 * Edukacija editor fields.
 * ----------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'osn_edukacija_format',
        __( 'Format edukacije', 'dealsdot-child' ),
        'osn_edukacija_format_meta_box',
        'edukacija',
        'side',
        'default'
    );
    add_meta_box(
        'osn_mentor_opis',
        __( 'Opis', 'dealsdot-child' ),
        'osn_mentor_opis_meta_box',
        'mentor',
        'normal',
        'default'
    );
} );

if ( ! function_exists( 'osn_edukacija_format_meta_box' ) ) :
    function osn_edukacija_format_meta_box( WP_Post $post ): void {
        $format = get_post_meta( $post->ID, 'format_edukacije', true );

        wp_nonce_field( 'osn_save_edukacija_format', 'osn_edukacija_format_nonce' );
        ?>
        <p>
            <label for="osn-format-edukacije">
                <?php esc_html_e( 'Primer: Online edukacija', 'dealsdot-child' ); ?>
            </label>
        </p>
        <input
            type="text"
            id="osn-format-edukacije"
            name="format_edukacije"
            value="<?php echo esc_attr( $format ); ?>"
            class="widefat"
        />
        <?php
    }
endif;

if ( ! function_exists( 'osn_mentor_opis_meta_box' ) ) :
    function osn_mentor_opis_meta_box( WP_Post $post ): void {
        $opis = get_post_meta( $post->ID, 'opis_mentora', true );

        wp_nonce_field( 'osn_save_mentor_opis', 'osn_mentor_opis_nonce' );
        wp_editor( $opis, 'osn_opis_mentora', [
            'media_buttons' => true,
            'textarea_name' => 'opis_mentora',
            'textarea_rows' => 10,
            'teeny'         => false,
        ] );
    }
endif;

add_action( 'save_post_edukacija', function ( int $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if (
        ! isset( $_POST['osn_edukacija_format_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['osn_edukacija_format_nonce'] ) ), 'osn_save_edukacija_format' )
    ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $format = isset( $_POST['format_edukacije'] )
        ? sanitize_text_field( wp_unslash( $_POST['format_edukacije'] ) )
        : '';

    if ( $format !== '' ) {
        update_post_meta( $post_id, 'format_edukacije', $format );
    } else {
        delete_post_meta( $post_id, 'format_edukacije' );
    }
} );

add_action( 'save_post_mentor', function ( int $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save opis_mentora
    if (
        isset( $_POST['osn_mentor_opis_nonce'] )
        && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['osn_mentor_opis_nonce'] ) ), 'osn_save_mentor_opis' )
    ) {
        $opis = isset( $_POST['opis_mentora'] )
            ? wp_kses_post( wp_unslash( $_POST['opis_mentora'] ) )
            : '';

        if ( $opis !== '' ) {
            update_post_meta( $post_id, 'opis_mentora', $opis );
        } else {
            delete_post_meta( $post_id, 'opis_mentora' );
        }
    }
} );

/* -----------------------------------------------------------------------
 * AJAX: Load-more handler for the "Besplatne edukacije" page template.
 * ----------------------------------------------------------------------- */
add_action( 'wp_ajax_osn_besplatne_loadmore',        'osn_besplatne_loadmore_handler' );
add_action( 'wp_ajax_nopriv_osn_besplatne_loadmore', 'osn_besplatne_loadmore_handler' );

function osn_besplatne_loadmore_handler(): void {
    check_ajax_referer( 'osn_besplatne_loadmore', 'nonce' );

    $per_page = 6;
    $offset   = max( 0, (int) ( $_POST['offset'] ?? 0 ) );

    $query = new WP_Query( [
        'post_type'      => 'edukacija',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'offset'         => $offset,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'datum',
        'order'          => 'DESC',
        'tax_query'      => [ [
            'taxonomy' => 'vrsta-edukacije',
            'field'    => 'slug',
            'terms'    => 'besplatne-edukacije',
        ] ],
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'sakri-iz-kategorije',
                'value'   => 'Da',
                'compare' => '!=',
            ],
            [
                'key'     => 'sakri-iz-kategorije',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ] );

    ob_start();
    while ( $query->have_posts() ) {
        $query->the_post();
        osn_edu_render_card( get_the_ID(), false );
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json( [
        'html'     => $html,
        'count'    => $query->post_count,
        'has_more' => ( $offset + $query->post_count ) < $query->found_posts,
    ] );
}

// Register elements with WPBakery after it initializes
add_action( 'vc_after_init', function () {
  if ( ! function_exists( 'vc_map' ) ) return;
  global $wpb_registered_elements;
  if ( empty( $wpb_registered_elements ) || ! is_array( $wpb_registered_elements ) ) return;

  foreach ( $wpb_registered_elements as $base => $config ) {
    $map = $config['map'];
    // Ensure base consistency
    $map['base'] = $base;
    // Register with WPBakery
    vc_map( $map );

    // Register shortcode render callback
    if ( ! empty( $config['render'] ) && is_callable( $config['render'] ) ) {
      add_shortcode( $base, $config['render'] );
    }
  }
}, 20 );

/* -----------------------------------------------------------------------
 * Blog archive: Sort posts - every 1st, 5th, 10th, 15th etc. is tag-nagrade
 * ----------------------------------------------------------------------- */
add_action( 'pre_get_posts', function ( WP_Query $query ) {
    if ( ! is_admin() && ( $query->is_home() || $query->is_archive( 'post' ) ) && $query->is_main_query() ) {
        // Get all post IDs with tag-nagrade
        $nagrade_term = get_term_by( 'slug', 'nagrade', 'post_tag' );
        if ( ! $nagrade_term ) {
            return;
        }

        $nagrade_posts = get_posts( [
            'numberposts' => -1,
            'tax_query'   => [
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $nagrade_term->term_id,
                ],
            ],
            'fields'      => 'ids',
            'orderby'     => 'date',
            'order'       => 'DESC',
        ] );

        if ( empty( $nagrade_posts ) ) {
            return;
        }

        // Get all OTHER posts (without tag-nagrade)
        $other_posts = get_posts( [
            'numberposts' => -1,
            'tax_query'   => [
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $nagrade_term->term_id,
                    'operator' => 'NOT IN',
                ],
            ],
            'fields'      => 'ids',
            'orderby'     => 'date',
            'order'       => 'DESC',
        ] );

        // Intersperse: 1st nagrade, then 4 others, then 1 nagrade, then 4 others, etc.
        $ordered_posts = [];
        $nagrade_index = 0;
        $other_index   = 0;
        $position      = 0;

        while ( $nagrade_index < count( $nagrade_posts ) || $other_index < count( $other_posts ) ) {
            $position++;
            
            // Every 1st, 5th, 10th, 15th position (1, 5, 10, 15, 20...)
            if ( $position % 5 == 1 && $nagrade_index < count( $nagrade_posts ) ) {
                $ordered_posts[] = $nagrade_posts[ $nagrade_index ];
                $nagrade_index++;
            } elseif ( $other_index < count( $other_posts ) ) {
                $ordered_posts[] = $other_posts[ $other_index ];
                $other_index++;
            } elseif ( $nagrade_index < count( $nagrade_posts ) ) {
                $ordered_posts[] = $nagrade_posts[ $nagrade_index ];
                $nagrade_index++;
            }
        }

        $query->set( 'post__in', $ordered_posts );
        $query->set( 'orderby', 'post__in' );
    }
} );

/* -----------------------------------------------------------------------
 * Mobile only: Sort archive cards so tag-nagrade appears at 1st, 5th, 10th positions
 * ----------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', function () {
    if ( is_home() || is_archive( 'post' ) ) {
        wp_add_inline_script( 'dealsdot-scripts', "
            document.addEventListener( 'DOMContentLoaded', function() {
                function sortArchiveCardsOnMobile() {
                    if ( window.innerWidth > 767 ) return;
                    
                    const grid = document.querySelector( '.osn-arc__grid' );
                    if ( ! grid ) return;
                    
                    const cards = Array.from( grid.querySelectorAll( '.osn-arc__card' ) );
                    const nagradeCards = [];
                    const otherCards = [];
                    
                    // Separate tag-nagrade cards from others
                    cards.forEach( card => {
                        if ( card.classList.contains( 'tag-nagrade' ) ) {
                            nagradeCards.push( card );
                        } else {
                            otherCards.push( card );
                        }
                    } );
                    
                    // Rebuild grid: every 1st, 5th, 10th position gets tag-nagrade
                    const orderedCards = [];
                    let nagradeIdx = 0;
                    let otherIdx = 0;
                    let position = 0;
                    
                    while ( nagradeIdx < nagradeCards.length || otherIdx < otherCards.length ) {
                        position++;
                        if ( position % 5 === 1 && nagradeIdx < nagradeCards.length ) {
                            orderedCards.push( nagradeCards[nagradeIdx++] );
                        } else if ( otherIdx < otherCards.length ) {
                            orderedCards.push( otherCards[otherIdx++] );
                        } else if ( nagradeIdx < nagradeCards.length ) {
                            orderedCards.push( nagradeCards[nagradeIdx++] );
                        }
                    }
                    
                    // Reorder DOM
                    orderedCards.forEach((card, index) => {
                        grid.appendChild( card );

                         if (card.classList.contains('tag-nagrade')) {
                            const divider = document.createElement('div');
                            divider.className = 'osn-arc__divider';
                            grid.appendChild(divider);
                            if(index === 0){
                                const latest = document.createElement('span');
                                latest.textContent = 'Najnovije';
                                latest.className = 'osn-arc__najnovije';
                                grid.appendChild(latest);
                            }
                        }
                    } );
                }
    
            sortArchiveCardsOnMobile();
        window.addEventListener( 'resize', sortArchiveCardsOnMobile );
    } );
" );
    }
}, 20 );