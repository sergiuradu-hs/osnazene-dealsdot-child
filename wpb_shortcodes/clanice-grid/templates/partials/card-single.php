<?php
/**
 * Partial: single member card
 * Expected variable: $post_obj (WP_Post)
 */
$post_id = $post_obj->ID;

// --- ACF fields -------------------------------------------------
$ime_raw    = get_post_meta( $post_id, 'ime_i_prezime_vlasnice', true );
$name       = ! empty( $ime_raw ) ? $ime_raw : get_the_title( $post_id );
$titula     = get_post_meta( $post_id, 'titula', true );
$zvezdica   = strtolower( trim( (string) get_post_meta( $post_id, 'zvezdica', true ) ) );

// --- Image ------------------------------------------------------
$img_id     = (int) get_post_meta( $post_id, 'naslovna_slika', true );
$img_url    = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
if ( ! $img_url ) {
    $img_url = get_the_post_thumbnail_url( $post_id, 'large' );
}

// --- Taxonomy tags (delatnost + drzava + mesto, max 4 combined) --
$tag_terms = [];
$del_terms = wp_get_post_terms( $post_id, 'delatnost' );
$drz_terms = wp_get_post_terms( $post_id, 'drzava' );
$mesto_taxonomies = [
    'mesto',
    'mesto-austrija',
    'mesto-bih',
    'mesto-crna-gora',
    'mesto-hrvatska',
    'mesto-madarska',
    'mesto-makedonija',
    'mesto-nemacka',
    'mesto-slovenija',
    'mesto-svajcarska',
    'mesto-usa-canada',
];
$mesto_terms = [];
foreach ( $mesto_taxonomies as $mesto_taxonomy ) {
    if ( ! taxonomy_exists( $mesto_taxonomy ) ) {
        continue;
    }

    $terms = wp_get_post_terms( $post_id, $mesto_taxonomy );
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $mesto_terms = array_merge( $mesto_terms, $terms );
    }
}
if ( ! is_wp_error( $del_terms ) ) {
    foreach ( $del_terms as $t ) {
        $tag_terms[] = $t->name;
    }
}
if ( ! is_wp_error( $drz_terms ) ) {
    foreach ( $drz_terms as $t ) {
        $tag_terms[] = $t->name;
    }
}
foreach ( $mesto_terms as $t ) {
    $tag_terms[] = $t->name;
}
$tag_terms = array_slice( array_unique( $tag_terms ), 0, 4 );

// --- Job title fallback -----------------------------------------
if ( empty( $titula ) && ! is_wp_error( $del_terms ) && ! empty( $del_terms ) ) {
    $titula = $del_terms[0]->name;
}

// --- Badge type --------------------------------------------------
$badge_class = '';
if ( $zvezdica === 'zlatna' || $zvezdica === 'gold' ) {
    $badge_class = 'osn-clanice-card__badge--gold';
} elseif ( $zvezdica === 'srebrna' || $zvezdica === 'silver' ) {
    $badge_class = 'osn-clanice-card__badge--silver';
}

// --- Post permalink ---------------------------------------------
$permalink = get_permalink( $post_id );
?>
<div class="osn-clanice-card">
    <a class="osn-clanice-card__inner" href="<?php echo esc_url( $permalink ); ?>">
        <div class="osn-clanice-card__image-wrap">
             <?php if ( $badge_class ) : ?>
                <span class="osn-clanice-card__badge osn-clanice-card__badge--mobile <?php echo esc_attr( $badge_class ); ?>" aria-hidden="true">
                    <svg viewBox="0 0 46 46" xmlns="http://www.w3.org/2000/svg">
                        <polygon class="osn-clanice-card__badge-ribbon" points="17.77,24.85 22.63,27.53 20,44 13,44"/>
                        <polygon class="osn-clanice-card__badge-ribbon" points="22.63,27.53 28.04,24.85 33,44 26,44"/>
                        <circle class="osn-clanice-card__badge-circle" cx="23" cy="18" r="13"/>
                        <text class="osn-clanice-card__badge-star" x="23" y="23" text-anchor="middle" font-size="14" font-family="serif">★</text>
                    </svg>
                </span>
            <?php endif; ?>
            <?php if ( $img_url ) : ?>
                <img class="osn-clanice-card__photo"
                     src="<?php echo esc_url( $img_url ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     loading="lazy" />
            <?php else : ?>
                <div class="osn-clanice-card__photo-placeholder"></div>
            <?php endif; ?>
        </div>
        <div class="osn-clanice-card__body">
            <div class="osn-clanice-card__info">
                <div class="osn-clanice-card__name"><?php echo esc_html( $name ); ?></div>
                <?php if ( ! empty( $titula ) ) : ?>
                    <div class="osn-clanice-card__titula"><?php echo esc_html( $titula ); ?></div>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $tag_terms ) ) : ?>
                <div class="osn-clanice-card__tags">
                    <?php foreach ( $tag_terms as $tag ) : ?>
                        <span class="osn-clanice-card__tag"><?php echo esc_html( $tag ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </a>
    <?php if ( $badge_class ) : ?>
        <span class="osn-clanice-card__badge <?php echo esc_attr( $badge_class ); ?>" aria-hidden="true">
            <svg viewBox="0 0 46 46" xmlns="http://www.w3.org/2000/svg">
                <polygon class="osn-clanice-card__badge-ribbon" points="17.77,24.85 22.63,27.53 20,44 13,44"/>
                <polygon class="osn-clanice-card__badge-ribbon" points="22.63,27.53 28.04,24.85 33,44 26,44"/>
                <circle class="osn-clanice-card__badge-circle" cx="23" cy="18" r="13"/>
                <text class="osn-clanice-card__badge-star" x="23" y="23" text-anchor="middle" font-size="14" font-family="serif">★</text>
            </svg>
        </span>
    <?php endif; ?>
</div>
