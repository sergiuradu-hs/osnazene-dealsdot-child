<?php
/**
 * Template: Latest Posts Three
 * Variables:
 * - $items (array): title, date, url, image_url, image_alt, featured
 */
?>
<?php if ( ! empty( $items ) ) : ?>
<div class="osn-latest-posts-3" role="list">
  <?php foreach ( $items as $item ) : ?>
    <?php
      $card_classes = [
        'osn-latest-posts-3__card',
        ! empty( $item['featured'] ) ? 'osn-latest-posts-3__card--featured' : 'osn-latest-posts-3__card--compact',
      ];
    ?>
  <article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>" role="listitem">

    <?php if ( ! empty( $item['image_url'] ) ) : ?>
      <a class="osn-latest-posts-3__image-link" href="<?php echo esc_url( $item['url'] ); ?>" aria-label="<?php echo esc_attr( $item['title'] ); ?>">
        <img class="osn-latest-posts-3__image" src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['image_alt'] ? $item['image_alt'] : $item['title'] ); ?>" loading="lazy" />
      </a>
    <?php else : ?>
      <a class="osn-latest-posts-3__image-link osn-latest-posts-3__image-link--placeholder" href="<?php echo esc_url( $item['url'] ); ?>" aria-label="<?php echo esc_attr( $item['title'] ); ?>">
        <span>Bez slike</span>
      </a>
    <?php endif; ?>

    <div class="osn-latest-posts-3__date"><?php echo esc_html( $item['date'] ); ?></div>

    <h3 class="osn-latest-posts-3__title">
      <a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
    </h3>

    <a class="osn-latest-posts-3__btn" href="<?php echo esc_url( $item['url'] ); ?>">
      <span>Pročitaj više</span>
    </a>

  </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
