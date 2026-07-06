<?php
/**
 * Image Overlay Card WPBakery element definition.
 *
 * Renders an image with a translucent bottom label, matching the Figma card.
 */

$base = 'wpb_image_overlay_card';

$map = [
    'name'        => __( 'Image Overlay Card', 'dealsdot-child' ),
    'base'        => $base,
    'description' => __( 'Image with bottom overlay text', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [
        [
            'type'        => 'attach_image',
            'heading'     => __( 'Image', 'dealsdot-child' ),
            'param_name'  => 'image_id',
            'description' => __( 'Select the image to display.', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Image size', 'dealsdot-child' ),
            'param_name'  => 'image_size',
            'value'       => 'full',
            'description' => __( 'Enter image size, e.g. thumbnail, medium, large, full, or 200x100.', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Overlay text', 'dealsdot-child' ),
            'param_name'  => 'overlay_text',
            'value'       => '',
            'description' => __( 'Text displayed at the bottom of the image.', 'dealsdot-child' ),
        ],
    ],
];

$render = function( $atts ) use ( $base ) {
    $atts = shortcode_atts( [
        'image_id'     => '',
        'image_size'   => 'full',
        'overlay_text' => '',
    ], $atts, $base );

    $image_id = absint( $atts['image_id'] );
    if ( ! $image_id ) {
        return '';
    }

    $image_size = sanitize_text_field( $atts['image_size'] );
    $image_size = ! empty( $image_size ) ? $image_size : 'full';
    $image_size_value = preg_match( '/^(\d+)x(\d+)$/', $image_size, $matches )
        ? [ absint( $matches[1] ), absint( $matches[2] ) ]
        : $image_size;

    $image_html = wp_get_attachment_image( $image_id, $image_size_value, false, [
        'class'   => 'osn-image-overlay-card__image',
        'loading' => 'lazy',
    ] );
    if ( empty( $image_html ) ) {
        return '';
    }

    $caption_html = '';
    if ( ! empty( $atts['overlay_text'] ) ) {
        $caption_html = sprintf(
            '<div class="osn-image-overlay-card__caption"><span class="osn-image-overlay-card__text">%s</span></div>',
            esc_html( sanitize_text_field( $atts['overlay_text'] ) )
        );
    }

    return '<div class="osn-image-overlay-card">' . $image_html . $caption_html . '</div>';
};

wpb_register_element( [
    'base'   => $base,
    'map'    => $map,
    'render' => $render,
] );
