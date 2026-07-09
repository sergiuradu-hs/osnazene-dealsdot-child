<?php
/**
 * Generic single post template.
 * Used for any post type that does not have its own single-{post_type}.php.
 *
 * Layout (white bg, padding 80px 128px):
 *   - Gold italic post title (full-width, centered)
 *   - Two-column row:
 *       Left  (465px): info card – featured image, date, title
 *       Right (flex 1): the_content()
 */

get_header();

while ( have_posts() ) : the_post();

    $img_url  = get_the_post_thumbnail_url( get_the_ID(), 'large' );
    $date_str = get_the_date( 'd.m.Y.' );
?>

<section class="vc_section osnazene_section osn-sp">
    <div class="container">
        <h1 class="osn-sp__title margin-bottom-60"><?php the_title(); ?></h1>

        <div class="osn-sp__layout">

            <!-- Left info card -->
            <aside class="osn-sp__sidebar">
                <div class="osn-sp__card">
                    <?php if ( $img_url ) : ?>
                    <div class="osn-sp__img-wrap">
                        <img src="<?php echo esc_url( $img_url ); ?>"
                            alt="<?php echo esc_attr( get_the_title() ); ?>"
                            loading="eager">
                    </div>
                    <?php endif; ?>

                    <?php if ( $date_str ) : ?>
                    <div class="osn-sp__date"><?php echo esc_html( $date_str ); ?></div>
                    <?php endif; ?>

                    <h2 class="osn-sp__card-title"><?php the_title(); ?></h2>
                </div>
            </aside>

            <!-- Post content -->
            <div class="osn-sp__content">
                <?php the_content(); ?>
            </div>

        </div>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
