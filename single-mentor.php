<?php
/**
 * Single template for the 'mentor' CPT.
 *
 * Mentor posts act as entry points from the mentors grid, while the rich profile
 * details live on the linked Osnazena profile saved in the `link_profila` field.
 *
 * @package Dealsdot Child
 */

/**
 * Resolve the Osnazena profile ID from a stored URL.
 *
 * url_to_postid() can fail when staging stores a production-domain URL, so
 * we also fall back to resolving the last URL path segment as an osnazena slug.
 */
$resolve_profile_id = static function ( $profile_url ) {
    $resolved_id  = 0;
    $profile_url  = trim( (string) $profile_url );
    $profile_id   = $profile_url ? url_to_postid( $profile_url ) : 0;
    $profile_path = $profile_url ? wp_parse_url( $profile_url, PHP_URL_PATH ) : '';
    $profile_slug = $profile_path ? basename( untrailingslashit( $profile_path ) ) : '';

    if ( $profile_id && get_post_type( $profile_id ) === 'osnazena' ) {
        $resolved_id = (int) $profile_id;
    } elseif ( ! empty( $profile_slug ) ) {
        $profile = get_page_by_path( sanitize_title( $profile_slug ), OBJECT, 'osnazena' );
        $resolved_id = $profile ? (int) $profile->ID : 0;
    }

    return $resolved_id;
};

/**
 * Normalize a gallery meta value into attachment IDs.
 */
$get_gallery_ids = static function ( $gallery_raw ) {
    $gallery_ids = empty( $gallery_raw ) ? [] : $gallery_raw;
    $gallery_ids = is_array( $gallery_ids ) ? $gallery_ids : maybe_unserialize( $gallery_ids );

    return array_filter( array_map( 'absint', (array) $gallery_ids ) );
};

/**
 * Read profile data from the linked profile first, then the mentor post.
 */
$get_profile_meta = static function ( $profile_id, $mentor_id, $key ) {
    $value = $profile_id ? get_post_meta( $profile_id, $key, true ) : '';
    return ! empty( $value ) ? $value : get_post_meta( $mentor_id, $key, true );
};

get_header();
the_post();

$mentor_id   = get_the_ID();
$profile_url = get_post_meta( $mentor_id, 'link_profila', true );
$profile_id  = $resolve_profile_id( $profile_url );
$source_id   = $profile_id ?: $mentor_id;

$name = $get_profile_meta( $profile_id, $mentor_id, 'ime_i_prezime_vlasnice' );
$name = ! empty( $name ) ? $name : get_the_title( $mentor_id );

$company         = $get_profile_meta( $profile_id, $mentor_id, 'naziv_firme' );
$title_meta      = $get_profile_meta( $profile_id, $mentor_id, 'titula' );
$business_status = $get_profile_meta( $profile_id, $mentor_id, 'status_biznisa' );
$description     = $get_profile_meta( $profile_id, $mentor_id, 'opis' );
$city            = $get_profile_meta( $profile_id, $mentor_id, 'mesto' );
$badge_value     = strtolower( trim( (string) $get_profile_meta( $profile_id, $mentor_id, 'zvezdica' ) ) );

$site        = $get_profile_meta( $profile_id, $mentor_id, 'sajt' );
$instagram   = $get_profile_meta( $profile_id, $mentor_id, 'instagram' );
$facebook    = $get_profile_meta( $profile_id, $mentor_id, 'facebook' );
$linkedin    = $get_profile_meta( $profile_id, $mentor_id, 'linkedin' );
$youtube     = $get_profile_meta( $profile_id, $mentor_id, 'youtube' );
$email       = $get_profile_meta( $profile_id, $mentor_id, 'email' );
$phone       = $get_profile_meta( $profile_id, $mentor_id, 'telefon' );
$online_shop = $get_profile_meta( $profile_id, $mentor_id, 'osnazene_online_shop' );

