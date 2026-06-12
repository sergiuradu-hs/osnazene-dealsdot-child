<?php
/**
 * Template: Osnažena Card
 * Variables:
 * - $card (array): image_url, name, category
 */
?>
<div class="osn-osnazena-card">
  <div class="osn-osnazena-card__image"<?php echo empty( $card['image_url'] ) ? '' : ' style="background-image:url(' . esc_url( $card['image_url'] ) . ');"'; ?>></div>
  <?php if ( ! empty( $card['name'] ) ) : ?>
    <div class="osn-osnazena-card__name"><?php echo esc_html( $card['name'] ); ?></div>
  <?php endif; ?>
  <?php if ( ! empty( $card['category'] ) ) : ?>
    <div class="osn-osnazena-card__category"><?php echo esc_html( $card['category'] ); ?></div>
  <?php endif; ?>
</div>
