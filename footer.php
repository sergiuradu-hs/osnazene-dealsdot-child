<?php
/**
 * footer.php
 * @package WordPress
 * @subpackage Dealsdot
 * @since Dealsdot 1.0
 * 
 */
 ?>
		<footer id="footer" class="osn-footer dark">
			<section class="osnazene_section dark">
				<div class="container">
					<?php
						$footer_options = function_exists('get_field') ? get_field('footer_options', 'option') : [];
						$footer_heading = is_array($footer_options) && isset($footer_options['footer_heading']) ? $footer_options['footer_heading'] : '';
						$footer_text = is_array($footer_options) && isset($footer_options['footer_description']) ? $footer_options['footer_description'] : '';
						$footer_btn_text = is_array($footer_options) && isset($footer_options['footer_button_text']) ? $footer_options['footer_button_text'] : '';
						$footer_btn_url = is_array($footer_options) && isset($footer_options['footer_button_url']) ? $footer_options['footer_button_url'] : '';
						$footer_social_text = is_array($footer_options) && isset($footer_options['footer_social_text']) ? $footer_options['footer_social_text'] : '';
						$footersocial = is_array($footer_options) && isset($footer_options['footer_social']) ? $footer_options['footer_social'] : [];
						$paymentimage = is_array($footer_options) && isset($footer_options['footer_payment']) ? $footer_options['footer_payment'] : [];
						$footer_copyright = is_array($footer_options) && isset($footer_options['footer_copyright']) ? $footer_options['footer_copyright'] : [];
					?>
					<!-- <div class="osn-footer__container"> -->
						<div class="osn-footer__row osn-footer__row--top">
							<div class="osn-footer__col osn-footer__col--left">
								<div class="osn-footer__title"><?php echo wp_kses_post( $footer_heading ); ?></div>
								<div class="osn-footer__text"><?php echo wp_kses_post( $footer_text ); ?></div>
								<div class="vc_btn3-container vc_btn3-inline vc_do_btn">
									<a class="btn btn-primary osn-footer__btn" href="<?php echo esc_url( $footer_btn_url ); ?>">
										<?php echo esc_html( $footer_btn_text ); ?>
									</a>
								</div>
							</div>
							<div class="osn-footer__col osn-footer__col--right">
								<div class="osn-footer__menus">
									<div class="osn-footer__menu">
										<?php
										$menu_obj = wp_get_nav_menu_object( 10 );
										if ( $menu_obj && ! empty( $menu_obj->name ) ) {
											echo '<h3>' . esc_html( $menu_obj->name ) . '</h3>';
										}
										?>
										<?php
										wp_nav_menu([
											'menu' => 10,
											'container' => '',
											'menu_class' => 'osn-footer__menu-list',
											'fallback_cb' => false,
										]);
										?>
									</div>
									<div class="osn-footer__menu">
										<?php
										$menu_obj = wp_get_nav_menu_object( 11 );
										if ( $menu_obj && ! empty( $menu_obj->name ) ) {
											echo '<h3>' . esc_html( $menu_obj->name ) . '</h3>';
										}
										?>
										<?php
										wp_nav_menu([
											'menu' => 11,
											'container' => '',
											'menu_class' => 'osn-footer__menu-list',
											'fallback_cb' => false,
										]);
										?>
									</div>
									<div class="osn-footer__menu">
										<?php
										$menu_obj = wp_get_nav_menu_object( 12 );
										if ( $menu_obj && ! empty( $menu_obj->name ) ) {
											echo '<h3>' . esc_html( $menu_obj->name ) . '</h3>';
										}
										?>
										<?php
										wp_nav_menu([
											'menu' => 12,
											'container' => '',
											'menu_class' => 'osn-footer__menu-list',
											'fallback_cb' => false,
										]);
										?>
									</div>
								</div>
							</div>
						</div>

						<div class="osn-footer__row osn-footer__row--social">
							<div class="osn-footer__social-text"><?php echo esc_html( $footer_social_text ); ?></div>
							<div class="osn-footer__social-icons">
								<?php if ( ! empty( $footersocial ) ) : ?>
									<?php $i = 0; foreach ( $footersocial as $f ) : if ( $i >= 3 ) : break; endif; ?>
										<?php
											$icon_url = '';
											if ( is_array( $f['social_icon'] ) && ! empty( $f['social_icon']['url'] ) ) {
												$icon_url = $f['social_icon']['url'];
											} elseif ( is_numeric( $f['social_icon'] ) ) {
												$icon_url = wp_get_attachment_image_url( (int) $f['social_icon'], 'thumbnail' );
											} elseif ( is_string( $f['social_icon'] ) ) {
												$icon_url = $f['social_icon'];
											}
										?>
										<a href="<?php echo esc_url( $f['social_url'] ); ?>" target="_blank" rel="noopener" aria-label="social">
											<?php if ( ! empty( $icon_url ) ) : ?>
												<img src="<?php echo esc_url( $icon_url ); ?>" alt="" class="osn-footer__social-icon-img" />
											<?php endif; ?>
										</a>
									<?php $i++; endforeach; ?>
								<?php else : ?>
									<a href="#" aria-label="facebook">Facebook</a>
									<a href="#" aria-label="instagram">Instagram</a>
									<a href="#" aria-label="linkedin">LinkedIn</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="osn-footer__row osn-footer__row--newsletter osn-hide-above-768">
							<div class="osn-footer__newsletter">
								<h3 class="osn-footer__newsletter-title"><?php esc_html_e( 'Newsletter prijava', 'dealsdot-child' ); ?></h3>
								<p class="osn-footer__newsletter-text"><?php esc_html_e( 'Budite u toku sa najnovijim konkursima, fondovima, događajima i prve saznajte najnovije informacije iz sveta ženskog preduzetništva i savremenog poslovnog sveta', 'dealsdot-child' ); ?></p>
								<?php echo do_shortcode( '[wpb_newsletter_form]' ); ?>
							</div>
						</div>

						<div class="osn-footer__row osn-footer__row--payments">
							<div class="osn-footer__payments">
								<?php if ( ! empty( $paymentimage ) ) : ?>
									<?php foreach ( $paymentimage as $p ) : ?>
										<img src="<?php echo esc_url( dealsdot_get_image( $p['payment_image'] ) ); ?>" alt="<?php esc_attr_e( 'payment', 'dealsdot' ); ?>">
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>

						<div class="osn-footer__row osn-footer__row--copyright">
							<div class="osn-footer__copyright">
								<?php if ( ! empty( $footer_copyright ) ) : ?>
									<?php echo dealsdot_sanitize_data( $footer_copyright ); ?>
								<?php else : ?>
									<?php esc_html_e( 'Copyright 2021.KlbTheme . All rights reserved', 'dealsdot' ); ?>
								<?php endif; ?>
							</div>
						</div>
					<!-- </div> -->
				</div>
			</section>
		</footer>

		<?php $mobilebottommenu = function_exists('get_field') ? get_field('mobile_bottom_menu', 'option') : '0'; ?>
		<?php if($mobilebottommenu == '1') : ?>
			<div class="footer-fix-nav shadow">
				<div class="row mx-0">
					<div class="col col-xs-3">
						<a href="<?php echo esc_url( home_url( "/" ) ); ?>" title="<?php bloginfo("name"); ?>"><i class="fa fa-home"></i></a>
					</div>
					<?php if(is_shop()) : ?>
					<div class="col col-xs-3">
						<a class="button-filter" data-toggle="offcanvas" href="#"><i class="fa fa-filter"></i></a>
					</div>
					<?php else : ?>
					<div class="col col-xs-3">
						<a href="<?php echo wc_get_page_permalink( 'shop' ); ?>"><i class="fa fa-th-large"></i></a>
					</div>
					<?php endif; ?>
					<div class="col col-xs-3">
						<?php global $woocommerce; ?>
						<a href="<?php echo esc_url(wc_get_cart_url()); ?>"><i class="fa fa-shopping-cart"></i><span class="cartcount"><?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'dealsdot'), $woocommerce->cart->cart_contents_count);?></span></a>
					</div>
					<div class="col col-xs-3">
						<a href="<?php echo wc_get_page_permalink( 'myaccount' ); ?>"><i class="fa fa-user"></i></a>
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
	<?php if(get_theme_mod('dealsdot_grid_list_view','0') == '1') : ?>
		<?php if(is_shop()) : ?>
			<?php get_template_part('includes/woocommerce-mobile-filter'); ?> 
		<?php endif; ?>
	<?php endif; ?>
<script>
(function () {
  const header = document.querySelector('header');

  if (!header) {
    return;
  }

  let lastScrollY = window.scrollY;
  let ticking = false;

  const minimumScrollBeforeHide = 120;
  const scrollSensitivity = 8;

  function updateHeader() {
    const currentScrollY = window.scrollY;
    const difference = Math.abs(currentScrollY - lastScrollY);

    if (difference < scrollSensitivity) {
      ticking = false;
      return;
    }

    if (currentScrollY <= minimumScrollBeforeHide) {
      header.classList.remove('is-hidden-on-scroll');
    } else if (currentScrollY > lastScrollY) {
      header.classList.add('is-hidden-on-scroll');
    } else {
      header.classList.remove('is-hidden-on-scroll');
    }

    lastScrollY = currentScrollY;
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(updateHeader);
      ticking = true;
    }
  }, { passive: true });
})();
</script>
	<?php wp_footer(); ?>
	</body>
</html>