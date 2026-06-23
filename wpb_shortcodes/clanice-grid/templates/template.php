<?php
/**
 * Template: Članice Grid
 *
 * Variables available:
 *   $instance_id      – unique HTML id prefix
 *   $per_page         – int
 *   $drzava_terms     – array of WP_Term
 *   $delatnost_terms  – array of WP_Term
 *   $posts            – array of WP_Post (initial page)
 *   $total            – int
 *   $pages            – int
 *   $current_page     – int
 */
?>
<div class="osn-clanice-grid"
     id="<?php echo esc_attr( $instance_id ); ?>"
     data-per-page="<?php echo esc_attr( $per_page ); ?>">

    <!-- ============================================================
         MOBILE TOOLBAR (hidden on desktop)
    ============================================================ -->
    <div class="osn-clanice-grid__mobile-toolbar">
        <div class="osn-clanice-grid__mobile-search">
            <label for="<?php echo esc_attr( $instance_id ); ?>-mob-ime" class="osn-clanice-grid__filter-label">
                <?php esc_html_e( 'Pretraga po imenu:', 'dealsdot-child' ); ?>
            </label>
            <div class="osn-clanice-grid__input-wrap">
                <input type="search"
                       id="<?php echo esc_attr( $instance_id ); ?>-mob-ime"
                       class="osn-clanice-grid__input osn-clanice-grid__input--ime"
                       name="ime"
                       placeholder="<?php esc_attr_e( 'Pretraga po imenu i prezimenu…', 'dealsdot-child' ); ?>"
                       autocomplete="off" />
                <button type="button"
                        class="osn-clanice-grid__input-clear osn-clanice-grid__input-clear--ime"
                        aria-label="<?php esc_attr_e( 'Obriši pretragu po imenu', 'dealsdot-child' ); ?>"
                        hidden>
                    &times;
                </button>
            </div>
        </div>
        <button class="osn-clanice-grid__filter-toggle" aria-expanded="false"
                aria-controls="<?php echo esc_attr( $instance_id ); ?>-modal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3 6h18M7 12h10M11 18h2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <?php esc_html_e( 'Filteri', 'dealsdot-child' ); ?>
        </button>
    </div>

    <div class="osn-clanice-grid__toolbar-bottom">
        <div class="osn-clanice-grid__result-count-mob"></div>
        <div class="osn-clanice-grid__view-toggle" role="group" aria-label="Prikaz">
            <button class="osn-clanice-grid__view-btn is-active" data-view="grid" aria-pressed="true" aria-label="Mrežni prikaz">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="3" y="3" width="8" height="8" rx="1" fill="currentColor"/>
                    <rect x="13" y="3" width="8" height="8" rx="1" fill="currentColor"/>
                    <rect x="3" y="13" width="8" height="8" rx="1" fill="currentColor"/>
                    <rect x="13" y="13" width="8" height="8" rx="1" fill="currentColor"/>
                </svg>
            </button>
            <button class="osn-clanice-grid__view-btn" data-view="list" aria-pressed="false" aria-label="Listni prikaz">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="4" rx="1" fill="currentColor"/>
                    <rect x="3" y="10" width="18" height="4" rx="1" fill="currentColor"/>
                    <rect x="3" y="16" width="18" height="4" rx="1" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- ============================================================
         LAYOUT: sidebar (desktop) + results area
    ============================================================ -->
    <div class="osn-clanice-grid__layout">

        <!-- SIDEBAR: desktop filters (hidden on mobile) -->
        <aside class="osn-clanice-grid__sidebar" aria-label="Filteri">
            <form class="osn-clanice-grid__filter-form" data-instance="<?php echo esc_attr( $instance_id ); ?>" novalidate>
                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-ime" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po imenu:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__input-wrap">
                        <input type="search"
                               id="<?php echo esc_attr( $instance_id ); ?>-ime"
                               class="osn-clanice-grid__input osn-clanice-grid__input--ime"
                               name="ime"
                               placeholder="<?php esc_attr_e( 'Pretraga po imenu i prezimenu…', 'dealsdot-child' ); ?>"
                               autocomplete="off" />
                        <button type="button"
                                class="osn-clanice-grid__input-clear osn-clanice-grid__input-clear--ime"
                                aria-label="<?php esc_attr_e( 'Obriši pretragu po imenu', 'dealsdot-child' ); ?>"
                                hidden>
                            &times;
                        </button>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-drzava" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po državi:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__select-wrap">
                        <select id="<?php echo esc_attr( $instance_id ); ?>-drzava"
                                class="osn-clanice-grid__select osn-clanice-grid__select--drzava"
                                name="drzava">
                            <option value=""><?php esc_html_e( '— sve države —', 'dealsdot-child' ); ?></option>
                            <?php foreach ( $drzava_terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="osn-clanice-grid__select-arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-mesto-search" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po mestu:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__input-wrap osn-clanice-grid__input-wrap--suggestions">
                        <input type="search"
                               id="<?php echo esc_attr( $instance_id ); ?>-mesto-search"
                               class="osn-clanice-grid__input osn-clanice-grid__input--mesto"
                               name="mesto_search"
                               placeholder="<?php esc_attr_e( 'Pretraga po mestu…', 'dealsdot-child' ); ?>"
                               autocomplete="off"
                               aria-autocomplete="list" />
                        <input type="hidden" name="mesto" value="" />
                        <div class="osn-clanice-grid__suggestions osn-clanice-grid__suggestions--mesto" hidden></div>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-delatnost" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po delatnosti:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__select-wrap">
                        <select id="<?php echo esc_attr( $instance_id ); ?>-delatnost"
                                class="osn-clanice-grid__select osn-clanice-grid__select--delatnost"
                                name="delatnost">
                            <option value=""><?php esc_html_e( '— sve delatnosti —', 'dealsdot-child' ); ?></option>
                            <?php foreach ( $delatnost_terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="osn-clanice-grid__select-arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

            </form>
        </aside>

        <!-- RESULTS: count + cards + pagination -->
        <div class="osn-clanice-grid__results" aria-live="polite" aria-atomic="true">
            <?php
            include get_stylesheet_directory() . '/wpb_shortcodes/clanice-grid/templates/partials/cards.php';
            ?>
        </div>

    </div><!-- /.osn-clanice-grid__layout -->

    <!-- ============================================================
         MOBILE FILTER MODAL
    ============================================================ -->
    <div class="osn-clanice-grid__modal"
         id="<?php echo esc_attr( $instance_id ); ?>-modal"
         role="dialog"
         aria-modal="true"
         aria-label="<?php esc_attr_e( 'Filteri', 'dealsdot-child' ); ?>"
         hidden>
        <div class="osn-clanice-grid__modal-backdrop"></div>
        <div class="osn-clanice-grid__modal-panel">
            <div class="osn-clanice-grid__modal-header">
                <span class="osn-clanice-grid__modal-handle" aria-hidden="true"></span>
            </div>

            <form class="osn-clanice-grid__filter-form osn-clanice-grid__filter-form--modal"
                  data-instance="<?php echo esc_attr( $instance_id ); ?>" novalidate>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-mob-ime-modal" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po imenu:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__input-wrap">
                        <input type="search"
                               id="<?php echo esc_attr( $instance_id ); ?>-mob-ime-modal"
                               class="osn-clanice-grid__input osn-clanice-grid__input--ime"
                               name="ime"
                               placeholder="<?php esc_attr_e( 'Pretraga po imenu i prezimenu…', 'dealsdot-child' ); ?>"
                               autocomplete="off" />
                        <button type="button"
                                class="osn-clanice-grid__input-clear osn-clanice-grid__input-clear--ime"
                                aria-label="<?php esc_attr_e( 'Obriši pretragu po imenu', 'dealsdot-child' ); ?>"
                                hidden>
                            &times;
                        </button>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-mob-drzava" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po državi:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__select-wrap">
                        <select id="<?php echo esc_attr( $instance_id ); ?>-mob-drzava"
                                class="osn-clanice-grid__select osn-clanice-grid__select--drzava"
                                name="drzava">
                            <option value=""><?php esc_html_e( '— sve države —', 'dealsdot-child' ); ?></option>
                            <?php foreach ( $drzava_terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="osn-clanice-grid__select-arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-mob-mesto-search" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po mestu:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__input-wrap osn-clanice-grid__input-wrap--suggestions">
                        <input type="search"
                               id="<?php echo esc_attr( $instance_id ); ?>-mob-mesto-search"
                               class="osn-clanice-grid__input osn-clanice-grid__input--mesto"
                               name="mesto_search"
                               placeholder="<?php esc_attr_e( 'Pretraga po mestu…', 'dealsdot-child' ); ?>"
                               autocomplete="off"
                               aria-autocomplete="list" />
                        <input type="hidden" name="mesto" value="" />
                        <div class="osn-clanice-grid__suggestions osn-clanice-grid__suggestions--mesto" hidden></div>
                    </div>
                </div>

                <div class="osn-clanice-grid__filter-field">
                    <label for="<?php echo esc_attr( $instance_id ); ?>-mob-delatnost" class="osn-clanice-grid__filter-label">
                        <?php esc_html_e( 'Pretraga po delatnosti:', 'dealsdot-child' ); ?>
                    </label>
                    <div class="osn-clanice-grid__select-wrap">
                        <select id="<?php echo esc_attr( $instance_id ); ?>-mob-delatnost"
                                class="osn-clanice-grid__select osn-clanice-grid__select--delatnost"
                                name="delatnost">
                            <option value=""><?php esc_html_e( '— sve delatnosti —', 'dealsdot-child' ); ?></option>
                            <?php foreach ( $delatnost_terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->term_id ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="osn-clanice-grid__select-arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                <div class="osn-clanice-grid__modal-actions">
                    <button type="button" class="osn-clanice-grid__modal-reset btn-secondary">
                        <?php esc_html_e( 'Poništi', 'dealsdot-child' ); ?>
                    </button>
                    <button type="submit" class="osn-clanice-grid__modal-apply btn-primary">
                        <?php esc_html_e( 'Primeni filtere', 'dealsdot-child' ); ?>
                    </button>
                </div>
            </form>
        </div>
    </div><!-- /.osn-clanice-grid__modal -->

</div><!-- /.osn-clanice-grid -->