$main_img_id  = (int) $get_profile_meta( $profile_id, $mentor_id, 'naslovna_slika' );
$main_img_url = $main_img_id ? wp_get_attachment_image_url( $main_img_id, 'large' ) : get_the_post_thumbnail_url( $source_id, 'large' );
$main_img_url = $main_img_url ?: get_the_post_thumbnail_url( $mentor_id, 'large' );

$gallery_ids = $get_gallery_ids( $get_profile_meta( $profile_id, $mentor_id, 'galerija_slika' ) );

$del_terms = wp_get_post_terms( $source_id, 'delatnost' );
$drz_terms = wp_get_post_terms( $source_id, 'drzava' );

$activity = '';
if ( ! empty( $title_meta ) ) {
    $activity = $title_meta;
} elseif ( ! is_wp_error( $del_terms ) && ! empty( $del_terms ) ) {
    $activity = $del_terms[0]->name;
}

$countries = '';
if ( ! is_wp_error( $drz_terms ) && ! empty( $drz_terms ) ) {
    $countries = implode( ', ', wp_list_pluck( $drz_terms, 'name' ) );
}

$badge_type = '';
if ( $badge_value === 'zlatna' || $badge_value === 'gold' ) {
    $badge_type = 'gold';
} elseif ( $badge_value === 'srebrna' || $badge_value === 'silver' ) {
    $badge_type = 'silver';
}

$links = array_filter( [
    'site'      => [ 'label' => __( 'Sajt', 'dealsdot-child' ), 'url' => $site ],
    'facebook'  => [ 'label' => __( 'Facebook', 'dealsdot-child' ), 'url' => $facebook ],
    'instagram' => [ 'label' => __( 'Instagram', 'dealsdot-child' ), 'url' => $instagram ],
    'linkedin'  => [ 'label' => __( 'LinkedIn', 'dealsdot-child' ), 'url' => $linkedin ],
    'youtube'   => [ 'label' => __( 'YouTube', 'dealsdot-child' ), 'url' => $youtube ],
], static function ( $link ) {
    return ! empty( $link['url'] );
} );

$link_icons = [
    'site'      => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><ellipse cx="12" cy="12" rx="4.5" ry="9"></ellipse><path d="M3 12h18"></path></svg>',
    'facebook'  => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 8h2V4h-2a5 5 0 0 0-5 5v3H7v4h3v5h4v-5h3l1-4h-4V9a1 1 0 0 1 1-1z"></path></svg>',
    'instagram' => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg>',
    'linkedin'  => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"></rect><path d="M8 11v6"></path><path d="M8 8.5v.01"></path><path d="M12 17v-6"></path><path d="M12 13.5c0-1.4.9-2.5 2.4-2.5S17 12.1 17 14v3"></path></svg>',
    'youtube'   => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="4"></rect><path d="M10 9.5v5l5-2.5z"></path></svg>',
    'email'     => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 7 9-7"></path></svg>',
    'phone'     => '<svg class="osn-mentor-single__link-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 16.5v3a2 2 0 0 1-2.2 2A17.6 17.6 0 0 1 3 5.2 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L9 10.4a13.5 13.5 0 0 0 4.6 4.6l1.1-1.1a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6A2 2 0 0 1 21 16.5z"></path></svg>',
];

$award_cards = [
    [
        'image'       => 'https://staging.osnazene.com/wp-content/uploads/2026/06/western-balkans-butterfly-innovation-award-2025.jpg',
        'alt_text'    => __( 'Regional Butterfly Innovation 2025', 'dealsdot-child' ),
        'title'       => __( 'Western Balkans Butterfly Innovation Award 2025', 'dealsdot-child' ),
        'description' => __( 'Women Innovation Award, dodeljena u okviru regionalne inicijative za promociju inovacija i održivog razvoja Zapadnog Balkana', 'dealsdot-child' ),
    ],
    [
        'image'       => 'https://staging.osnazene.com/wp-content/uploads/2026/06/european-enterprise-promotion-award-eepa-2024.jpg',
        'alt_text'    => __( 'EEPA 2024 Winner', 'dealsdot-child' ),
        'title'       => __( 'European Enterprise Promotion Award (EEPA) 2024', 'dealsdot-child' ),
        'description' => __( 'Prva nagrada u kategoriji Digital Transition, dodeljena od strane Evropske komisije', 'dealsdot-child' ),
    ],
];
?>

