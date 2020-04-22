<?php
/**
 * Gallery
 *
 * @package media-ace
 * @subpackage Templates
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<div class="g1-gallery-wrapper g1-gallery-<?php echo esc_attr( mace_gallery_get_skin() );?>">
	<div class="g1-gallery">
		<div class="g1-gallery-header">
			<div class="g1-gallery-header-left">
				<div class="g1-gallery-logo">
				<?php
				$logo = wp_get_attachment_image_src( mace_gallery_get_logo(), 'full' );
				$logo_hdpi = wp_get_attachment_image_src( mace_gallery_get_logo_hdpi(), 'full' );
				$srcset = '';
				if ( $logo_hdpi ) {
					$srcset = 'srcset="' . $logo_hdpi[0] .' 2x,' . $logo[0] . ' 1x"';
				}
				if ( $logo ) {
					printf( '<img width="%d" height="%d" src="%s" %s>',
						absint( $logo[1] ),
						absint( $logo[2] ),
						esc_attr( $logo[0] ),
						$srcset
					);
				}
				?>
				</div>
				<div class="g1-gallery-title g1-gamma g1-gamma-1st">{title}</div>
			</div>
			<div class="g1-gallery-header-right">
				<div class="g1-gallery-back-to-slideshow"><?php echo esc_html__( 'Back to slideshow', 'mace' );?></div>
				<div class="g1-gallery-thumbs-button"></div>
				<div class="g1-gallery-numerator">{numerator}</div>
				<div class="g1-gallery-close-button"></div>
			</div>
		</div>
		<div class="g1-gallery-body">
			<div class="g1-gallery-frames">
				{frames}
			</div>
			<div class="g1-gallery-thumbnails32">
				<div class="g1-gallery-thumbnails-collection">
					{thumbnails32}
				</div>
			</div>
			<div class="g1-gallery-sidebar">
					<div class="g1-gallery-shares">
					</div>
					<div class="g1-gallery-ad"><?php
							if ( mace_can_use_plugin( 'ad-ace/ad-ace.php' ) && (  adace_is_ad_slot( 'adace-mace-gallery' ) ) ) {
								mace_adace_render_sidebar_ad();

							} ?></div>
					<?php if( 'show' === mace_gallery_get_thumbnails_visibillity() ) :?>
						<div class="g1-gallery-thumbnails">
							<div class="g1-gallery-thumbnails-up"></div>
							<div class="g1-gallery-thumbnails-collection">{thumbnails}</div>
							<div class="g1-gallery-thumbnails-down"></div>
						</div>
					<?php endif;?>
			</div>
		</div>
	</div>
</div>
