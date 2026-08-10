<?php
/**
 * Package Card WPBakery element
 *
 * Renders a single pricing-package card matching the Figma design.
 * Drop multiple instances inside a vc_row to create a side-by-side grid.
 *
 * Editable fields:
 *   - Package name, subtitle, price
 *   - Header background color
 *   - Three payment-detail lines
 *   - Optional intro text (shown above bullets)
 *   - Bullet points (param_group – add/remove/reorder)
 *   - Number of CTA buttons (1 or 2)
 *   - Per-button: text, link
 *   - Optional footer text (shown below buttons)
 */

// ---------- WPBakery map ----------------------------------------

$map = [
    'name'        => __( 'Package Card', 'dealsdot-child' ),
    'base'        => 'wpb_package_card',
    'description' => __( 'Pricing package card (Figma design)', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'icon'        => 'vc_general vc_element-icon icon-wpb-accordion-slide',
    'params'      => [

        /* ── Header ──────────────────────────────────────────── */
        [
            'type'        => 'textfield',
            'heading'     => __( 'Package name', 'dealsdot-child' ),
            'param_name'  => 'package_name',
            'value'       => __( 'Silver paket', 'dealsdot-child' ),
            'admin_label' => true,
            'description' => __( 'Large heading inside the card header.', 'dealsdot-child' ),
            'group'       => __( 'Header', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Subtitle', 'dealsdot-child' ),
            'param_name'  => 'package_subtitle',
            'value'       => __( '(godišnja članarina)', 'dealsdot-child' ),
            'description' => __( 'Smaller text below the package name.', 'dealsdot-child' ),
            'group'       => __( 'Header', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Price', 'dealsdot-child' ),
            'param_name'  => 'package_price',
            'value'       => __( '12.000 din', 'dealsdot-child' ),
            'description' => __( 'Price displayed below the subtitle.', 'dealsdot-child' ),
            'group'       => __( 'Header', 'dealsdot-child' ),
        ],
        [
            'type'        => 'colorpicker',
            'heading'     => __( 'Header background color', 'dealsdot-child' ),
            'param_name'  => 'header_bg',
            'value'       => '#E1CBA6',
            'description' => __( 'Background color of the header block. Figma values: #F3EDD9 (Starter), #E1CBA6 (Silver), #C6B08A (Gold).', 'dealsdot-child' ),
            'group'       => __( 'Header', 'dealsdot-child' ),
        ],

        /* ── Payment details ─────────────────────────────────── */
        [
            'type'        => 'textfield',
            'heading'     => __( 'Monthly payment text', 'dealsdot-child' ),
            'param_name'  => 'payment_monthly',
            'value'       => __( '1.000 dinara mesečno', 'dealsdot-child' ),
            'description' => __( 'Displayed next to the calendar icon.', 'dealsdot-child' ),
            'group'       => __( 'Payment details', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Payment method text', 'dealsdot-child' ),
            'param_name'  => 'payment_method',
            'value'       => __( 'Jednokratno ili u 3 rate', 'dealsdot-child' ),
            'description' => __( 'Displayed next to the payment-card icon.', 'dealsdot-child' ),
            'group'       => __( 'Payment details', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Membership duration text', 'dealsdot-child' ),
            'param_name'  => 'payment_duration',
            'value'       => __( '12 meseci od evidentirane prve uplate', 'dealsdot-child' ),
            'description' => __( 'Displayed next to the clock icon.', 'dealsdot-child' ),
            'group'       => __( 'Payment details', 'dealsdot-child' ),
        ],

        /* ── Bullets ─────────────────────────────────────────── */
        [
            'type'        => 'textfield',
            'heading'     => __( 'Intro text (optional)', 'dealsdot-child' ),
            'param_name'  => 'intro_text',
            'value'       => '',
            'description' => __( 'Short line shown above the bullet list, e.g. "Plaćanje odjednom ili na 3 rate". Leave blank to hide.', 'dealsdot-child' ),
            'group'       => __( 'Bullets', 'dealsdot-child' ),
        ],
        [
            'type'        => 'param_group',
            'heading'     => __( 'Bullet points', 'dealsdot-child' ),
            'param_name'  => 'bullets',
            'description' => __( 'Add one row per bullet. HTML is allowed (e.g. <br/> for line breaks).', 'dealsdot-child' ),
            'value'       => '',
            'group'       => __( 'Bullets', 'dealsdot-child' ),
            'params'      => [
                [
                    'type'        => 'textarea',
                    'heading'     => __( 'Bullet text', 'dealsdot-child' ),
                    'param_name'  => 'bullet_text',
                    'value'       => '',
                    'admin_label' => true,
                    'description' => __( 'Text for this bullet. You may use basic HTML.', 'dealsdot-child' ),
                ],
            ],
        ],

        /* ── Buttons ─────────────────────────────────────────── */
        [
            'type'        => 'dropdown',
            'heading'     => __( 'Number of buttons', 'dealsdot-child' ),
            'param_name'  => 'button_count',
            'value'       => [
                __( '2 buttons (Rate + Godišnje)', 'dealsdot-child' ) => '2',
                __( '1 button', 'dealsdot-child' )                    => '1',
            ],
            'std'         => '2',
            'description' => __( 'Show one or two CTA buttons at the bottom of the card.', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Button 1 text', 'dealsdot-child' ),
            'param_name'  => 'btn1_text',
            'value'       => __( 'Plati u 3 rate', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
        ],
        [
            'type'        => 'vc_link',
            'heading'     => __( 'Button 1 link', 'dealsdot-child' ),
            'param_name'  => 'btn1_link',
            'description' => __( 'Destination URL, target, and title.', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Button 2 text', 'dealsdot-child' ),
            'param_name'  => 'btn2_text',
            'value'       => __( 'Plati godišnje', 'dealsdot-child' ),
            'description' => __( 'Only shown when "2 buttons" is selected above.', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
            'dependency'  => [ 'element' => 'button_count', 'value' => [ '2' ] ],
        ],
        [
            'type'        => 'vc_link',
            'heading'     => __( 'Button 2 link', 'dealsdot-child' ),
            'param_name'  => 'btn2_link',
            'description' => __( 'Only shown when "2 buttons" is selected above.', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
            'dependency'  => [ 'element' => 'button_count', 'value' => [ '2' ] ],
        ],
        [
            'type'        => 'textfield',
            'heading'     => __( 'Footer text (optional)', 'dealsdot-child' ),
            'param_name'  => 'footer_text',
            'value'       => '',
            'description' => __( 'Small line shown below the buttons, e.g. "Trajanje članstva: 365 dana". Leave blank to hide.', 'dealsdot-child' ),
            'group'       => __( 'Buttons', 'dealsdot-child' ),
        ],
    ],
];

$template_rel = 'wpb_shortcodes/package-card/templates/template.php';

// ---------- Render callback -------------------------------------

$render = function( $atts ) use ( $template_rel ) {
    $atts = shortcode_atts( [
        'package_name'    => 'Silver paket',
        'package_subtitle'=> '(godišnja članarina)',
        'package_price'   => '12.000 din',
        'header_bg'       => '#E1CBA6',
        'payment_monthly' => '1.000 dinara mesečno',
        'payment_method'  => 'Jednokratno ili u 3 rate',
        'payment_duration'=> '12 meseci od evidentirane prve uplate',
        'intro_text'      => '',
        'bullets'         => '',
        'button_count'    => '2',
        'btn1_text'       => 'Plati u 3 rate',
        'btn1_link'       => '',
        'btn2_text'       => 'Plati godišnje',
        'btn2_link'       => '',
        'footer_text'     => '',
    ], $atts, 'wpb_package_card' );

    // Sanitize scalar fields
    $package_name     = sanitize_text_field( $atts['package_name'] );
    $package_subtitle = sanitize_text_field( $atts['package_subtitle'] );
    $package_price    = sanitize_text_field( $atts['package_price'] );
    $header_bg        = sanitize_hex_color( $atts['header_bg'] ) ?: '#E1CBA6';
    $payment_monthly  = sanitize_text_field( $atts['payment_monthly'] );
    $payment_method   = sanitize_text_field( $atts['payment_method'] );
    $payment_duration = sanitize_text_field( $atts['payment_duration'] );
    $intro_text       = sanitize_text_field( $atts['intro_text'] );
    $button_count     = in_array( $atts['button_count'], [ '1', '2' ], true ) ? (int) $atts['button_count'] : 2;
    $footer_text      = sanitize_text_field( $atts['footer_text'] );

    // Parse bullet param_group
    $bullet_items = [];
    if ( ! empty( $atts['bullets'] ) && function_exists( 'vc_param_group_parse_atts' ) ) {
        $rows = vc_param_group_parse_atts( $atts['bullets'] );
        foreach ( $rows as $row ) {
            $text = isset( $row['bullet_text'] ) ? trim( $row['bullet_text'] ) : '';
            if ( $text !== '' ) {
                // Allow limited HTML in bullet text (line breaks, bold)
                $bullet_items[] = wp_kses( $text, [
                    'br'     => [],
                    'strong' => [],
                    'b'      => [],
                    'em'     => [],
                    'i'      => [],
                ] );
            }
        }
    }

    // Parse button links via vc_build_link
    $btn1 = [ 'url' => '#', 'target' => '', 'title' => '' ];
    $btn2 = [ 'url' => '#', 'target' => '', 'title' => '' ];
    if ( function_exists( 'vc_build_link' ) ) {
        if ( ! empty( $atts['btn1_link'] ) ) {
            $btn1 = array_merge( $btn1, vc_build_link( $atts['btn1_link'] ) );
        }
        if ( ! empty( $atts['btn2_link'] ) ) {
            $btn2 = array_merge( $btn2, vc_build_link( $atts['btn2_link'] ) );
        }
    }
    $btn1_text = sanitize_text_field( $atts['btn1_text'] );
    $btn2_text = sanitize_text_field( $atts['btn2_text'] );

    ob_start();
    $template_abs = trailingslashit( get_stylesheet_directory() ) . $template_rel;
    if ( file_exists( $template_abs ) ) {
        include $template_abs;
    }
    return ob_get_clean();
};

// ---------- Registration ----------------------------------------

wpb_register_element( [
    'base'   => 'wpb_package_card',
    'map'    => $map,
    'render' => $render,
] );
