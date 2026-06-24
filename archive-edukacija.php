<?php
/**
 * Archive template for the `edukacija` custom post type.
 *
 * Layout (3 blocks, dark background):
 *   1. EDUKACIJE       – latest 3 posts, featured card (no date / no sponsor)
 *   2. Besplatne       – posts in vrsta-edukacije / besplatne-edukacije, 6 initial + load-more
 *   3. Komercijalne    – posts in vrsta-edukacije / komercijalne + kotizacija terms, same
 */

if ( ! function_exists( 'osn_edu_render_card' ) ) :
    /**
     * Render a single edukacija card.
     *
     * @param int  $post_id     Post ID.
     * @param bool $is_featured Show as featured card (no date/sponsor, optional subtitle).
     */
    function osn_edu_render_card( int $post_id, bool $is_featured = false ): void {
        $title      = get_the_title( $post_id );
        $img_url    = get_the_post_thumbnail_url( $post_id, 'large' );
        $post_link  = get_permalink( $post_id );
        $datum_ts   = (int) get_post_meta( $post_id, 'datum', true );
        $datum_str  = $datum_ts ? date_i18n( 'd.m.Y.', $datum_ts ) : '';
        $format     = get_post_meta( $post_id, 'format_edukacije', true );
        $sponsor_id = get_post_meta( $post_id, 'logo-sponzora', true );
        $excerpt    = get_post_field( 'post_excerpt', $post_id );
        $card_class = 'osn-edu-card' . ( $is_featured ? ' osn-edu-card--featured' : '' );
        ?>
        <a href="<?php echo esc_url( $post_link ); ?>" class="<?php echo esc_attr( $card_class ); ?>">
            <?php if ( $img_url ) : ?>
            <div class="osn-edu-card__img-wrap">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
            </div>
            <?php endif; ?>
            <div class="osn-edu-card__body">
                <?php if ( ! $is_featured && ( $datum_str || $format ) ) : ?>
                <div class="osn-edu-card__meta">
                    <?php echo $datum_str ? '<span class="osn-edu-card__date">' . esc_html( $datum_str ) . '</span>' : ''; ?>
                    <?php echo $format ? '<span class="osn-edu-card__format">' . esc_html( $format ) . '</span>' : ''; ?>
                </div>
                <?php endif; ?>
                <h3 class="osn-edu-card__title"><?php echo esc_html( $title ); ?></h3>
                <?php if ( $is_featured && $excerpt ) : ?>
                <p class="osn-edu-card__subtitle"><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>
                <?php if ( ! $is_featured && $sponsor_id ) :
                    $sponsor_img = wp_get_attachment_image( $sponsor_id, [137, 50], false, [
                        'class'   => 'osn-edu-card__sponsor-img',
                        'loading' => 'lazy',
                    ] );
                    if ( $sponsor_img ) : ?>
                <div class="osn-edu-card__sponsor">
                    <span class="osn-edu-card__sponsor-label"><?php esc_html_e( 'Sponzor edukacije', 'dealsdot-child' ); ?></span>
                    <?php echo $sponsor_img; ?>
                </div>
                    <?php endif;
                endif; ?>
            </div>
        </a>
        <?php
    }
endif;

get_header();

/* -----------------------------------------------------------------------
 * Shared WP_Query args: exclude posts hidden from category listing.
 * ----------------------------------------------------------------------- */
$base_args = [
    'post_type'   => 'edukacija',
    'post_status' => 'publish',
    'orderby'     => 'meta_value_num',
    'meta_key'    => 'datum',
    'order'       => 'DESC',
    'meta_query'  => [
        'relation' => 'OR',
        [
            'key'     => 'sakri-iz-kategorije',
            'value'   => 'Da',
            'compare' => '!=',
        ],
        [
            'key'     => 'sakri-iz-kategorije',
            'compare' => 'NOT EXISTS',
        ],
    ],
];

