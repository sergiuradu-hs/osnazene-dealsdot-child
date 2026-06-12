<?php
/**
 * Newsletter Form WPBakery element definition
 *
 * MailerLite newsletter form rendered as a reusable widget.
 */

$map = [
  'name'        => __( 'Newsletter Form', 'dealsdot-child' ),
  'base'        => 'wpb_newsletter_form',
  'description' => __( 'MailerLite newsletter signup form', 'dealsdot-child' ),
  'category'    => __( 'Custom', 'dealsdot-child' ),
  'params'      => [],
];

$template_rel = 'wpb_shortcodes/newsletter-form/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {
  $instance_id = 'mlb2-5762378-' . wp_generate_uuid4();

  // Keep MailerLite webforms script loaded through WP when possible.
  wp_enqueue_script(
    'osn-mailerlite-webforms',
    'https://groot.mailerlite.com/js/w/webforms.min.js?vc2affd81117220f6978e779b988d5128',
    [],
    null,
    true
  );

  ob_start();
  $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
  if ( file_exists( $template_abs ) ) {
    include $template_abs;
  }
  return ob_get_clean();
};

wpb_register_element([
  'base'   => 'wpb_newsletter_form',
  'map'    => $map,
  'render' => $render,
]);
