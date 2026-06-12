<?php
/**
 * archive-osnazena.php — Archive template for the 'osnazena' custom post type.
 * @package WordPress
 * @subpackage Dealsdot Child
 */

get_header(); ?>

<section class="vc_section osnazene_section">
	<div class="container">
    	<h2><?php esc_html_e( 'Osna', 'dealsdot-child' ); ?><strong><?php esc_html_e( 'Žene', 'dealsdot-child' ); ?></strong></h2>
    	<?php echo do_shortcode( '[wpb_clanice_grid]' ); ?>
	</div>
</section>

<?php
// Intentionally no WP loop — the clanice-grid shortcode handles its own query.
// This block exists only as a placeholder so that get_header()/get_footer() work.
?>

<?php
// Legacy template structure kept below — unreachable but left for reference.
if ( false ) { ?>
<div class="body-content blog-page outer-top-ts">
	<div class="container">
		<div class="row">
			<?php if( get_theme_mod( 'dealsdot_blog_layout' ) == 'left-sidebar') { ?>
				<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
					<div class="sidebar-module-container">
						<?php if ( is_active_sidebar( 'blog-sidebar' ) ) { ?>
							<?php dynamic_sidebar( 'blog-sidebar' ); ?>
						<?php } ?>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-9 klb-blog-list">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<?php  get_template_part( 'post-format/content', get_post_format() ); ?>

						<?php endwhile; ?>
							
							<?php get_template_part( 'post-format/pagination' ); ?>
							
						<?php else : ?>

							<h2><?php esc_html_e('No Posts Found', 'dealsdot') ?></h2>

						<?php endif; ?>
				</div>
			<?php } elseif( get_theme_mod( 'dealsdot_blog_layout' ) == 'full-width') { ?>
				<div class="col-md-10 col-md-offset-1 klb-blog-list">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<?php  get_template_part( 'post-format/content', get_post_format() ); ?>

					<?php endwhile; ?>
						
						<?php get_template_part( 'post-format/pagination' ); ?>
						
					<?php else : ?>

						<h2><?php esc_html_e('No Posts Found', 'dealsdot') ?></h2>

					<?php endif; ?>
				</div>
			<?php } else { ?>
				<?php if ( is_active_sidebar( 'blog-sidebar' ) ) { ?>
					<div class="col-xs-12 col-sm-12 col-md-9 klb-blog-list">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<?php  get_template_part( 'post-format/content', get_post_format() ); ?>

						<?php endwhile; ?>
							
							<?php get_template_part( 'post-format/pagination' ); ?>
							
						<?php else : ?>

							<h2><?php esc_html_e('No Posts Found', 'dealsdot') ?></h2>

						<?php endif; ?>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-3 sidebar klb-sidebar">
						<div class="sidebar-module-container">
							<?php if ( is_active_sidebar( 'blog-sidebar' ) ) { ?>
								<?php dynamic_sidebar( 'blog-sidebar' ); ?>
							<?php } ?>
						</div>
					</div>
				<?php } else { ?>
					<div class="col-md-10 col-md-offset-1 klb-blog-list">
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<?php  get_template_part( 'post-format/content', get_post_format() ); ?>

						<?php endwhile; ?>
							
							<?php get_template_part( 'post-format/pagination' ); ?>
							
						<?php else : ?>

							<h2><?php esc_html_e('No Posts Found', 'dealsdot') ?></h2>

						<?php endif; ?>
					</div>
				<?php } ?>
			
			<?php } ?>
		</div>
	</div>
</div>
<?php } // end if(false) ?>

<?php get_footer(); ?>