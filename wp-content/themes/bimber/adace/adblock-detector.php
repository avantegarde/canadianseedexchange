<?php
/**
 * Adace Adblock Detector.
 *
 * @package adace
 * @subpackage Frontend Slot
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
$title 			= get_option( 'adace_adblock_detector_title', adace_options_get_defaults( 'adace_adblock_detector_title' ) );
$description 	= get_option( 'adace_adblock_detector_description', adace_options_get_defaults( 'adace_adblock_detector_description' ) );
$page 			= get_option( 'adace_adblock_detector_page', adace_options_get_defaults( 'adace_adblock_detector_page' ) );
?>

<div class="adace-popup adace-popup-detector">
	<div class="adace-popup-inner">
		<div class="adace-detector-flag"></div>

		<h2 class="adace-detector-title g1-beta g1-beta-1st"><?php echo esc_html( $title );?></h2>

		<div class="adace-detector-content"><?php echo wp_kses_post( $description );?></div>

		<p class="adace-detector-buttons">
			<?php if ( '-1' !== $page ) :
				$page_url 		= get_the_permalink( $page );
			?>
				<a href="<?php echo esc_url( $page_url );?>" class="adace-detector-button-disable g1-button-solid g1-button g1-button-m"><?php echo esc_html__( 'How to disable?', 'adace' );?></a>
			<?php endif;?>
			<a  class="adace-detector-button-refresh g1-button-simple g1-button g1-button-m"><?php echo esc_html__( 'Refresh', 'adace' );?></a>
		</p>
	</div>

	<div class="adace-popup-background">
	</div>
</div>
