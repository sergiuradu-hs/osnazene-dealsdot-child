<?php
/**
 * Statistics Box WPBakery element definition.
 */

$base = 'wpb_statistics_box';

$map = [
    'name'        => __( 'Statistics Box', 'dealsdot-child' ),
    'base'        => $base,
    'description' => __( 'A centered statistic heading and label in a bordered box.', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [
        [
            'type'        => 'textfield',
            'heading'     => __( 'Statistic Heading', 'dealsdot-child' ),
            'param_name'  => 'heading',
            'value'       => '12+',
            'description' => __( 'Example: 12+ or 1590+', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Statistic Label', 'dealsdot-child' ),
            'param_name'  => 'text',
            'value'       => 'Zemalja',
            'description' => __( 'The text displayed beneath the statistic heading.', 'dealsdot-child' ),
        ],
    ],
];

$render = static function ( $atts ) use ( $base ): string {
    $atts = shortcode_atts( [
        'heading' => '12+',
        'text'    => 'Zemalja',
    ], $atts, $base );

    $heading = sanitize_text_field( $atts['heading'] );
    $text    = sanitize_text_field( $atts['text'] );

    ob_start();
    $template = trailingslashit( get_stylesheet_directory() ) . 'wpb_shortcodes/statistics-box/templates/template.php';

    if ( file_exists( $template ) ) {
        include $template;
    }

    return ob_get_clean();
};

wpb_register_element( [
    'base'   => $base,
    'map'    => $map,
    'render' => $render,
] );