<section class="vc_section osnazene_section dark osn-mentor-single">
    <div class="container">
        <div class="osn-mentor-single__layout">
            <aside class="osn-mentor-single__media">
                <div class="osn-mentor-single__photo-wrap">
                    <?php if ( $main_img_url ) : ?>
                    <img id="osn-mentor-main-photo"
                         class="osn-mentor-single__photo"
                         src="<?php echo esc_url( $main_img_url ); ?>"
                         alt="<?php echo esc_attr( $name ); ?>"
                         loading="eager">
                    <?php else : ?>
                    <div class="osn-mentor-single__photo-placeholder"></div>
                    <?php endif; ?>

                    <?php if ( $badge_type ) : ?>
                    <span class="osn-mentor-single__badge osn-mentor-single__badge--<?php echo esc_attr( $badge_type ); ?>" aria-hidden="true">
                        <svg viewBox="0 0 46 46" xmlns="http://www.w3.org/2000/svg">
                            <polygon class="osn-mentor-single__badge-ribbon" points="17.77,24.85 22.63,27.53 20,44 13,44"/>
                            <polygon class="osn-mentor-single__badge-ribbon" points="22.63,27.53 28.04,24.85 33,44 26,44"/>
                            <circle class="osn-mentor-single__badge-circle" cx="23" cy="18" r="13"/>
                            <text class="osn-mentor-single__badge-star" x="23" y="23" text-anchor="middle" font-size="14" font-family="serif">★</text>
                        </svg>
                    </span>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $gallery_ids ) ) : ?>
                <div class="osn-mentor-single__gallery">
                    <div class="osn-mentor-single__thumbs">
                        <?php
                        $thumbs_shown = 0;
                        foreach ( $gallery_ids as $gallery_id ) :
                            if ( $thumbs_shown >= 3 ) {
                                break;
                            }
                            $thumb_url = wp_get_attachment_image_url( $gallery_id, 'thumbnail' );
                            $full_url  = wp_get_attachment_image_url( $gallery_id, 'large' );
                            if ( ! $thumb_url || ! $full_url ) {
                                continue;
                            }
                            ?>
                            <button class="osn-mentor-single__thumb<?php echo $thumbs_shown === 0 ? ' is-active' : ''; ?>"
                                    data-full="<?php echo esc_url( $full_url ); ?>"
                                    type="button"
                                    aria-label="<?php esc_attr_e( 'Prikaži sliku', 'dealsdot-child' ); ?>">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy">
                            </button>
                            <?php
                            $thumbs_shown++;
                        endforeach;
                        ?>
                    </div>
                    <div class="osn-mentor-single__dots" aria-hidden="true">
                        <?php for ( $i = 0; $i < $thumbs_shown; $i++ ) : ?>
                        <span class="osn-mentor-single__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $links ) || ! empty( $email ) || ! empty( $phone ) ) : ?>
                <div class="osn-mentor-single__links">
                    <h2 class="osn-mentor-single__links-heading"><?php esc_html_e( 'Korisni linkovi:', 'dealsdot-child' ); ?></h2>
                    <?php foreach ( $links as $type => $link ) : ?>
                    <a class="osn-mentor-single__link" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo $link_icons[ $type ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span><?php echo esc_html( $link['label'] ); ?></span>
                    </a>
                    <?php endforeach; ?>
                    <?php if ( ! empty( $email ) ) : ?>
                    <a class="osn-mentor-single__link" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                        <?php echo $link_icons['email']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span><?php echo esc_html( antispambot( $email ) ); ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if ( ! empty( $phone ) ) : ?>
                    <a class="osn-mentor-single__link" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>">
                        <?php echo $link_icons['phone']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span><?php echo esc_html( $phone ); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </aside>

            <main class="osn-mentor-single__content">
                <div class="osn-mentor-single__header">
                    <h1 class="osn-mentor-single__name"><?php echo esc_html( $name ); ?></h1>
                    <?php if ( ! empty( $company ) ) : ?>
                    <p class="osn-mentor-single__company"><?php echo esc_html( $company ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $business_status ) ) : ?>
                <span class="osn-mentor-single__status"><?php echo esc_html( $business_status ); ?></span>
                <?php endif; ?>

                <div class="osn-mentor-single__meta">
                    <?php if ( ! empty( $activity ) ) : ?>
                    <p><strong><?php esc_html_e( 'Delatnost:', 'dealsdot-child' ); ?></strong> <?php echo esc_html( $activity ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $city ) ) : ?>
                    <p><strong><?php esc_html_e( 'Mesto:', 'dealsdot-child' ); ?></strong> <?php echo esc_html( $city ); ?></p>
                    <?php endif; ?>
                    <?php if ( ! empty( $countries ) ) : ?>
                    <p><strong><?php esc_html_e( 'Država:', 'dealsdot-child' ); ?></strong> <?php echo esc_html( $countries ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="osn-mentor-single__actions">
                    <?php if ( ! empty( $online_shop ) ) : ?>
                    <a class="osn-mentor-single__button" href="<?php echo esc_url( $online_shop ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Posetite Online Shop', 'dealsdot-child' ); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <?php if ( trim( get_the_content() ) ) : ?>
                <div class="osn-mentor-single__extra-content">
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $description ) ) : ?>
                <div class="osn-mentor-single__description">
                    <p class="osn-mentor-single__description-label"><strong><?php esc_html_e( 'Opis:', 'dealsdot-child' ); ?></strong></p>
                    <div class="osn-mentor-single__description-text">
                        <?php echo wp_kses_post( wpautop( $description ) ); ?>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>

