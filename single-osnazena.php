<?php
/**
 * single-osnazena.php — Single post template for the 'osnazena' CPT.
 * @package Dealsdot Child
 */

// Hide header on mobile devices for this page
echo '<style>
@media (max-width: 768px) {
    header.header-style-1 {
        display: none;
    }
}
</style>';

get_header();
the_post();

$post_id = get_the_ID();

// --- ACF fields -------------------------------------------------
$name           = get_post_meta( $post_id, 'ime_i_prezime_vlasnice', true );
if ( empty( $name ) ) $name = get_the_title();

$naziv_firme    = get_post_meta( $post_id, 'naziv_firme', true );
$titula         = get_post_meta( $post_id, 'titula', true );
$status_biznisa = get_post_meta( $post_id, 'status_biznisa', true );
$opis           = get_post_meta( $post_id, 'opis', true );
$mesto          = get_post_meta( $post_id, 'mesto', true );
$zvezdica       = strtolower( trim( (string) get_post_meta( $post_id, 'zvezdica', true ) ) );

// Links
$sajt           = get_post_meta( $post_id, 'sajt', true );
$instagram      = get_post_meta( $post_id, 'instagram', true );
$facebook       = get_post_meta( $post_id, 'facebook', true );
$linkedin       = get_post_meta( $post_id, 'linkedin', true );
$youtube        = get_post_meta( $post_id, 'youtube', true );
$email          = get_post_meta( $post_id, 'email', true );
$telefon        = get_post_meta( $post_id, 'telefon', true );
$online_shop    = get_post_meta( $post_id, 'osnazene_online_shop', true );

// --- Main photo -------------------------------------------------
$img_id  = (int) get_post_meta( $post_id, 'naslovna_slika', true );
$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : get_the_post_thumbnail_url( $post_id, 'large' );

// --- Gallery images (serialized array of attachment IDs) --------
$gallery_raw = get_post_meta( $post_id, 'galerija_slika', true );
$gallery_ids = [];
if ( ! empty( $gallery_raw ) ) {
    if ( is_array( $gallery_raw ) ) {
        $gallery_ids = $gallery_raw;
    } else {
        $gallery_ids = maybe_unserialize( $gallery_raw );
    }
}
$gallery_ids = array_filter( (array) $gallery_ids );

// --- Taxonomies -------------------------------------------------
$del_terms = wp_get_post_terms( $post_id, 'delatnost' );
$drz_terms = wp_get_post_terms( $post_id, 'drzava' );

$delatnost_label = '';
if ( ! is_wp_error( $del_terms ) && ! empty( $del_terms ) ) {
    // prefer the ACF meta if filled, otherwise taxonomy label
    $delatnost_label = ! empty( $titula ) ? $titula : $del_terms[0]->name;
} elseif ( ! empty( $titula ) ) {
    $delatnost_label = $titula;
}

// Location: city + country
$lokacija_parts = array_filter( [
    $mesto,
    ( ! is_wp_error( $drz_terms ) && ! empty( $drz_terms ) ) ? $drz_terms[0]->name : '',
] );
$lokacija = implode( ', ', $lokacija_parts );

// --- Badge class ------------------------------------------------
if ( $zvezdica === 'zlatna' || $zvezdica === 'gold' ) {
    $badge_type = 'gold';
} elseif ( $zvezdica === 'srebrna' || $zvezdica === 'silver' ) {
    $badge_type = 'silver';
} else {
    $badge_type = '';
}

// --- Display title (firm name preferred over personal name) -----
$display_title = ! empty( $naziv_firme ) ? esc_html( $naziv_firme ) : esc_html( $name );
?>

