<?php
/**
 * Image Carousel — template
 *
 * Variables available from element.php render closure:
 *   $atts['images']        — comma-separated attachment IDs
 *   $atts['height']        — image height in px (default 125)
 *   $atts['mobile_height'] — image height in px below 768px (empty = same as height)
 *   $atts['speed']         — scroll speed in px/s (default 80)
 */

$image_ids     = array_filter( array_map( 'intval', explode( ',', $atts['images'] ) ) );
$height        = max( 1, (int) $atts['height'] );
$mobile_height = max( 0, (int) $atts['mobile_height'] );
$speed         = max( 1, (int) $atts['speed'] );

if ( empty( $image_ids ) ) {
	return;
}

$images = [];
foreach ( $image_ids as $id ) {
	$src = wp_get_attachment_image_src( $id, 'full' );
	if ( ! $src ) {
		continue;
	}
	[ $url, $orig_w, $orig_h ] = $src;
	$alt         = get_post_meta( $id, '_wp_attachment_image_alt', true );
	$scaled_w    = $orig_h > 0 ? (int) round( $orig_w * $height / $orig_h ) : $height;
	$images[]    = [
		'url'    => $url,
		'alt'    => $alt,
		'width'  => $scaled_w,
		'height' => $height,
	];
}

if ( empty( $images ) ) {
	return;
}
?>
<div
	class="osn-image-carousel<?php echo $mobile_height > 0 ? ' osn-image-carousel--mobile-height' : ''; ?>"
	data-speed="<?php echo esc_attr( $speed ); ?>"
	<?php echo $mobile_height > 0 ? 'style="--osn-carousel-mobile-height: ' . (int) $mobile_height . 'px"' : ''; ?>
>
	<div class="osn-image-carousel__track">
		<?php foreach ( $images as $image ) : ?>
			<img
				class="osn-image-carousel__item"
				src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( $image['alt'] ); ?>"
				width="<?php echo esc_attr( $image['width'] ); ?>"
				height="<?php echo esc_attr( $image['height'] ); ?>"
			/>
		<?php endforeach; ?>
	</div>
</div>
