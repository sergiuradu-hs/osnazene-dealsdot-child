<?php
/**
 * Hero Section Trophy Badges WPBakery element definition.
 */

$base = 'wpb_hero_trophy_badges';

$badge_params = static function ( int $index, string $label, array $defaults ): array {
    return [
        [
            'type'        => 'attach_image',
            'heading'     => sprintf( __( '%s Icon', 'dealsdot-child' ), $label ),
            'param_name'  => "badge_{$index}_icon_id",
            'description' => __( 'Select the trophy icon image.', 'dealsdot-child' ),
        ],
        [
            'type'       => 'textfield',
            'heading'    => sprintf( __( '%s Winner Label', 'dealsdot-child' ), $label ),
            'param_name' => "badge_{$index}_winner_label",
            'value'      => $defaults['winner_label'],
        ],
        [
            'type'       => 'textfield',
            'heading'    => sprintf( __( '%s Top Label', 'dealsdot-child' ), $label ),
            'param_name' => "badge_{$index}_top_label",
            'value'      => $defaults['top_label'],
        ],
        [
            'type'       => 'textfield',
            'heading'    => sprintf( __( '%s Large Word', 'dealsdot-child' ), $label ),
            'param_name' => "badge_{$index}_large_word",
            'value'      => $defaults['large_word'],
        ],
        [
            'type'       => 'textfield',
            'heading'    => sprintf( __( '%s Bottom Label', 'dealsdot-child' ), $label ),
            'param_name' => "badge_{$index}_bottom_label",
            'value'      => $defaults['bottom_label'],
        ],
        [
            'type'       => 'textfield',
            'heading'    => sprintf( __( '%s Year', 'dealsdot-child' ), $label ),
            'param_name' => "badge_{$index}_year",
            'value'      => $defaults['year'],
        ],
    ];
};

$badge_1_defaults = [
    'winner_label' => 'Winner',
    'top_label'    => 'Regional Butterfly',
    'large_word'   => 'Innovation',
    'bottom_label' => 'Award',
    'year'         => '2025',
];

$badge_2_defaults = [
    'winner_label' => 'Winner',
    'top_label'    => 'European Enterprise',
    'large_word'   => 'Promotion',
    'bottom_label' => 'Award',
    'year'         => '2024',
];

$map = [
    'name'        => __( 'Hero Section Trophy Badges', 'dealsdot-child' ),
    'base'        => $base,
    'description' => __( 'Two trophy award badges for the hero section.', 'dealsdot-child' ),
    'category'    => __( 'Custom', 'dealsdot-child' ),
    'params'      => array_merge(
        $badge_params( 1, __( 'Badge 1', 'dealsdot-child' ), $badge_1_defaults ),
        $badge_params( 2, __( 'Badge 2', 'dealsdot-child' ), $badge_2_defaults )
    ),
];

$render_badge = static function ( array $badge ): string {
    $icon_html = '';
    if ( ! empty( $badge['icon_id'] ) ) {
        $icon_html = wp_get_attachment_image( absint( $badge['icon_id'] ), 'thumbnail', false, [
            'class'   => 'osn-hero-trophy-badges__icon',
            'loading' => 'lazy',
        ] );
    }

    return sprintf(
        '<div class="osn-hero-trophy-badges__badge"><div class="osn-hero-trophy-badges__media">%1$s<div class="osn-hero-trophy-badges__winner">%2$s</div></div><div class="osn-hero-trophy-badges__content"><div class="osn-hero-trophy-badges__top">%3$s</div><div class="osn-hero-trophy-badges__large">%4$s</div><div class="osn-hero-trophy-badges__bottom"><span>%5$s</span><span>%6$s</span></div></div></div>',
        $icon_html,
        esc_html( $badge['winner_label'] ),
        esc_html( $badge['top_label'] ),
        esc_html( $badge['large_word'] ),
        esc_html( $badge['bottom_label'] ),
        esc_html( $badge['year'] )
    );
};

$render = static function ( $atts ) use ( $base, $badge_1_defaults, $badge_2_defaults, $render_badge ): string {
    $atts = shortcode_atts( [
        'badge_1_icon_id'      => '',
        'badge_1_winner_label' => $badge_1_defaults['winner_label'],
        'badge_1_top_label'    => $badge_1_defaults['top_label'],
        'badge_1_large_word'   => $badge_1_defaults['large_word'],
        'badge_1_bottom_label' => $badge_1_defaults['bottom_label'],
        'badge_1_year'         => $badge_1_defaults['year'],
        'badge_2_icon_id'      => '',
        'badge_2_winner_label' => $badge_2_defaults['winner_label'],
        'badge_2_top_label'    => $badge_2_defaults['top_label'],
        'badge_2_large_word'   => $badge_2_defaults['large_word'],
        'badge_2_bottom_label' => $badge_2_defaults['bottom_label'],
        'badge_2_year'         => $badge_2_defaults['year'],
    ], $atts, $base );

    $badges = [
        [
            'icon_id'      => $atts['badge_1_icon_id'],
            'winner_label' => sanitize_text_field( $atts['badge_1_winner_label'] ),
            'top_label'    => sanitize_text_field( $atts['badge_1_top_label'] ),
            'large_word'   => sanitize_text_field( $atts['badge_1_large_word'] ),
            'bottom_label' => sanitize_text_field( $atts['badge_1_bottom_label'] ),
            'year'         => sanitize_text_field( $atts['badge_1_year'] ),
        ],
        [
            'icon_id'      => $atts['badge_2_icon_id'],
            'winner_label' => sanitize_text_field( $atts['badge_2_winner_label'] ),
            'top_label'    => sanitize_text_field( $atts['badge_2_top_label'] ),
            'large_word'   => sanitize_text_field( $atts['badge_2_large_word'] ),
            'bottom_label' => sanitize_text_field( $atts['badge_2_bottom_label'] ),
            'year'         => sanitize_text_field( $atts['badge_2_year'] ),
        ],
    ];

    return '<div class="osn-hero-trophy-badges">' . implode( '', array_map( $render_badge, $badges ) ) . '</div>';
};

wpb_register_element( [
    'base'   => $base,
    'map'    => $map,
    'render' => $render,
] );
