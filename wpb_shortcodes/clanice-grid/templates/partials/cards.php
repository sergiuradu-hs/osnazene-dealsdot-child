<?php
/**
 * Partial: cards + pagination
 * Used for both the initial render (inside template.php) and AJAX responses.
 *
 * Expected variables: $posts, $total, $pages, $current_page
 */

$card_partial = get_stylesheet_directory() . '/wpb_shortcodes/clanice-grid/templates/partials/card-single.php';
?>
<div class="osn-clanice-grid__count">
    <?php
    /* translators: %s: number of members */
    echo esc_html( sprintf( _n( '%s članica', '%s članica', $total, 'dealsdot-child' ), number_format_i18n( $total ) ) );
    ?>
</div>

<div class="osn-clanice-grid__cards-wrap">
    <?php if ( ! empty( $posts ) ) : ?>
        <?php foreach ( $posts as $post_obj ) : ?>
            <?php include $card_partial; ?>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="osn-clanice-grid__empty"><?php esc_html_e( 'Nema rezultata za odabrane filtere.', 'dealsdot-child' ); ?></p>
    <?php endif; ?>
</div>

<?php if ( $pages > 1 ) : ?>
<nav class="osn-clanice-grid__pagination" aria-label="<?php esc_attr_e( 'Stranice članica', 'dealsdot-child' ); ?>">
    <?php
    // Prev button
    $prev_disabled = $current_page <= 1 ? ' disabled' : '';
    echo '<button class="osn-clanice-pag__btn osn-clanice-pag__btn--prev"'
        . ' data-page="' . max( 1, $current_page - 1 ) . '"'
        . $prev_disabled . ' aria-label="Prethodna stranica">'
        . '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>'
        . '</button>';

    // Page numbers with ellipsis
    $window = 2; // pages to show around current
    for ( $p = 1; $p <= $pages; $p++ ) {
        if (
            $p === 1 ||
            $p === $pages ||
            ( $p >= $current_page - $window && $p <= $current_page + $window )
        ) {
            $active = ( $p === $current_page ) ? ' is-active' : '';
            echo '<button class="osn-clanice-pag__btn osn-clanice-pag__num' . $active . '" data-page="' . $p . '">'
                . $p
                . '</button>';
        } elseif (
            $p === $current_page - $window - 1 ||
            $p === $current_page + $window + 1
        ) {
            echo '<span class="osn-clanice-pag__ellipsis">…</span>';
        }
    }

    // Next button
    $next_disabled = $current_page >= $pages ? ' disabled' : '';
    echo '<button class="osn-clanice-pag__btn osn-clanice-pag__btn--next"'
        . ' data-page="' . min( $pages, $current_page + 1 ) . '"'
        . $next_disabled . ' aria-label="Sledeća stranica">'
        . '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>'
        . '</button>';
    ?>
</nav>
<?php endif; ?>
