<?php
/**
 * Template: Package Card
 *
 * Variables provided by element.php render callback:
 *   $package_name     string
 *   $package_subtitle string
 *   $package_price    string
 *   $header_bg        string  hex color
 *   $payment_monthly  string
 *   $payment_method   string
 *   $payment_duration string
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

    <?php /* ── Payment details ─────────────────────────────── */ ?>
    <?php if ( $payment_monthly || $payment_method || $payment_duration ) : ?>
    <div class="osn-pkg-card__payment-details">
        <?php if ( $payment_monthly ) : ?>
        <div class="osn-pkg-card__payment-row">
            <svg class="osn-pkg-card__payment-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3.5" y="5.5" width="17" height="15" rx="1.5"></rect>
                <path d="M7.5 3.5v4M16.5 3.5v4M3.5 9.5h17"></path>
                <path d="M8 13h.01M12 13h.01M16 13h.01M8 17h.01M12 17h.01M16 17h.01"></path>
            </svg>
            <span><?php echo esc_html( $payment_monthly ); ?></span>
        </div>
        <?php endif; ?>

        <?php if ( $payment_method ) : ?>
        <div class="osn-pkg-card__payment-row">
            <svg class="osn-pkg-card__payment-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect>
                <path d="M3.5 9.5h17M7 15h4"></path>
            </svg>
            <span><?php echo esc_html( $payment_method ); ?></span>
        </div>
        <?php endif; ?>

        <?php if ( $payment_duration ) : ?>
        <div class="osn-pkg-card__payment-row">
            <svg class="osn-pkg-card__payment-icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="8.5"></circle>
                <path d="M12 7.5V12l3 2"></path>
            </svg>
            <span><?php echo esc_html( $payment_duration ); ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php /* ── Body (intro + bullets) ────────────────────── */ ?>
    <div class="osn-pkg-card__body">
        <h3 class="osn-pkg-card__benefits-title"><?php esc_html_e( 'Šta dobijate', 'dealsdot-child' ); ?></h3>

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
               <?php
               if ( ! empty( $btn1['target'] ) ) {
                   echo 'target="' . esc_attr( $btn1['target'] ) . '" rel="noopener noreferrer"';
               }
               ?>>
                <?php echo esc_html( $btn1_text ); ?>
            </a>
            <?php if ( $button_count === 2 ) : ?>
            <a class="osn-pkg-card__btn"
               href="<?php echo esc_url( $btn2['url'] ?: '#' ); ?>"
               <?php
               if ( ! empty( $btn2['target'] ) ) {
                   echo 'target="' . esc_attr( $btn2['target'] ) . '" rel="noopener noreferrer"';
               }
               ?>>
                <?php echo esc_html( $btn2_text ); ?>
            </a>
            <?php endif; ?>
        </div>

        <?php if ( $footer_text ) : ?>
        <p class="osn-pkg-card__footer-text"><?php echo esc_html( $footer_text ); ?></p>
        <?php endif; ?>
    </div>

</div>
