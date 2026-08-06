<?php
/**
 * Image Carousel WPBakery element definition
 *
 * Infinite-scroll image strip — images are selected from the media library and
 * scroll continuously left, wrapping seamlessly.
 */

$map = [
	'name'        => __( 'Image Carousel', 'dealsdot-child' ),
	'base'        => 'wpb_image_carousel',
	'description' => __( 'Infinite-scroll image strip from the media library', 'dealsdot-child' ),
	'category'    => __( 'Custom', 'dealsdot-child' ),
	'params'      => [
		[
			'type'        => 'attach_images',
			'heading'     => __( 'Images', 'dealsdot-child' ),
			'param_name'  => 'images',
			'description' => __( 'Select the images to display in the carousel. All images are scaled to the same height.', 'dealsdot-child' ),
		],
		[
			'type'        => 'textfield',
			'heading'     => __( 'Image height (px)', 'dealsdot-child' ),
			'param_name'  => 'height',
			'value'       => '125',
			'description' => __( 'Height in pixels for every image. Width scales proportionally.', 'dealsdot-child' ),
		],
		[
			'type'        => 'textfield',
			'heading'     => __( 'Mobile display image height (px)', 'dealsdot-child' ),
			'param_name'  => 'mobile_height',
			'value'       => '',
			'description' => __( 'Image height on screens narrower than 768px. Leave empty to use the regular height.', 'dealsdot-child' ),
		],
		[
			'type'        => 'textfield',
			'heading'     => __( 'Scroll speed (px/s)', 'dealsdot-child' ),
			'param_name'  => 'speed',
			'value'       => '80',
			'description' => __( 'How fast the images scroll, in pixels per second.', 'dealsdot-child' ),
		],
	],
];

$template_rel = 'wpb_shortcodes/image-carousel/templates/template.php';

$render = function( $atts, $content = '' ) use ( $template_rel ) {
	$atts = shortcode_atts(
		[
			'images'        => '',
			'height'        => '125',
			'mobile_height' => '',
			'speed'         => '80',
		],
		$atts
	);

	wp_enqueue_script(
		'osn-image-carousel',
		get_stylesheet_directory_uri() . '/wpb_shortcodes/image-carousel/assets/carousel.js',
		[],
		filemtime( get_stylesheet_directory() . '/wpb_shortcodes/image-carousel/assets/carousel.js' ),
		true
	);

	ob_start();
	$template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
	if ( file_exists( $template_abs ) ) {
		include $template_abs;
	}
	return ob_get_clean();
};

wpb_register_element( [
	'base'   => 'wpb_image_carousel',
	'map'    => $map,
	'render' => $render,
] );