<section class="vc_section osnazene_section osn-clanica-single">
    <div class="container">
       <!-- Mobile header -->
        <div class="osn-clanica-single__mobile-header">
            <!-- Mobile-only top nav bar -->
            <div class="osn-clanica-single__mobile-nav">
                <a class="osn-clanica-single__back" href="<?php echo esc_url(get_post_type_archive_link('osnazena')); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <polyline points="15,18 9,12 15,6" stroke="#151367" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span><?php esc_html_e('Nazad', 'dealsdot-child'); ?></span>
                </a>
                <button class="osn-clanica-single__share" type="button" aria-label="<?php esc_attr_e('Podeli', 'dealsdot-child'); ?>" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" stroke="#151367" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                        <polyline points="16,6 12,2 8,6" stroke="#151367" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <line x1="12" y1="2" x2="12" y2="15" stroke="#151367" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
            <div class="osn-clanica-single__photo">
                <?php if ($img_url) : ?>
                    <img id="osn-clanica-main-photo" class="osn-clanica-single__photo" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" />
                <?php else : ?>
                    <div class="osn-clanica-single__photo-placeholder"></div>
                <?php endif; ?>
            </div>
            <h3 class="osn-clanica-single__name"><?php echo $display_title; ?></h3>
            <div class="osn-clanice-single__subheading">
                <?php if ( ! empty( $delatnost_label ) ) : ?>
                    <p class="osn-clanica-single__meta-row">
                        <span><?php echo esc_html( $delatnost_label ); ?></span>
                    </p>
                <?php endif; ?>
                <?php if ( ! empty( $delatnost_label ) && ! empty( $lokacija ) ) :?>
                    <span class="osn-clanica-single__meta-row-separator"></span>
                 <?php endif; ?>   
                 <?php if ( ! empty( $lokacija ) ) : ?>
                    <p class="osn-clanica-single__meta-row">
                        <span><?php echo esc_html( $lokacija ); ?></span>
                    </p>
                <?php endif; ?>
            </div>
             <?php if ( ! empty( $status_biznisa ) ) : ?>
                <div class="osn-clanice-single__status-container">
                  <span class="osn-clanica-single__status-tag"><?php echo esc_html( $status_biznisa ); ?></span>
                </div>
            <?php endif; ?>
            <!-- Mobile-only CTA button -->
            <?php if ( ! empty( $email ) || ! empty( $sajt ) || ! empty( $telefon ) ) : ?>
                <div class="osn-clanica-single__cta-wrap">
                    <?php if ( ! empty( $email ) ) : ?>
                    <a class="osn-clanica-single__cta btn btn-primary" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                        <?php esc_html_e( 'Kontaktiraj Članicu', 'dealsdot-child' ); ?>
                    </a>
                    <?php elseif ( ! empty( $sajt ) ) : ?>
                    <a class="osn-clanica-single__cta btn btn-primary" href="<?php echo esc_url( $sajt ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Kontaktiraj Članicu', 'dealsdot-child' ); ?>
                    </a>
                    <?php else : ?>
                    <a class="osn-clanica-single__cta btn btn-primary" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $telefon ) ); ?>">
                        <?php esc_html_e( 'Kontaktiraj Članicu', 'dealsdot-child' ); ?>
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="osn-clanica-single__inner">
           

            <!-- ===================== LEFT COLUMN ===================== -->
            <div class="osn-clanica-single__left">

                <!-- Main photo -->
                <div class="osn-clanica-single__photo-wrap">
                    <?php if ( $img_url ) : ?>
                        <img id="osn-clanica-main-photo"
                            class="osn-clanica-single__photo"
                            src="<?php echo esc_url( $img_url ); ?>"
                            alt="<?php echo esc_attr( $name ); ?>" />
                    <?php else : ?>
                        <div class="osn-clanica-single__photo-placeholder"></div>
                    <?php endif; ?>

                    <?php if ( $badge_type ) : ?>
                    <div class="osn-clanica-single__badge osn-clanica-single__badge--<?php echo esc_attr( $badge_type ); ?>">
                        <?php if ( $badge_type === 'gold' ) : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" viewBox="0 0 46 46" fill="none">
                            <polygon points="25.27,24.85 18.77,42.63 21.47,24.85" fill="#CEA257" stroke="#B88E25" stroke-width="1"/>
                            <polygon points="22.63,27.53 30.04,45.31 22.63,27.53" fill="#CEA257" stroke="#B88E25" stroke-width="1"/>
                            <circle cx="23" cy="17" r="11" fill="#CEA257" stroke="#B88E25" stroke-width="1"/>
                        </svg>
                        <?php else : ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" viewBox="0 0 46 46" fill="none">
                            <polygon points="25.27,24.85 18.77,42.63 21.47,24.85" fill="#F3EDD9" stroke="#C6B08A" stroke-width="1"/>
                            <polygon points="22.63,27.53 30.04,45.31 22.63,27.53" fill="#F3EDD9" stroke="#C6B08A" stroke-width="1"/>
                            <circle cx="23" cy="17" r="11" fill="#E6E6E6" stroke="#C6B08A" stroke-width="1"/>
                        </svg>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div><!-- /.osn-clanica-single__photo-wrap -->

                 <div class="osn-clanice-single__description-mobile">
                    <?php if ( ! empty( $opis ) ) : ?>
                        <div class="osn-clanica-single__opis">
                            <p class="osn-clanica-single__opis-label"><strong><?php esc_html_e( 'Opis:', 'dealsdot-child' ); ?></strong></p>
                            <div class="osn-clanica-single__opis-text">
                                <?php echo wp_kses_post( wpautop( $opis ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( ! empty( $gallery_ids ) ) : ?>
                <!-- Gallery thumbnails -->
                <div class="osn-clanica-single__gallery">
                    <div class="osn-clanica-single__thumbs">
                        <?php
                        $thumbs_shown = 0;
                        foreach ( $gallery_ids as $gid ) {
                            if ( $thumbs_shown >= 3 ) break;
                            $thumb_url = wp_get_attachment_image_url( (int) $gid, 'thumbnail' );
                            $full_url  = wp_get_attachment_image_url( (int) $gid, 'large' );
                            if ( ! $thumb_url ) continue;
                            ?>
                            <button class="osn-clanica-single__thumb<?php echo $thumbs_shown === 0 ? ' is-active' : ''; ?>"
                                    data-full="<?php echo esc_url( $full_url ); ?>"
                                    type="button"
                                    aria-label="<?php esc_attr_e( 'Prikaži sliku', 'dealsdot-child' ); ?>">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" />
                            </button>
                            <?php
                            $thumbs_shown++;
                        }
                        ?>
                    </div>
                    <div class="osn-clanica-single__dots" aria-hidden="true">
                        <?php for ( $i = 0; $i < $thumbs_shown; $i++ ) : ?>
                            <span class="osn-clanica-single__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /.osn-clanica-single__left -->

            <!-- ===================== RIGHT COLUMN ==================== -->
            <div class="osn-clanica-single__right">

                <!-- Header info -->
                <div class="osn-clanica-single__info">

                    <h3 class="osn-clanica-single__name"><?php echo $display_title; ?></h3>

                    <?php if ( ! empty( $status_biznisa ) ) : ?>
                    <span class="osn-clanica-single__status-tag"><?php echo esc_html( $status_biznisa ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $delatnost_label ) ) : ?>
                    <p class="osn-clanica-single__meta-row">
                        <strong><?php esc_html_e( 'Delatnost:', 'dealsdot-child' ); ?></strong>
                        <span><?php echo esc_html( $delatnost_label ); ?></span>
                    </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $lokacija ) ) : ?>
                    <p class="osn-clanica-single__meta-row">
                        <strong><?php esc_html_e( 'Lokacija:', 'dealsdot-child' ); ?></strong>
                        <span><?php echo esc_html( $lokacija ); ?></span>
                    </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $opis ) ) : ?>
                    <div class="osn-clanica-single__opis">
                        <p class="osn-clanica-single__opis-label"><strong><?php esc_html_e( 'Opis:', 'dealsdot-child' ); ?></strong></p>
                        <div class="osn-clanica-single__opis-text">
                            <?php echo wp_kses_post( wpautop( $opis ) ); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- /.osn-clanica-single__info -->

                <!-- Links section -->
                <?php
                $links = array_filter( [
                    'sajt'      => $sajt,
                    'instagram' => $instagram,
                    'facebook'  => $facebook,
                    'linkedin'  => $linkedin,
                    'youtube'   => $youtube,
                    'shop'      => $online_shop,
                ] );
                $has_links = ! empty( $links ) || ! empty( $email ) || ! empty( $telefon );
                ?>
                <?php if ( $has_links ) : ?>
                <div class="osn-clanica-single__links">
                    <h3 class="osn-clanica-single__links-heading"><?php esc_html_e( 'Korisni linkovi:', 'dealsdot-child' ); ?></h3>

                    <?php if ( ! empty( $sajt ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $sajt ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <ellipse cx="12" cy="12" rx="4.5" ry="9" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <line x1="3" y1="12" x2="21" y2="12" stroke="#151367" stroke-width="1.5"/>
                        </svg>
                        <span><?php esc_html_e( 'Sajt', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $instagram ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="2" width="20" height="20" rx="5" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <circle cx="12" cy="12" r="4" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <circle cx="17.5" cy="6.5" r="1" fill="#151367"/>
                        </svg>
                        <span><?php esc_html_e( 'Instagram', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $facebook ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" stroke="#151367" stroke-width="1.5" fill="none" stroke-linejoin="round"/>
                        </svg>
                        <span><?php esc_html_e( 'Facebook', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $linkedin ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="2" width="20" height="20" rx="3" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <line x1="8" y1="11" x2="8" y2="16" stroke="#151367" stroke-width="1.5"/>
                            <line x1="8" y1="8" x2="8" y2="8.5" stroke="#151367" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 11v5m0-5c0 0 0-2 3-2s3 2 3 2v5" stroke="#151367" stroke-width="1.5" fill="none" stroke-linejoin="round"/>
                        </svg>
                        <span><?php esc_html_e( 'LinkedIn', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $youtube ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="5" width="20" height="14" rx="4" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <polygon points="10,8.5 10,15.5 16,12" fill="#151367"/>
                        </svg>
                        <span><?php esc_html_e( 'YouTube', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $online_shop ) ) : ?>
                    <a class="osn-clanica-single__link" href="<?php echo esc_url( $online_shop ); ?>" target="_blank" rel="noopener noreferrer">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="#151367" stroke-width="1.5" fill="none" stroke-linejoin="round"/>
                            <line x1="3" y1="6" x2="21" y2="6" stroke="#151367" stroke-width="1.5"/>
                            <path d="M16 10a4 4 0 0 1-8 0" stroke="#151367" stroke-width="1.5" fill="none"/>
                        </svg>
                        <span><?php esc_html_e( 'Online shop', 'dealsdot-child' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $email ) ) : ?>
                    <a class="osn-clanica-single__link" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="4" width="20" height="16" rx="2" stroke="#151367" stroke-width="1.5" fill="none"/>
                            <polyline points="2,4 12,13 22,4" stroke="#151367" stroke-width="1.5" fill="none"/>
                        </svg>
                        <span><?php echo esc_html( antispambot( $email ) ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $telefon ) ) : ?>
                    <a class="osn-clanica-single__link" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $telefon ) ); ?>">
                        <svg class="osn-clanica-single__link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 2 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke="#151367" stroke-width="1.5" fill="none"/>
                        </svg>
                        <span><?php echo esc_html( $telefon ); ?></span>
                    </a>
                    <?php endif; ?>

                </div><!-- /.osn-clanica-single__links -->
                <?php endif; ?>

                

            </div><!-- /.osn-clanica-single__right -->

        </div><!-- /.osn-clanica-single__inner -->
    </div>
</section>

<script>
(function () {
    var thumbs = document.querySelectorAll('.osn-clanica-single__thumb');
    var mainPhoto = document.getElementById('osn-clanica-main-photo');
    var dots = document.querySelectorAll('.osn-clanica-single__dot');
    if (!thumbs.length || !mainPhoto) return;

    thumbs.forEach(function (btn, i) {
        btn.addEventListener('click', function () {
            mainPhoto.src = btn.dataset.full;
            thumbs.forEach(function (b) { b.classList.remove('is-active'); });
            dots.forEach(function (d) { d.classList.remove('is-active'); });
            btn.classList.add('is-active');
            if (dots[i]) dots[i].classList.add('is-active');
        });
    });
}());
</script>

<?php get_footer(); ?>
