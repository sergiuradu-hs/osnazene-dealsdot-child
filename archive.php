<?php
/**
 * Generic archive template.
 * Used for any post type / taxonomy archive that does not have its own template.
 *
 * Layout (white bg, padding 80px 128px):
 *   - Gold italic archive heading + optional description
 *   - 3-column card grid (image, date, title, "Pročitaj više" button)
 *   - Paginated navigation
 */

get_header();
?>

<section class="vc_section osnazene_section osn-arc">
    <div class="container">
        <?php /* ----------------------------------------------------------------
        * Heading + description
        * -------------------------------------------------------------- */
        $heading     = '';
        $description = '';

        if ( is_post_type_archive() ) {
            $heading     = post_type_archive_title( '', false );
            $pt_obj      = get_queried_object();
            $description = isset( $pt_obj->description ) ? $pt_obj->description : '';
        } elseif ( is_tax() || is_category() || is_tag() ) {
            $term        = get_queried_object();
            $heading     = $term->name ?? '';
            $description = term_description();
        } elseif ( is_home() || is_archive() ) {
            $heading = __( 'Blog', 'dealsdot-child' );
        }
        ?>

        <?php if ( $heading || $description ) : ?>
        <div class="osn-arc__header">
            <?php if ( $heading ) : ?>
            <h1 class="osn-arc__heading"><?php echo esc_html( $heading ); ?></h1>
            <?php endif; ?>
            <?php if ( $description ) : ?>
            <div class="osn-arc__description"><?php echo wp_kses_post( $description ); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php /* ----------------------------------------------------------------
        * Card grid
        * -------------------------------------------------------------- */
        if ( have_posts() ) : ?>
        <div class="osn-arc__grid margin-top-60">
            <?php while ( have_posts() ) : the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="osn-arc__card">
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="osn-arc__card-img">
                    <?php the_post_thumbnail( 'large', [ 'loading' => 'lazy' ] ); ?>
                </div>
                <?php endif; ?>

                <div class="osn-arc__card-date"><?php echo get_the_date( 'd.m.Y.' ); ?></div>

                <h2 class="osn-arc__card-title"><?php the_title(); ?></h2>

                <span class="osn-arc__card-btn">Pročitaj više</span>
            </a>
            <?php endwhile; ?>
        </div>

        <?php /* ----------------------------------------------------------------
        * Pagination
        * -------------------------------------------------------------- */
        $pagination = paginate_links( [
            'prev_text' => '&#8249;',
            'next_text' => '&#8250;',
            'type'      => 'array',
        ] );

        if ( $pagination ) : ?>
        <nav class="osn-arc__pagination" aria-label="<?php esc_attr_e( 'Stranice', 'dealsdot-child' ); ?>">
            <?php foreach ( $pagination as $page_link ) : ?>
                <?php echo $page_link; ?>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <p class="osn-arc__empty"><?php esc_html_e( 'Nema objava.', 'dealsdot-child' ); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
