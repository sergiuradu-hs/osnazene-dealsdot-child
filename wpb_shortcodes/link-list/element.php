<?php
/**
 * Link List WPBakery element definition
 *
 * Displays a vertical list of labelled links, each prefixed with a chain-link icon.
 *
 * Uses WPBakery param_group to allow the editor to add/remove/reorder rows.
 * Each row has:
 *   label – the visible link text
 *   url   – the href (vc_link for target/rel support)
 */

// ---------- WPBakery map ----------------------------------------

$map = [
    'name'        => __( 'Link List', 'dealsdot-child' ),
    'base'        => 'wpb_link_list',
    'description' => __( 'Vertical list of labelled links with icon', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => [
        [
            'type'        => 'param_group',
            'heading'     => __( 'Links', 'dealsdot-child' ),
            'param_name'  => 'links',
            'description' => __( 'Add one row per link.', 'dealsdot-child' ),
            'value'       => '',
            'params'      => [
                [
                    'type'        => 'textfield',
                    'heading'     => __( 'Label', 'dealsdot-child' ),
                    'param_name'  => 'label',
                    'value'       => '',
                    'description' => __( 'Visible link text.', 'dealsdot-child' ),
                    'admin_label' => true,
                ],
                [
                    'type'        => 'vc_link',
                    'heading'     => __( 'URL', 'dealsdot-child' ),
                    'param_name'  => 'url',
                    'description' => __( 'Link destination (supports target).', 'dealsdot-child' ),
                ],
            ],
        ],
    ],
];

$template_rel = 'wpb_shortcodes/link-list/templates/template.php';

// ---------- Render callback -------------------------------------

$render = function( $atts, $content = '' ) use ( $template_rel ) {
    $atts = shortcode_atts( [
        'links' => '',
    ], $atts, 'wpb_link_list' );

    // Decode param_group value → array of rows
    $rows = [];
    if ( ! empty( $atts['links'] ) && function_exists( 'vc_param_group_parse_atts' ) ) {
        $rows = vc_param_group_parse_atts( $atts['links'] );
    }

    // Normalise each row
    $items = [];
    foreach ( $rows as $row ) {
        $label = isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '';
        $raw   = isset( $row['url'] )   ? $row['url']                          : '';

        $parsed = [ 'url' => '', 'target' => '' ];
        if ( function_exists( 'vc_build_link' ) && ! empty( $raw ) ) {
            $parsed = array_merge( $parsed, vc_build_link( $raw ) );
        } elseif ( is_string( $raw ) ) {
            $parsed['url'] = $raw;
        }

        if ( $label === '' && $parsed['url'] === '' ) {
            continue; // skip empty rows
        }

        $items[] = [
            'label'  => $label,
            'url'    => $parsed['url'],
            'target' => ! empty( $parsed['target'] ) ? $parsed['target'] : '',
        ];
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
    'base'   => 'wpb_link_list',
    'map'    => $map,
    'render' => $render,
] );
