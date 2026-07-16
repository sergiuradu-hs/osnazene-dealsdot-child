<?php
/**
 * Template: Statistics Box.
 *
 * Variables available:
 * - $heading (string)
 * - $text (string)
 */
?>
<div class="osn-statistics-box">
    <?php if ( $heading !== '' ) : ?>
    <div class="osn-statistics-box__heading"><?php echo esc_html( $heading ); ?></div>
    <?php endif; ?>

    <?php if ( $text !== '' ) : ?>
    <div class="osn-statistics-box__text"><?php echo esc_html( $text ); ?></div>
    <?php endif; ?>
</div>
