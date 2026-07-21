<?php
/**
 * Single post template for the `edukacija` custom post type.
 *
 * Sections:
 *   1. Main    – centered title + card (left, ACF) + the_content() (right)
 *   2. Prijava – registration heading + form shortcode + image (shown if ACF fields set)
 *
 * ACF fields: datum (unix ts), platforma (text), logo-sponzora (attachment ID),
 *             forma-prijave (shortcode string), slika-forme (attachment ID)
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();

    $post_id        = get_the_ID();
    $title          = get_the_title();
    $img_url        = get_the_post_thumbnail_url( $post_id, 'large' );
    $datum_ts       = (int) get_post_meta( $post_id, 'datum', true );
    $datum_str      = $datum_ts ? date_i18n( 'd.m.Y.', $datum_ts ) : '';
    $platforma      = get_post_meta( $post_id, 'platforma', true );
    $format         = get_post_meta( $post_id, 'format_edukacije', true );
    if ( empty( $format ) ) {
        $format = $platforma;
    }
    $sponsor_id     = get_post_meta( $post_id, 'logo-sponzora', true );
    $forma          = get_post_meta( $post_id, 'forma-prijave', true );
    $slika_forme_id = (int) get_post_meta( $post_id, 'slika-forme', true );
    ?>

    <section class="vc_section osnazene_section osn-single-edukacija">
        <div class="container">
            <h1 class="osn-sed__title"><?php echo esc_html( $title ); ?></h1>
            <span class="osng-sed__date"><?php echo esc_html( $datum_ts ); ?></span>

            <div class="osn-sed__row">

                <!-- Left: card -->
                <div class="osn-sed__card">
                    <?php if ( $img_url ) : ?>
                    <div class="osn-sed__card-img">
                        <img src="<?php echo esc_url( $img_url ); ?>"
                            alt="<?php echo esc_attr( $title ); ?>"
                            loading="lazy">
                    </div>
                    <?php endif; ?>

                    <div class="osn-sed__card-body">
                        <?php if ( $datum_str || $format ) : ?>
                        <div class="osn-sed__card-meta">
                            <span class="osn-sed__card-date"><?php echo esc_html( $datum_str ); ?></span>
                            <?php if ( $format ) : ?>
                            <span class="osn-sed__card-platform"><?php echo esc_html( $format ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <h2 class="osn-sed__card-heading"><?php echo esc_html( $title ); ?></h2>

                        <?php
                        if ( $sponsor_id ) :
                            $sponsor_img = wp_get_attachment_image( $sponsor_id, [ 137, 50 ], false, [
                                'class'   => 'osn-sed__card-sponsor-img',
                                'loading' => 'lazy',
                            ] );
                            if ( $sponsor_img ) : ?>
                        <div class="osn-sed__card-sponsor">
                            <span class="osn-sed__card-sponsor-label">
                                <?php esc_html_e( 'Sponzor edukacije', 'dealsdot-child' ); ?>
                            </span>
                            <?php echo $sponsor_img; ?>
                        </div>
                            <?php endif;
                        endif; ?>
                    </div>
                </div>

                <!-- Right: post content -->
                <div class="osn-sed__content">
                    <?php the_content(); ?>
                </div>

            </div>
        </div>

    </section>

    <?php if ( $forma || $slika_forme_id ) : ?>
    <section class="vc_section osnazene_section osn-sed-prijava">
        <div class="container">
            <h2 class="osn-sed-prijava__heading">
                <?php esc_html_e( 'Prijavite se putem forme ispod i obezbedite svoje mesto!', 'dealsdot-child' ); ?>
            </h2>

            <?php if ( $forma ) : ?>
            <div class="osn-sed-prijava__form">
                <?php echo do_shortcode( $forma ); ?>
            </div>
            <?php endif; ?>

            <?php if ( $slika_forme_id ) :
                $prijava_img = wp_get_attachment_image( $slika_forme_id, [ 1000, 298 ], false, [
                    'loading' => 'lazy',
                    'class'   => 'osn-sed-prijava__img-el',
                ] );
                if ( $prijava_img ) : ?>
            <div class="osn-sed-prijava__img">
                <?php echo $prijava_img; ?>
            </div>
            <?php endif; endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php
    endwhile;
endif;

get_footer();
