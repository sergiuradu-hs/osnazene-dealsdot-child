<?php
/**
 * Template: Link List
 *
 * Variables available (set in element.php render callback):
 *   $items – array of [ 'label' => string, 'url' => string, 'target' => string ]
 */

if ( empty( $items ) ) {
    return;
}
?>
<ul class="osn-link-list">
    <?php foreach ( $items as $item ) : ?>
    <li class="osn-link-list__item">
        <img class="osn-link-list__icon"
             src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/link_icon.svg' ); ?>"
             alt=""
             aria-hidden="true"
             width="24"
             height="24">
        <?php if ( ! empty( $item['url'] ) ) : ?>
        <a class="osn-link-list__label"
           href="<?php echo esc_url( $item['url'] ); ?>"
           <?php if ( $item['target'] ) : ?>
           target="<?php echo esc_attr( $item['target'] ); ?>"
           rel="noopener noreferrer"
           <?php endif; ?>>
            <?php echo esc_html( $item['label'] ); ?>
        </a>
        <?php else : ?>
        <span class="osn-link-list__label"><?php echo esc_html( $item['label'] ); ?></span>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
