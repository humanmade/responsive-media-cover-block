<?php
/**
 * Plugin Name: Responsive Media Cover Block
 * Description: Extends the core cover block to allow for a different image or video for mobile.
 * Author: Human Made Limited
 * Author URI: https://humanmade.com
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HM\ResponsiveMediaCoverBlock;

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\register_block_editor_scripts', 1 );
add_filter( 'render_block_core/cover', __NAMESPACE__ . '\\render_cover_block', 10, 2 );

/**
 * Register block editor scripts.
 */
function register_block_editor_scripts(): void {
	$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php' );

	wp_enqueue_script(
		'responsive-media-cover-block',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version']
	);
};

/**
 * Render the cover block with mobile media.
 *
 * @param string $content The block content.
 * @param array  $block   The block attributes.
 * @return string
 */
function render_cover_block( string $content, array $block ): string {
	if ( is_admin() || empty( $block['attrs']['mobileMediaId'] ) ) {
		return $content;
	}

	$mime_type = get_post_mime_type( $block['attrs']['mobileMediaId'] );
	$type = $mime_type ? preg_replace( '/\/.+$/m', '', $mime_type ) : null;

	if ( ! $type ) {
		return $content;
	}

	ob_start();

	$id = uniqid();
	$content = str_replace( 'class="wp-block-cover ', 'class="wp-block-cover wp-block-cover-' . $id . ' ', $content );

	/**
	 * Filter the breakpoint at which the mobile media is displayed.
	 *
	 * @param string $breakpoint The breakpoint.
	 */
	$breakpoint = apply_filters( 'responsive_media_cover_block_breakpoint', '36rem' );

	/**
	 * Filter the size of the mobile image.
	 *
	 * @param string $size The image size.
	 */
	$size = apply_filters( 'responsive_media_cover_block_mobile_image_size', 'large' );

	?>

	<style>
		.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__image-background.mobile,
		.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__video-background.mobile {
			display: none !important;
		}

		@media only screen and ( max-width: <?php echo esc_html( $breakpoint ); ?> ) {
			.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__image-background:not(.mobile),
			.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__video-background:not(.mobile) {
				display: none !important;
			}

			.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__image-background.mobile,
			.wp-block-cover-<?php echo esc_html( $id ); ?> .wp-block-cover__video-background.mobile {
				display: block !important;
			}
		}
	</style>

	<?php
	if ( $type === 'image' ) {
		echo wp_get_attachment_image( $block['attrs']['mobileMediaId'], $size, false, [
			'class' => 'wp-block-cover__image-background mobile',
		] );
	} elseif ( $type === 'video') {
		$src = wp_get_attachment_url( $block['attrs']['mobileMediaId'] );
		printf(
			'<video class="%s" autoplay muted loop playsinline src="%s" data-object-fit="cover"></video>',
			'wp-block-cover__video-background wp-block-cover__video-background--mobile intrinsic-ignore mobile',
			esc_attr( $src )
		);
	}

	return preg_replace( '/(<div class="wp-block-cover .+?>)/m', '$1' . ob_get_clean(), $content );
}
