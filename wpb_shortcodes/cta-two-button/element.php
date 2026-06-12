<?php
/**
 * CTA Two Button WPBakery element definition
 */

// Map (params shown in WPBakery editor)
$map = [
  'name'        => __( 'Two Button CTA', 'wpb-dev-example' ),
  'base'        => 'wpb_cta_two_button',
  'description' => __( 'CTA with heading, text and two buttons', 'wpb-dev-example' ),
  'category'    => __( 'Custom', 'wpb-dev-example' ),
  'params'      => [
    [
      'type'       => 'textfield',
      'heading'    => __( 'Heading 1', 'wpb-dev-example' ),
      'param_name' => 'heading1',
      'value'      => '',
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Heading 2', 'wpb-dev-example' ),
      'param_name' => 'heading2',
      'value'      => '',
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Heading 3', 'wpb-dev-example' ),
      'param_name' => 'heading3',
      'value'      => '',
    ],
    [
      'type'       => 'textarea_html',
      'heading'    => __( 'Text', 'wpb-dev-example' ),
      'param_name' => 'content',
      'value'      => '',
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Button 1 Text', 'wpb-dev-example' ),
      'param_name' => 'btn1_text',
      'value'      => '',
    ],
    [
      'type'       => 'vc_link',
      'heading'    => __( 'Button 1 Link', 'wpb-dev-example' ),
      'param_name' => 'btn1_link',
    ],
    [
      'type'       => 'textfield',
      'heading'    => __( 'Button 2 Text', 'wpb-dev-example' ),
      'param_name' => 'btn2_text',
      'value'      => '',
    ],
    [
      'type'       => 'vc_link',
      'heading'    => __( 'Button 2 Link', 'wpb-dev-example' ),
      'param_name' => 'btn2_link',
    ],
  ],
];

// Template relative path (inside child theme)
$template_rel = 'wpb_shortcodes/cta-two-button/templates/template.php';

// Shortcode render callback (loads the template)
$render = function( $atts, $content = '' ) use ( $template_rel ) {
  $atts = shortcode_atts([
    'heading1'   => '',
    'heading2'   => '',
    'heading3'   => '',
    'btn1_text'  => '',
    'btn1_link'  => '',
    'btn2_text'  => '',
    'btn2_link'  => '',
  ], $atts, 'wpb_cta_two_button');

  // Parse vc_link
  $btn1 = [ 'url' => '', 'title' => '', 'target' => '' ];
  $btn2 = [ 'url' => '', 'title' => '', 'target' => '' ];
  if ( function_exists( 'vc_build_link' ) ) {
    $btn1 = array_merge( $btn1, vc_build_link( $atts['btn1_link'] ) );
    $btn2 = array_merge( $btn2, vc_build_link( $atts['btn2_link'] ) );
  } else {
    $btn1['url'] = is_string( $atts['btn1_link'] ) ? $atts['btn1_link'] : '';
    $btn2['url'] = is_string( $atts['btn2_link'] ) ? $atts['btn2_link'] : '';
  }

  $btn1['title'] = is_string( $atts['btn1_text'] ) ? $atts['btn1_text'] : '';
  $btn2['title'] = is_string( $atts['btn2_text'] ) ? $atts['btn2_text'] : '';

  // Variables exposed to template
  $heading1 = $atts['heading1'];
  $heading2 = $atts['heading2'];
  $heading3 = $atts['heading3'];
  $text     = $content; // WPBakery passes textarea_html as $content

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

// Register element with the global loader
wpb_register_element([
  'base'   => 'wpb_cta_two_button',
  'map'    => $map,
  'render' => $render,
]);
