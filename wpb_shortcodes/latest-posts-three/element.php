<?php
/**
 * Latest Posts Three WPBakery element definition
 *
 * Renders latest 3 published blog posts in a card grid.
 */

$map = [
  'name'        => __( 'Latest Posts (3)', 'dealsdot-child' ),
  'base'        => 'wpb_latest_posts_three',
  'description' => __( 'Displays 3 latest blog posts', 'dealsdot-child' ),
  'category'    => __( 'Custom', 'dealsdot-child' ),
  'params'      => [],
];

$template_rel = 'wpb_shortcodes/latest-posts-three/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {
  $posts = get_posts([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 3,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
  ]);

  $items = [];
  foreach ( $posts as $post ) {
    $post_id = (int) $post->ID;
    $image   = get_the_post_thumbnail_url( $post_id, 'large' );
    $featured = empty( $items );

    $items[] = [
      'title'      => get_the_title( $post_id ),
      'date'       => get_the_date( 'd.m.Y.', $post_id ),
      'url'        => get_permalink( $post_id ),
      'image_url'  => $image ? $image : '',
      'image_alt'  => get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true ),
      'featured'   => $featured,
    ];
  }

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

wpb_register_element([
  'base'   => 'wpb_latest_posts_three',
  'map'    => $map,
  'render' => $render,
]);
