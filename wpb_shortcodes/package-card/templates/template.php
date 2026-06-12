<?php
/**
 * Template: Package Card
 *
 * Variables provided by element.php render callback:
 *   $package_name     string
 *   $package_subtitle string
 *   $package_price    string
 *   $header_bg        string  hex color
 *   $intro_text       string  optional line above bullets
 *   $bullet_items     array   of safe HTML strings
 *   $button_count     int     1 or 2
 *   $btn1_text        string
 *   $btn1             array   [ url, target, title ]
 *   $btn2_text        string
 *   $btn2             array   [ url, target, title ]
 *   $footer_text      string  optional line below buttons
 */
?>
<div class="osn-pkg-card">

    <?php /* ── Header ──────────────────────────────────────── */ ?>
    <div class="osn-pkg-card__header" style="background-color: <?php echo esc_attr( $header_bg ); ?>;">
        <?php if ( $package_name ) : ?>
        <div class="osn-pkg-card__name"><?php echo esc_html( $package_name ); ?></div>
        <?php endif; ?>
        <?php if ( $package_subtitle ) : ?>
        <div class="osn-pkg-card__subtitle"><?php echo esc_html( $package_subtitle ); ?></div>
        <?php endif; ?>
        <?php if ( $package_price ) : ?>
        <div class="osn-pkg-card__price"><?php echo esc_html( $package_price ); ?></div>
        <?php endif; ?>
    </div>

    <?php /* ── Body (intro + bullets) ────────────────────── */ ?>
    <div class="osn-pkg-card__body">
        <?php if ( $intro_text ) : ?>
        <p class="osn-pkg-card__intro"><?php echo esc_html( $intro_text ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $bullet_items ) ) : ?>
        <ul class="osn-pkg-card__bullets">
            <?php foreach ( $bullet_items as $bullet ) : ?>
            <li class="osn-pkg-card__bullet"><?php echo $bullet; /* already kses-sanitized in element.php */ ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <?php /* ── Footer (buttons + optional text) ─────────── */ ?>
    <div class="osn-pkg-card__footer">
        <div class="osn-pkg-card__btns osn-pkg-card__btns--<?php echo $button_count === 2 ? 'two' : 'one'; ?>">
            <a class="osn-pkg-card__btn"
               href="<?php echo esc_url( $btn1['url'] ?: '#' ); ?>"
               <?php if ( ! empty( $btn1['target'] ) ) echo 'target="' . esc_attr( $btn1['target'] ) . '" rel="noopener noreferrer"'; ?>>
                <?php echo esc_html( $btn1_text ); ?>
            </a>
            <?php if ( $button_count === 2 ) : ?>
            <a class="osn-pkg-card__btn"
               href="<?php echo esc_url( $btn2['url'] ?: '#' ); ?>"
               <?php if ( ! empty( $btn2['target'] ) ) echo 'target="' . esc_attr( $btn2['target'] ) . '" rel="noopener noreferrer"'; ?>>
                <?php echo esc_html( $btn2_text ); ?>
            </a>
            <?php endif; ?>
        </div>

        <?php if ( $footer_text ) : ?>
        <p class="osn-pkg-card__footer-text"><?php echo esc_html( $footer_text ); ?></p>
        <?php endif; ?>
    </div>

</div>
