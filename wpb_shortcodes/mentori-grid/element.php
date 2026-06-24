<?php
/**
 * Mentori Grid WPBakery element definition
 *
 * Displays a grid of 'mentor' CPT cards with photo, name, role, and action buttons.
 * Below the grid renders two CTA buttons ("Postani mentor" / "Postani menti").
 *
 * ACF fields on each 'mentor' post:
 *   uloga    – text  – optional role/subtitle
 *   cv_link  – URL   – CV download link
 */

// ---------- WPBakery map ----------------------------------------

$map = [
    'name'        => __( 'Mentori Grid', 'dealsdot-child' ),
    'base'        => 'wpb_mentori_grid',
    'description' => __( 'Grid of mentor cards + CTA buttons', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [],
];

$template_rel = 'wpb_shortcodes/mentori-grid/templates/template.php';

// ---------- Render callback -------------------------------------

$render = function( $atts, $content = '' ) use ( $template_rel ) {
    $atts = shortcode_atts( [], $atts, 'wpb_mentori_grid' );

    // Query mentor CPT
    $mentor_query = new WP_Query( [
        'post_type'      => 'mentor',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );

    $mentors = [];
    if ( $mentor_query->have_posts() ) {
        while ( $mentor_query->have_posts() ) {
            $mentor_query->the_post();
            $mid      = get_the_ID();
            $cv_link  = get_post_meta( $mid, 'cv', true );
            $mentors[] = [
                'id'       => $mid,
                'name'     => get_the_title(),
                'link'     => get_permalink(),
                'uloga'    => get_post_meta( $mid, 'uloga',   true ),
                'cv_link'  => ! empty( $cv_link ) ? $cv_link : get_post_meta( $mid, 'cv_link', true ),
                'img_url'  => get_the_post_thumbnail_url( $mid, 'large' ),
            ];
        }
        wp_reset_postdata();
    }

    ob_start();
    $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
    if ( file_exists( $template_abs ) ) {
        include $template_abs;
    }
    return ob_get_clean();
};

// ---------- Registration ----------------------------------------

wpb_register_element( [
    'base'   => 'wpb_mentori_grid',
    'map'    => $map,
    'render' => $render,
] );
