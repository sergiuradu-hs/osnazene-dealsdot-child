<?php
/**
 * header.php
 * @package WordPress
 * @subpackage Dealsdot
 * @since Dealsdot 1.0
 * 
 */
 ?>
 
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( "charset" ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (!get_theme_mod( 'dealsdot_loader' )) : ?>
<div id="preloader"></div>
<?php endif; ?>

	<header class="header-style-1">
		<section class="osnazene_section">
			<?php $topheader = get_theme_mod('dealsdot_top_header','0'); ?>
			<?php if($topheader == '1') : ?>
			<div class="top-bar animate-dropdown">
				<div class="container">
					<div class="header-top-inner">
						<div class="cnt-account">
							<?php 
							wp_nav_menu(array(
							'theme_location' => 'top-right-menu',
							'container' => '',
							'fallback_cb' => 'show_top_menu',
							'menu_id' => '',
							'menu_class' => 'list-unstyled',
							'echo' => true,
							'depth' => 0 
								)); 
							?>
						</div>

						<div class="cnt-block">
							<?php 
							wp_nav_menu(array(
							'theme_location' => 'top-left-menu',
							'container' => '',
							'fallback_cb' => 'show_top_menu',
							'menu_id' => '',
							'menu_class' => 'list-unstyled list-inline',
							'echo' => true,
							'walker' => new dealsdot_topleft_walker(),
							'depth' => 0 
								)); 
							?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if(get_theme_mod('dealsdot_sticky_header') == "on") : ?>
				<?php $menutype = 'fixed-menu'?>
			<?php else : ?>
				<?php $menutype = 'static-menu'?>
			<?php endif; ?>

			<div class="main-header <?php echo esc_attr($menutype); ?>">
				<div class="osn-header">
					<div class="osn-header__left logo-holder">
						<div class="logo">
							<?php if (get_theme_mod( 'dealsdot_logo' )) { ?>
								<?php $size = get_theme_mod( 'dealsdot_logo_size', array( 'width' => '140', 'height' => '141') ); ?>
								<a href="<?php echo esc_url( home_url( "/" ) ); ?>" title="<?php bloginfo("name"); ?>">
									<img src="<?php echo esc_url( wp_get_attachment_url(get_theme_mod( 'dealsdot_logo' )) ); ?>" height="<?php echo esc_attr( $size["height"] ); ?>" width="<?php echo esc_attr( $size["width"] ); ?>" alt="<?php bloginfo("name"); ?>"  class="img-fluid">
								</a>
							<?php } elseif (get_theme_mod( 'dealsdot_logo_text' )) { ?>
								<a class="nav-brand text" href="<?php echo esc_url( home_url( "/" ) ); ?>" title="<?php bloginfo("name"); ?>">
									<span><?php echo esc_html(get_theme_mod( 'dealsdot_logo_text' )); ?></span>
								</a>
							<?php } else { ?>
								<a class="nav-brand text" href="<?php echo esc_url( home_url( "/" ) ); ?>" title="<?php bloginfo("name"); ?>">
									<span><?php esc_html_e("Dealsdot","dealsdot"); ?></span>
								</a>
							<?php } ?>
						</div>
					</div>

					<div class="osn-header__right">
						<div class="osn-header__cta">
							<div class="header-cta-buttons">
								<?php 
									$header_options = function_exists('get_field') ? get_field('header_options', 'option') : [];
									$btn1_text   = is_array($header_options) && isset($header_options['header_cta_button_1_text']) ? $header_options['header_cta_button_1_text'] : '';
									$btn1_link   = is_array($header_options) && isset($header_options['header_cta_button_1_link']) ? $header_options['header_cta_button_1_link'] : '';
									$btn1_target = is_array($header_options) && isset($header_options['header_cta_button_1_target']) ? $header_options['header_cta_button_1_target'] : '';

									$btn2_text   = is_array($header_options) && isset($header_options['header_cta_button_2_text']) ? $header_options['header_cta_button_2_text'] : '';
									$btn2_link   = is_array($header_options) && isset($header_options['header_cta_button_2_link']) ? $header_options['header_cta_button_2_link'] : '';
									$btn2_target = is_array($header_options) && isset($header_options['header_cta_button_2_target']) ? $header_options['header_cta_button_2_target'] : '';
								?>
								<?php if ( $btn1_text && $btn1_link ) : ?>
									<a href="<?php echo esc_url( $btn1_link ); ?>" class="btn btn-primary header-cta-btn header-cta-btn-1" <?php echo $btn1_target ? 'target="_blank"' : ''; ?>>
										<?php echo esc_html( $btn1_text ); ?>
									</a>
								<?php else : ?>
									<a href="#" class="btn btn-primary header-cta-btn header-cta-btn-1"><?php esc_html_e('Button 1','dealsdot'); ?></a>
								<?php endif; ?>

								<?php if ( $btn2_text && $btn2_link ) : ?>
									<a href="<?php echo esc_url( $btn2_link ); ?>" class="btn btn-secondary header-cta-btn header-cta-btn-2" <?php echo $btn2_target ? 'target="_blank"' : ''; ?>>
										<?php echo esc_html( $btn2_text ); ?>
									</a>
								<?php else : ?>
									<a href="#" class="btn btn-secondary header-cta-btn header-cta-btn-2"><?php esc_html_e('Button 2','dealsdot'); ?></a>
								<?php endif; ?>
							</div>
						</div>

						<div class="osn-header__nav">
							<div class="yamm navbar navbar-default" role="navigation">
								<div class="navbar-header">
									<button data-target="#mc-horizontal-menu-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> 
									<span class="sr-only"><?php esc_html_e('Toggle navigation','dealsdot'); ?></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
								</div>
								<div class="nav-bg-class">
									<div class="navbar-collapse collapse" id="mc-horizontal-menu-collapse">
										<div class="nav-outer">
											<?php 
											wp_nav_menu(array(
											"theme_location" => "main-menu",
											"container" => "",
											"fallback_cb" => "show_top_menu",
											"menu_id" => "",
											"menu_class" => "nav navbar-nav",
											"echo" => true,
											"walker" => new dealsdot_description_walker(),
											"depth" => 0 
											)); 
											?>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
							</div>
							<?php get_template_part( 'includes/header/cart' ); ?>
						</div>
					</div>
					<button class="osn-burger" type="button" aria-controls="osn-mobile-menu" aria-expanded="false">
						<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'dealsdot' ); ?></span>
						<span class="osn-burger__bar"></span>
						<span class="osn-burger__bar"></span>
						<span class="osn-burger__bar"></span>
					</button>
				</div>
			</div>
		</section>
	</header>
	<?php
	$mobile_footer_options = function_exists( 'get_field' ) ? get_field( 'footer_options', 'option' ) : [];
	$mobile_social_text    = is_array( $mobile_footer_options ) && isset( $mobile_footer_options['footer_social_text'] ) ? $mobile_footer_options['footer_social_text'] : '';
	$mobile_social_links   = is_array( $mobile_footer_options ) && isset( $mobile_footer_options['footer_social'] ) ? $mobile_footer_options['footer_social'] : [];
	?>
	<div class="osn-mobile-menu" id="osn-mobile-menu" aria-hidden="true">
		<nav class="osn-mobile-menu__nav" aria-label="<?php esc_attr_e( 'Mobile menu', 'dealsdot' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'main-menu',
				'container'      => '',
				'fallback_cb'    => 'show_top_menu',
				'menu_id'        => '',
				'menu_class'     => 'osn-mobile-menu__list',
				'depth'          => 2,
			] );
			?>
			<button class="osn-mobile-menu__language-trigger" type="button" aria-controls="osn-mobile-menu-languages" aria-expanded="false" data-theme-uri="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>" hidden>
				<span class="osn-mobile-menu__language-trigger-current">
					<span class="osn-mobile-menu__language-trigger-flag" aria-hidden="true"></span>
					<span class="osn-mobile-menu__language-trigger-label"><?php esc_html_e( 'Language', 'dealsdot' ); ?></span>
				</span>
				<span class="osn-mobile-menu__language-trigger-arrow" aria-hidden="true"></span>
			</button>
		</nav>
		<?php if ( ! empty( $mobile_social_text ) || ! empty( $mobile_social_links ) ) : ?>
		<div class="osn-mobile-menu__social">
			<?php if ( ! empty( $mobile_social_text ) ) : ?>
			<?php endif; ?>
			<?php if ( ! empty( $mobile_social_links ) ) : ?>
			<div class="osn-mobile-menu__social-icons">
				<?php $mobile_social_count = 0; ?>
				<?php foreach ( $mobile_social_links as $mobile_social_link ) : ?>
					<?php
					if ( $mobile_social_count >= 3 ) {
						break;
					}

					$mobile_icon_url = '';
					if ( isset( $mobile_social_link['social_icon'] ) && is_array( $mobile_social_link['social_icon'] ) && ! empty( $mobile_social_link['social_icon']['url'] ) ) {
						$mobile_icon_url = $mobile_social_link['social_icon']['url'];
					} elseif ( isset( $mobile_social_link['social_icon'] ) && is_numeric( $mobile_social_link['social_icon'] ) ) {
						$mobile_icon_url = wp_get_attachment_image_url( (int) $mobile_social_link['social_icon'], 'thumbnail' );
					} elseif ( isset( $mobile_social_link['social_icon'] ) && is_string( $mobile_social_link['social_icon'] ) ) {
						$mobile_icon_url = $mobile_social_link['social_icon'];
					}
					?>
					<a href="<?php echo esc_url( $mobile_social_link['social_url'] ?? '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Social link', 'dealsdot' ); ?>">
						<?php if ( ! empty( $mobile_icon_url ) ) : ?>
						<img src="<?php echo esc_url( $mobile_icon_url ); ?>" alt="" class="osn-mobile-menu__social-icon" />
						<?php endif; ?>
					</a>
					<?php $mobile_social_count++; ?>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<section class="osn-mobile-menu__languages" id="osn-mobile-menu-languages" aria-label="<?php esc_attr_e( 'Choose language', 'dealsdot' ); ?>" hidden>
			<div class="osn-mobile-menu__languages-header">
				<button class="osn-mobile-menu__languages-back" type="button">
					<span aria-hidden="true">&lsaquo;</span>
					<?php esc_html_e( 'Nazad', 'dealsdot' ); ?>
				</button>
			</div>
			<ul class="osn-mobile-menu__languages-list"></ul>
		</section>
	</div>
<div class="body-content" id="top-banner-and-menu">
