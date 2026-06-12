<?php
/**
 * Template Name: Događaji
 *
 * Page template that lists all posts of the `dogadjaj` custom post type
 * (registered via JetEngine), with the same card layout as the generic
 * archive template, and standard WordPress pagination.
 */

get_header();

$paged   = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$per_page = (int) get_option( 'posts_per_page' );

$query = new WP_Query( [
    'post_type'      => 'dogadjaj',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
?>

<section class="vc_section osnazene_section osn-arc">
    <div class="container">
        <?php if ( $query->have_posts() ) : ?>
        <div class="osn-arc__grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
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
            <?php wp_reset_postdata(); ?>
        </div>

        <?php
        $pagination = paginate_links( [
            'base'      => get_pagenum_link( 1 ) . '%_%',
            'format'    => 'page/%#%/',
            'current'   => $paged,
            'total'     => $query->max_num_pages,
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