/* -----------------------------------------------------------------------
 * Block 1 – EDUKACIJE (latest 3, featured card style)
 * ----------------------------------------------------------------------- */
$q_featured = new WP_Query( array_merge( $base_args, [ 'posts_per_page' => 3 ] ) );

/* -----------------------------------------------------------------------
 * Block 2 – Besplatne edukacije
 * ----------------------------------------------------------------------- */
$q_besplatne = new WP_Query( array_merge( $base_args, [
    'posts_per_page' => 6,
    'tax_query'      => [ [
        'taxonomy' => 'vrsta-edukacije',
        'field'    => 'slug',
        'terms'    => 'besplatne-edukacije',
    ] ],
] ) );

/* -----------------------------------------------------------------------
 * Block 3 – Komercijalne edukacije (includes "sa kotizacijom" / "placene")
 * ----------------------------------------------------------------------- */
$q_komercijalne = new WP_Query( array_merge( $base_args, [
    'posts_per_page' => 6,
    'tax_query'      => [ [
        'taxonomy' => 'vrsta-edukacije',
        'field'    => 'slug',
        'terms'    => [ 'komercijalne-edukacije', 'edukacije-sa-kotizacijom', 'placene-edukacije' ],
        'operator' => 'IN',
    ] ],
] ) );
?>

<section class="vc_section osnazene_section dark osn-edukacija-archive">
    <div class="container">
        <?php /* ============================================================
            * Block 1 – EDUKACIJE (featured)
            * ============================================================ */
        if ( $q_featured->have_posts() ) : ?>
        <div class="osn-edu-block">
            <h2 class="osn-edu-heading">EDUKACIJE</h2>
            <div class="osn-edu-grid osn-edu-grid--featured">
                <?php while ( $q_featured->have_posts() ) :
                    $q_featured->the_post();
                    osn_edu_render_card( get_the_ID(), true );
                endwhile;
                wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php /* ============================================================
            * Block 2 – Besplatne edukacije
            * ============================================================ */
        if ( $q_besplatne->have_posts() ) :
            $besplatne_term = get_term_by( 'slug', 'besplatne-edukacije', 'vrsta-edukacije' );
        ?>
        <div class="osn-edu-block">
            <h2 class="osn-edu-heading">Besplatne edukacije</h2>
            <div class="osn-edu-grid">
                <?php while ( $q_besplatne->have_posts() ) :
                    $q_besplatne->the_post();
                    osn_edu_render_card( get_the_ID(), false );
                endwhile;
                wp_reset_postdata(); ?>
            </div>
            <?php if ( $q_besplatne->found_posts > 6 && $besplatne_term && ! is_wp_error( $besplatne_term ) ) : ?>
            <div class="osn-edu-loadmore">
                <a href="<?php echo esc_url( get_term_link( $besplatne_term ) ); ?>" class="osn-edu-btn">Prikaži još</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php /* ============================================================
            * Block 3 – Komercijalne edukacije
            * ============================================================ */
        if ( $q_komercijalne->have_posts() ) :
            $kom_term = get_term_by( 'slug', 'komercijalne-edukacije', 'vrsta-edukacije' )
                    ?: get_term_by( 'slug', 'edukacije-sa-kotizacijom', 'vrsta-edukacije' );
        ?>
        <div class="osn-edu-block">
            <h2 class="osn-edu-heading">Komercijalne edukacije</h2>
            <div class="osn-edu-grid">
                <?php while ( $q_komercijalne->have_posts() ) :
                    $q_komercijalne->the_post();
                    osn_edu_render_card( get_the_ID(), false );
                endwhile;
                wp_reset_postdata(); ?>
            </div>
            <?php if ( $q_komercijalne->found_posts > 6 && $kom_term && ! is_wp_error( $kom_term ) ) : ?>
            <div class="osn-edu-loadmore">
                <a href="<?php echo esc_url( get_term_link( $kom_term ) ); ?>" class="osn-edu-btn">Prikaži još</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
