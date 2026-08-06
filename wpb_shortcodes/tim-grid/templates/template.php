<?php
/**
 * Template: Tim Grid
 *
 * Variables (from element.php render callback):
 *   $members              – array of [ name, role, link, img_url ]
 *   $feature_first_mobile – bool: featured first card on mobile (true)
 *                           or all cards stacked horizontally (false)
 */
?>
<div class="osn-tim-grid<?php echo $feature_first_mobile ? '' : ' osn-tim-grid--stacked-mobile'; ?>">
    <?php foreach ( $members as $member ) :
        $tag       = ! empty( $member['link'] ) ? 'a' : 'div';
        $link_attr = $tag === 'a' ? ' href="' . esc_url( $member['link'] ) . '"' : '';
    ?>
    <<?php echo $tag; ?> class="osn-tim-card<?php echo $tag === 'a' ? ' osn-tim-card--linked' : ''; ?>"<?php echo $link_attr; ?>>

        <div class="osn-tim-card__photo-wrap">
            <?php if ( ! empty( $member['img_url'] ) ) : ?>
            <img
                class="osn-tim-card__photo"
                src="<?php echo esc_url( $member['img_url'] ); ?>"
                alt="<?php echo esc_attr( $member['name'] ); ?>"
                loading="lazy"
            >
            <?php endif; ?>
        </div>

        <div class="osn-tim-card__info">
            <p class="osn-tim-card__name"><?php echo esc_html( $member['name'] ); ?></p>
            <?php if ( ! empty( $member['role'] ) ) : ?>
            <p class="osn-tim-card__role"><?php echo esc_html( $member['role'] ); ?></p>
            <?php endif; ?>
        </div>

    </<?php echo $tag; ?>>
    <?php endforeach; ?>
</div>

