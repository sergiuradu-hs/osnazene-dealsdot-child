<?php
/**
 * Template: Država Tax Grid
 * Variables:
 * - $grid (array): columns, box_size, items[] (name, link, zastava_url, img_url)
 */
?>
<div class="osn-tax-grid" style="--osn-tax-columns: <?php echo (int)$grid['columns']; ?>; --osn-tax-box-size: <?php echo (int)$grid['box_size']; ?>px;">
  <?php foreach ( $grid['items'] as $item ) : ?>
    <a class="osn-tax-grid-item" href="<?php echo esc_url( $item['link'] ); ?>" title="<?php echo esc_attr( $item['name'] ); ?>"<?php echo empty( $item['img_url'] ) ? '' : ' style="background-image:url(' . esc_url( $item['img_url'] ) . ');"'; ?>>
      <span class="osn-tax-grid-item__label">
        <?php if ( ! empty( $item['zastava_url'] ) ) : ?>
          <img class="osn-tax-grid-item__zastava" src="<?php echo esc_url( $item['zastava_url'] ); ?>" alt="" />
        <?php endif; ?>
        <span class="osn-tax-grid-item__text"><?php echo esc_html( $item['name'] ); ?></span>
      </span>
      <span class="osn-tax-grid-item__chevron" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
      </span>
    </a>
  <?php endforeach; ?>
</div>