<section class="vc_section osnazene_section gold osn-mentor-awards">
    <div class="container">
        <h2 class="osn-mentor-awards__heading">
            <?php esc_html_e( 'Prijavite se putem forme ispod i obezbedite svoje mesto!', 'dealsdot-child' ); ?>
        </h2>

        <div class="osn-mentor-awards__grid">
            <?php foreach ( $award_cards as $award_card ) : ?>
            <article class="osn-mentor-awards__card">
                <img class="osn-mentor-awards__image"
                     src="<?php echo esc_url( $award_card['image'] ); ?>"
                     alt="<?php echo esc_attr( $award_card['alt_text'] ); ?>"
                     loading="lazy">
                <h3 class="osn-mentor-awards__title"><?php echo esc_html( $award_card['title'] ); ?></h3>
                <p class="osn-mentor-awards__description"><?php echo esc_html( $award_card['description'] ); ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
(function () {
    var thumbs = document.querySelectorAll('.osn-mentor-single__thumb');
    var mainPhoto = document.getElementById('osn-mentor-main-photo');
    var dots = document.querySelectorAll('.osn-mentor-single__dot');
    if (!thumbs.length || !mainPhoto) return;

    thumbs.forEach(function (btn, i) {
        btn.addEventListener('click', function () {
            mainPhoto.src = btn.dataset.full;
            thumbs.forEach(function (item) { item.classList.remove('is-active'); });
            dots.forEach(function (dot) { dot.classList.remove('is-active'); });
            btn.classList.add('is-active');
            if (dots[i]) dots[i].classList.add('is-active');
        });
    });
}());
</script>

<?php get_footer(); ?>
