<?php
/**
 * Template: Two Button CTA
 * Variables available:
 * - $heading1 (string)
 * - $heading2 (string)
 * - $heading3 (string)
 * - $text (string, HTML)
 * - $btn1 (array: url, title, target)
 * - $btn2 (array: url, title, target)
 */
?>
<div class="wpb-cta wpb-osnazene-cta-2bttn-wrapper-outer dark">
    <div class="wpb-cta wpb-osnazene-cta-2bttn-wrapper-inner vc_col-xl-12 vc_col-lg-12 vc_col-md-12 vc_col-sm-12 vc_col-xs-12">
    <?php if ( ! empty( $heading1 ) || ! empty( $heading2 ) || ! empty( $heading3 ) ) : ?>
        <h3 class="wpb-cta__heading wpb-osnazene-cta-2bttn-heading">
            <div style="width: 100%; height: 100%; justify-content: flex-start; align-items: center; gap: 37px; display: inline-flex">
                <div><span style="color: white; font-size: 36px; font-family: Montserrat; font-weight: 700; line-height: 22px; letter-spacing: 1.92px; word-wrap: break-word"><?php echo esc_html( $heading1 ); ?></span><span style="color: white; font-size: 36px; font-family: Montserrat; font-weight: 700; line-height: 55.20px; letter-spacing: 1.92px; word-wrap: break-word"> </span></div>
                <div style="width: 10px; height: 10px; background: white; border-radius: 9999px"></div>
                <div style="color: white; font-size: 36px; font-family: Montserrat; font-weight: 700; line-height: 22px; letter-spacing: 1.92px; word-wrap: break-word"><?php echo esc_html( $heading2 ); ?></div>
                <div style="width: 10px; height: 10px; background: white; border-radius: 9999px"></div>
                <div><span style="color: white; font-size: 36px; font-family: Montserrat; font-weight: 700; line-height: 22px; letter-spacing: 1.92px; word-wrap: break-word"><?php echo esc_html( $heading3 ); ?></span><span style="color: white; font-size: 36px; font-family: Montserrat; font-weight: 700; line-height: 55.20px; letter-spacing: 1.92px; word-wrap: break-word"> </span></div>
            </div>
        </h3>
    <?php endif; ?>

    <?php if ( ! empty( $text ) ) : ?>
        <?php $text1 = wp_kses_post( $text ); ?>
        <?php
            $text_clean = preg_replace_callback(
                '/<p\b[^>]*>(.*?)<\/p>/is',
                static function ( $m ) {
                    $inner = html_entity_decode( wp_strip_all_tags( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
                    $inner = preg_replace( '/[\s\x{00A0}]+/u', '', $inner );
                    return $inner === '' ? '' : $m[0];
                },
                $text1
            );
        ?>
        <div class="wpb-cta__text wpb-osnazene-cta-2bttn-text"><?php echo $text_clean; ?></div>
    <?php else: ?>
        <div class="wpb-cta__text wpb-osnazene-cta-2bttn-text">
            <p>Mi smo pokretač razvoja ženskog preduzetništva — tu da vas podržimo u svakom izazovu, pružimo prave informacije, omogućimo edukaciju, osnažimo, povežemo i pomognemo vam da ostvarite svoje najveće ciljeve.<br /><br />Prijavi se sa svojim biznisom već danas i počni da prodaješ kao deo zajednice i rasta Osnaženih!<br /><br /><strong>Dobrodošla u zajednicu u kojoj se ženski glas vidi, čuje i vrednuje.</strong></p>
        </div>
    <?php endif; ?>

    <div class="wpb-cta__buttons wpb-osnazene-cta-2bttn-buttons">
        <?php if ( ! empty( $btn1['url'] ) ) : ?>
        <a class="btn btn-primary wpb-cta__btn wpb-cta__btn--1" href="<?php echo esc_url( $btn1['url'] ); ?>"<?php echo ! empty( $btn1['target'] ) ? ' target="' . esc_attr( $btn1['target'] ) . '" rel="noopener"' : ''; ?>>
            <?php echo esc_html( isset( $btn1['title'] ) && $btn1['title'] !== '' ? $btn1['title'] : 'Button 1' ); ?>
        </a>
        <?php endif; ?>

        <?php if ( ! empty( $btn2['url'] ) ) : ?>
        <a class="btn btn-secondary wpb-cta__btn wpb-cta__btn--2" href="<?php echo esc_url( $btn2['url'] ); ?>"<?php echo ! empty( $btn2['target'] ) ? ' target="' . esc_attr( $btn2['target'] ) . '" rel="noopener"' : ''; ?>>
            <?php echo esc_html( isset( $btn2['title'] ) && $btn2['title'] !== '' ? $btn2['title'] : 'Button 2' ); ?>
        </a>
        <?php endif; ?>
    </div>
    </div>
</div>