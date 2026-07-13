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
} elseif ( $zvezdica === 'starter' ) {
    $badge_class = 'osn-clanice-card__badge--gray';
}

// --- Post permalink ---------------------------------------------
$permalink = get_permalink( $post_id );
?>
<div class="osn-clanice-card">
    <a class="osn-clanice-card__inner" href="<?php echo esc_url( $permalink ); ?>">
        <div class="osn-clanice-card__image-wrap">
             <?php if ( $badge_class ) : ?>
                <span class="osn-clanice-card__badge osn-clanice-card__badge--mobile <?php echo esc_attr( $badge_class ); ?> " aria-hidden="true">
                    <?php if ( $zvezdica === 'gold' || $zvezdica === 'zlatna' ) : ?>
                        <svg width="33" height="43" viewBox="0 0 33 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.2872 26.0214L11.1172 41.2928L8.70424 38.5632L8.52559 38.3614L8.2587 38.399L5.19791 38.837L11.3456 23.6209L17.2872 26.0214Z" fill="#CEA257" stroke="#B88E25"/>
                            <path d="M22.5212 23.5249L28.6912 38.7963L25.0594 38.5089L24.7907 38.4879L24.6248 38.7003L22.7273 41.1416L16.5796 25.9255L22.5212 23.5249Z" fill="#CEA257" stroke="#B88E25"/>
                            <path d="M15.5654 2.36816C15.9205 1.85748 16.6762 1.85748 17.0312 2.36816C17.7891 3.45789 19.2892 3.72199 20.374 2.95703C20.8823 2.59858 21.592 2.85766 21.751 3.45898C22.0904 4.74225 23.4104 5.50405 24.6914 5.15625C25.2916 4.9933 25.8705 5.47838 25.8145 6.09766C25.6943 7.41975 26.6732 8.5869 27.9961 8.69824C28.6159 8.75039 28.9933 9.40494 28.7285 9.96777C28.1637 11.1689 28.685 12.6001 29.8896 13.1572C30.4541 13.4182 30.5855 14.1622 30.1445 14.6006C29.2027 15.5362 29.2027 17.0605 30.1445 17.9961C30.5855 18.4345 30.4541 19.1785 29.8896 19.4395C28.685 19.9966 28.1637 21.4278 28.7285 22.6289C28.9933 23.1917 28.6159 23.8463 27.9961 23.8984C26.6732 24.0098 25.6943 25.1769 25.8145 26.499C25.8705 27.1183 25.2916 27.6034 24.6914 27.4404C23.4104 27.0926 22.0904 27.8544 21.751 29.1377C21.592 29.739 20.8823 29.9981 20.374 29.6396C19.2892 28.8747 17.7891 29.1388 17.0312 30.2285C16.6762 30.7392 15.9205 30.7392 15.5654 30.2285C14.8075 29.1388 13.3075 28.8747 12.2227 29.6396C11.7143 29.9981 11.0047 29.739 10.8457 29.1377C10.5063 27.8544 9.18633 27.0926 7.90527 27.4404C7.30513 27.6034 6.72619 27.1183 6.78223 26.499C6.90233 25.1769 5.92343 24.0098 4.60059 23.8984C3.98079 23.8463 3.60343 23.1917 3.86816 22.6289C4.43296 21.4278 3.91166 19.9966 2.70703 19.4395C2.14254 19.1785 2.01114 18.4345 2.45215 17.9961C3.39399 17.0605 3.39399 15.5362 2.45215 14.6006C2.01114 14.1622 2.14254 13.4182 2.70703 13.1572C3.91166 12.6001 4.43296 11.1689 3.86816 9.96777C3.60343 9.40494 3.98079 8.75039 4.60059 8.69824C5.92343 8.5869 6.90233 7.41974 6.78223 6.09766C6.72619 5.47838 7.30513 4.9933 7.90527 5.15625C9.18633 5.50405 10.5063 4.74225 10.8457 3.45898C11.0047 2.85766 11.7143 2.59858 12.2227 2.95703C13.3075 3.72199 14.8075 3.45789 15.5654 2.36816Z" fill="#CEA257" stroke="#B88E25" stroke-width="1.5"/>
                        </svg>
                    <?php elseif ( $zvezdica === 'silver' || $zvezdica === 'srebrna' ) : ?>
                        <svg width="33" height="43" viewBox="0 0 33 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.2862 26.0214L11.1162 41.2928L8.70326 38.5632L8.52461 38.3614L8.25772 38.399L5.19693 38.837L11.3446 23.6209L17.2862 26.0214Z" fill="#F3EDD9" stroke="#C6B08A"/>
                            <path d="M22.5202 23.5251L28.6902 38.7965L25.0584 38.5092L24.7897 38.4882L24.6238 38.7005L22.7263 41.1418L16.5786 25.9257L22.5202 23.5251Z" fill="#F3EDD9" stroke="#C6B08A"/>
                            <path d="M15.5654 2.36816C15.9205 1.85748 16.6762 1.85748 17.0312 2.36816C17.7891 3.45789 19.2892 3.72199 20.374 2.95703C20.8823 2.59858 21.592 2.85766 21.751 3.45898C22.0904 4.74225 23.4104 5.50405 24.6914 5.15625C25.2916 4.9933 25.8705 5.47838 25.8145 6.09766C25.6943 7.41975 26.6732 8.5869 27.9961 8.69824C28.6159 8.75039 28.9933 9.40494 28.7285 9.96777C28.1637 11.1689 28.685 12.6001 29.8896 13.1572C30.4541 13.4182 30.5855 14.1622 30.1445 14.6006C29.2027 15.5362 29.2027 17.0605 30.1445 17.9961C30.5855 18.4345 30.4541 19.1785 29.8896 19.4395C28.685 19.9966 28.1637 21.4278 28.7285 22.6289C28.9933 23.1917 28.6159 23.8463 27.9961 23.8984C26.6732 24.0098 25.6943 25.1769 25.8145 26.499C25.8705 27.1183 25.2916 27.6034 24.6914 27.4404C23.4104 27.0926 22.0904 27.8544 21.751 29.1377C21.592 29.739 20.8823 29.9981 20.374 29.6396C19.2892 28.8747 17.7891 29.1388 17.0312 30.2285C16.6762 30.7392 15.9205 30.7392 15.5654 30.2285C14.8075 29.1388 13.3075 28.8747 12.2227 29.6396C11.7143 29.9981 11.0047 29.739 10.8457 29.1377C10.5063 27.8544 9.18633 27.0926 7.90527 27.4404C7.30513 27.6034 6.72619 27.1183 6.78223 26.499C6.90233 25.1769 5.92343 24.0098 4.60059 23.8984C3.98079 23.8463 3.60343 23.1917 3.86816 22.6289C4.43296 21.4278 3.91166 19.9966 2.70703 19.4395C2.14254 19.1785 2.01114 18.4345 2.45215 17.9961C3.39399 17.0605 3.39399 15.5362 2.45215 14.6006C2.01114 14.1622 2.14254 13.4182 2.70703 13.1572C3.91166 12.6001 4.43296 11.1689 3.86816 9.96777C3.60343 9.40494 3.98079 8.75039 4.60059 8.69824C5.92343 8.5869 6.90233 7.41974 6.78223 6.09766C6.72619 5.47838 7.30513 4.9933 7.90527 5.15625C9.18633 5.50405 10.5063 4.74225 10.8457 3.45898C11.0047 2.85766 11.7143 2.59858 12.2227 2.95703C13.3075 3.72199 14.8075 3.45789 15.5654 2.36816Z" fill="#F3EDD9" stroke="#C6B08A" stroke-width="1.5"/>
                        </svg>
                    <?php elseif ( $zvezdica === 'starter' ) : ?>
                        <svg width="33" height="43" viewBox="0 0 33 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.2872 26.0214L11.1172 41.2928L8.70424 38.5632L8.52559 38.3614L8.2587 38.399L5.19791 38.837L11.3456 23.6209L17.2872 26.0214Z" fill="#DAD4C0" stroke="#BBB5A1"/>
                            <path d="M22.5202 23.5251L28.6902 38.7965L25.0584 38.5092L24.7897 38.4882L24.6238 38.7005L22.7263 41.1418L16.5786 25.9257L22.5202 23.5251Z" fill="#DAD4C0" stroke="#BBB5A1"/>
                            <path d="M15.5654 2.36816C15.9205 1.85748 16.6762 1.85748 17.0312 2.36816C17.7891 3.45789 19.2892 3.72199 20.374 2.95703C20.8823 2.59858 21.592 2.85766 21.751 3.45898C22.0904 4.74225 23.4104 5.50405 24.6914 5.15625C25.2916 4.9933 25.8705 5.47838 25.8145 6.09766C25.6943 7.41975 26.6732 8.5869 27.9961 8.69824C28.6159 8.75039 28.9933 9.40494 28.7285 9.96777C28.1637 11.1689 28.685 12.6001 29.8896 13.1572C30.4541 13.4182 30.5855 14.1622 30.1445 14.6006C29.2027 15.5362 29.2027 17.0605 30.1445 17.9961C30.5855 18.4345 30.4541 19.1785 29.8896 19.4395C28.685 19.9966 28.1637 21.4278 28.7285 22.6289C28.9933 23.1917 28.6159 23.8463 27.9961 23.8984C26.6732 24.0098 25.6943 25.1769 25.8145 26.499C25.8705 27.1183 25.2916 27.6034 24.6914 27.4404C23.4104 27.0926 22.0904 27.8544 21.751 29.1377C21.592 29.739 20.8823 29.9981 20.374 29.6396C19.2892 28.8747 17.7891 29.1388 17.0312 30.2285C16.6762 30.7392 15.9205 30.7392 15.5654 30.2285C14.8075 29.1388 13.3075 28.8747 12.2227 29.6396C11.7143 29.9981 11.0047 29.739 10.8457 29.1377C10.5063 27.8544 9.18633 27.0926 7.90527 27.4404C7.30513 27.6034 6.72619 27.1183 6.78223 26.499C6.90233 25.1769 5.92343 24.0098 4.60059 23.8984C3.98079 23.8463 3.60343 23.1917 3.86816 22.6289C4.43296 21.4278 3.91166 19.9966 2.70703 19.4395C2.14254 19.1785 2.01114 18.4345 2.45215 17.9961C3.39399 17.0605 3.39399 15.5362 2.45215 14.6006C2.01114 14.1622 2.14254 13.4182 2.70703 13.1572C3.91166 12.6001 4.43296 11.1689 3.86816 9.96777C3.60343 9.40494 3.98079 8.75039 4.60059 8.69824C5.92343 8.5869 6.90233 7.41974 6.78223 6.09766C6.72619 5.47838 7.30513 4.9933 7.90527 5.15625C9.18633 5.50405 10.5063 4.74225 10.8457 3.45898C11.0047 2.85766 11.7143 2.59858 12.2227 2.95703C13.3075 3.72199 14.8075 3.45789 15.5654 2.36816Z" fill="#DAD4C0" stroke="#BBB5A1" stroke-width="1.5"/>
                            </svg>
                    <?php endif; ?>
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
