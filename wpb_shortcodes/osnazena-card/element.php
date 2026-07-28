<?php
/**
 * Osnažena Card WPBakery element definition
 * Select an "osnazena" CPT and render image, name, category.
 */

$base = 'wpb_osnazena_card';

$map = [
  'name'        => __( 'Osnažena Card', 'dealsdot-child' ),
  'base'        => $base,
  'description' => __( 'Box with image, name and category', 'dealsdot-child' ),
  'category'    => __( 'Custom', 'dealsdot-child' ),
  'params'      => [
    [
      'type'       => 'autocomplete',
      'heading'    => __( 'Select Osnažena', 'dealsdot-child' ),
      'param_name' => 'osnazena_id',
      'settings'   => [
        'multiple'        => false,
        'min_length'      => 1,
        'groups'          => false,
        'unique_values'   => true,
        'display_inline'  => true,
        'delay'           => 100,
      ],
      'description'=> __( 'Search and select a post from the "osnazena" CPT.', 'dealsdot-child' ),
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Fallback Post ID', 'dealsdot-child' ),
      'param_name' => 'post_id_fallback',
      'description'=> __( 'If autocomplete fails, enter the post ID manually.', 'dealsdot-child' ),
    ],
  ],
];

$template_rel = 'wpb_shortcodes/osnazena-card/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {
  $atts = shortcode_atts([
    'osnazena_id'      => '',
    'post_id_fallback' => '',
  ], $atts, 'wpb_osnazena_card');

  // Determine post ID from autocomplete or fallback
  $post_id = 0;
  if ( is_numeric( $atts['osnazena_id'] ) ) {
    $post_id = (int) $atts['osnazena_id'];
  } elseif ( is_string( $atts['osnazena_id'] ) && preg_match('/\d+/', $atts['osnazena_id'], $m) ) {
    $post_id = (int) $m[0];
  } elseif ( is_numeric( $atts['post_id_fallback'] ) ) {
    $post_id = (int) $atts['post_id_fallback'];
  }

  if ( $post_id <= 0 ) {
    return '';
  }

  $post = get_post( $post_id );
  if ( ! $post || 'osnazena' !== $post->post_type ) {
    return '';
  }

  // ACF fields
  $img_raw  = function_exists('get_field') ? get_field('naslovna_slika', $post_id ) : '';
  $name     = function_exists('get_field') ? get_field('ime_i_prezime_vlasnice', $post_id ) : get_the_title( $post );

  // Normalize image URL
  $image_url = '';
  if ( is_array( $img_raw ) && ! empty( $img_raw['url'] ) ) {
    $image_url = $img_raw['url'];
  } elseif ( is_numeric( $img_raw ) ) {
    $image_url = wp_get_attachment_image_url( (int) $img_raw, 'large' );
  } elseif ( is_string( $img_raw ) ) {
    $image_url = $img_raw;
  } elseif ( has_post_thumbnail( $post_id ) ) {
    $image_url = get_the_post_thumbnail_url( $post_id, 'large' );
  }

  // Taxonomy "delatnost" (first term name)
  $term_name = '';
  $terms = wp_get_post_terms( $post_id, 'delatnost' );
  if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
    $term_name = $terms[0]->name;
  }

  // Membership badge tier from the 'zvezdica' meta (same logic as clanice-grid)
  $zvezdica = strtolower( trim( (string) get_post_meta( $post_id, 'zvezdica', true ) ) );
  if ( in_array( $zvezdica, [ 'zlatna', 'gold' ], true ) ) {
    $badge = 'gold';
  } elseif ( in_array( $zvezdica, [ 'srebrna', 'silver' ], true ) ) {
    $badge = 'silver';
  } elseif ( 'starter' === $zvezdica ) {
    $badge = 'starter';
  } else {
    $badge = '';
  }

  // Expose variables to template
  $card = [
    'image_url' => $image_url,
    'name'      => $name,
    'category'  => $term_name,
    'badge'     => $badge,
  ];

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

// Register element with the loader
wpb_register_element([
  'base'   => $base,
  'map'    => $map,
  'render' => $render,
]);

// WPBakery autocomplete callbacks
add_filter( 'vc_autocomplete_' . $base . '_osnazena_id_callback', function( $search ) {
  $query = new WP_Query([
    'post_type'      => 'osnazena',
    's'              => sanitize_text_field( $search ),
    'posts_per_page' => 20,
    'post_status'    => 'publish',
  ]);
  $results = [];
  foreach ( $query->posts as $p ) {
    $results[] = [
      'value' => $p->ID,
      'label' => $p->post_title,
    ];
  }
  return $results;
}, 10, 1 );

add_filter( 'vc_autocomplete_' . $base . '_osnazena_id_render', function( $value ) {
  $post_id = 0;
  if ( is_array( $value ) && isset( $value['value'] ) ) {
    $post_id = (int) $value['value'];
  } elseif ( is_numeric( $value ) ) {
    $post_id = (int) $value;
  }
  if ( $post_id <= 0 ) return false;
  $p = get_post( $post_id );
  if ( ! $p ) return false;
  return [
    'value' => $p->ID,
    'label' => $p->post_title,
  ];
}, 10, 1 );
