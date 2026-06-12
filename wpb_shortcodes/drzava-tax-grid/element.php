<?php
/**
 * Država Taxonomy Grid WPBakery element definition
 */

$map = [
  'name'        => __( 'Država Tax Grid', 'dealsdot-child' ),
  'base'        => 'wpb_tax_drzava_nav',
  'description' => __( 'Grid of taxonomy terms (drzava) with images/flags', 'dealsdot-child' ),
  'category'    => __( 'Custom', 'dealsdot-child' ),
  'params'      => [
    [
      'type'       => 'textfield',
      'heading'    => __( 'Taxonomy', 'dealsdot-child' ),
      'param_name' => 'taxonomy',
      'value'      => 'drzava',
      'description'=> __( 'Taxonomy slug to pull terms from', 'dealsdot-child' ),
    ],
    [
      'type'       => 'dropdown',
      'heading'    => __( 'Columns (desktop)', 'dealsdot-child' ),
      'param_name' => 'columns',
      'value'      => [ '5' => 5, '4' => 4, '3' => 3 ],
      'std'        => 5,
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Box size (px)', 'dealsdot-child' ),
      'param_name' => 'box_size',
      'value'      => '200',
    ],
    [
      'type'       => 'checkbox',
      'heading'    => __( 'Hide empty terms', 'dealsdot-child' ),
      'param_name' => 'hide_empty',
      'value'      => [ __( 'Yes', 'dealsdot-child' ) => 'true' ],
    ],
  ],
];

$template_rel = 'wpb_shortcodes/drzava-tax-grid/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {
  $atts = shortcode_atts([
    'taxonomy'   => 'drzava',
    'columns'    => 5,
    'box_size'   => 200,
    'hide_empty' => '',
  ], $atts, 'wpb_tax_drzava_nav');

  $taxonomy = sanitize_key( $atts['taxonomy'] );
  $columns  = max( 1, (int) $atts['columns'] );
  $box_size = max( 80, (int) $atts['box_size'] );
  $hide     = $atts['hide_empty'] === 'true';

  if ( ! taxonomy_exists( $taxonomy ) ) {
    return '';
  }

  $terms = get_terms([
    'taxonomy'   => $taxonomy,
    'hide_empty' => $hide,
  ]);

  if ( is_wp_error( $terms ) || empty( $terms ) ) {
    return '';
  }

  // Helper to fetch ACF term field (works with various return types)
  $get_term_field = function( $field, $term ) use ( $taxonomy ) {
    $val = null;
    if ( function_exists('get_field') ) {
      $val = get_field( $field, $taxonomy . '_' . $term->term_id );
      if ( empty( $val ) ) {
        $val = get_field( $field, $term );
      }
    }
    return $val;
  };

  // Normalize term data and sort by ACF 'order'
  $items = [];
  foreach ( $terms as $term ) {
    $order = (int) ( $get_term_field('order', $term) ?: 0 );

    // zastava
    $zastava_raw = $get_term_field('zastava', $term);
    $zastava_url = '';
    if ( is_array( $zastava_raw ) && ! empty( $zastava_raw['url'] ) ) {
      $zastava_url = $zastava_raw['url'];
    } elseif ( is_numeric( $zastava_raw ) ) {
      $zastava_url = wp_get_attachment_image_url( (int) $zastava_raw, 'thumbnail' );
    } elseif ( is_string( $zastava_raw ) ) {
      $zastava_url = $zastava_raw; // direct URL
    }

    // Image
    $img_raw = $get_term_field('slika_drzave', $term);
    $img_url = '';
    if ( is_array( $img_raw ) && ! empty( $img_raw['url'] ) ) {
      $img_url = $img_raw['url'];
    } elseif ( is_numeric( $img_raw ) ) {
      $img_url = wp_get_attachment_image_url( (int) $img_raw, 'large' );
    } elseif ( is_string( $img_raw ) ) {
      $img_url = $img_raw;
    }

    $items[] = [
      'id'       => $term->term_id,
      'name'     => $term->name,
      'link'     => get_term_link( $term ),
      'order'    => $order,
      'zastava_url' => $zastava_url,
      'img_url'  => $img_url,
    ];
  }

  // Sort by 'order' ASC, then name ASC
  usort( $items, function( $a, $b ) {
    if ( $a['order'] === $b['order'] ) {
      return strcmp( $a['name'], $b['name'] );
    }
    return $a['order'] <=> $b['order'];
  });

  // Expose variables to template
  $grid = [
    'columns'  => $columns,
    'box_size' => $box_size,
    'items'    => $items,
  ];

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

wpb_register_element([
  'base'   => 'wpb_tax_drzava_nav',
  'map'    => $map,
  'render' => $render,
]);
