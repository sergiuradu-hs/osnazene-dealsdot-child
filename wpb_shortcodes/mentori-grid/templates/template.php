<?php
/**
 * Template: Mentori Grid
 *
 * Variables available (set in element.php render callback):
 *   $mentors – array of mentor data (id, name, link, uloga, cv_link, img_url)
 */
?>
<section class="vc_section osnazene_section dark osn-mp-mentori">

    <?php if ( ! empty( $mentors ) ) : ?>
    <div class="osn-mp-grid">
        <?php foreach ( $mentors as $mentor ) : ?>
        <div class="osn-mp-card">

            <div class="osn-mp-card__photo-wrap">
                <?php if ( ! empty( $mentor['img_url'] ) ) : ?>
                <img src="<?php echo esc_url( $mentor['img_url'] ); ?>"
                     alt="<?php echo esc_attr( $mentor['name'] ); ?>"
                     class="osn-mp-card__photo"
                     loading="lazy">
                <?php endif; ?>
            </div>

            <div class="osn-mp-card__info">
                <h3 class="osn-mp-card__name"><?php echo esc_html( $mentor['name'] ); ?></h3>
                <?php if ( ! empty( $mentor['uloga'] ) ) : ?>
                <p class="osn-mp-card__uloga"><?php echo esc_html( $mentor['uloga'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $mentor['opis'] ) ) : ?>
                    <div class="osn-mp-card__opis"><?php echo wp_kses_post( $mentor['opis'] ); ?></div>
                <?php endif; ?>
            </div>

            <div class="osn-mp-card__actions">
                <?php if ( ! empty( $mentor['cv_link'] ) ) : ?>
                <a href="<?php echo esc_url( $mentor['cv_link'] ); ?>"
                   class="osn-mp-card__btn"
                   target="_blank"
                   rel="noopener noreferrer">Mentorski portfolio</a>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</section>
