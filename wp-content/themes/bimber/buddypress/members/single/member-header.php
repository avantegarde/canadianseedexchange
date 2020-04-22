<?php
/**
 * The Template for displaying member header.
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme 5.4
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

$bimber_class = array(
	'g1-row',
	'g1-row-layout-page',
);
if ( bp_displayed_user_use_cover_image_header() ) {
	$bimber_class[] = 'g1-dark';
}

if ( bimber_can_use_plugin( 'mycred/mycred.php' ) && bimber_mycred_is_addon_enabled( 'ranks' ) ) {
	$bimber_class[] = 'csstodo-with-rank';
}

?>
<div id="item-header" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $bimber_class ) ); ?>">
	<div class="g1-row-inner member-header-wrapper">
		<div class="g1-column  member-header bp-layout-standard">
			<?php
			/**
			 * Fires before the display of a member's header.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_before_member_header' );
			?>
			<div id="item-header-avatar">
				<a href="<?php bp_displayed_user_link(); ?>">
					<?php
						bp_displayed_user_avatar( array(
							'width'     => 160,
							'height'    => 160,
							'type'      => 'full',
						) );
						do_action( 'bimber_buddypress_memebers_after_avatar', bp_displayed_user_id() );
					?>
				</a>
				<?php if ( bimber_bp_show_profile_photo_change_link()  ) : ?>
					<?php bimber_bp_render_profile_photo_change_link(); ?>
				<?php endif; ?>
			</div><!-- #item-header-avatar -->

			<div class="item-header-body">
				<div class="item-header-body-main">
					<h1 class="g1-alpha g1-alpha-1st entry-title"><?php bp_displayed_user_fullname(); ?>
						<sup><?php do_action( 'bimber_buddypress_memebers_after_user_name', bp_displayed_user_id() );?></sup>
					</h1>

					<?php if ( function_exists( 'xprofile_get_field_data' ) ) : ?>
						<?php
							$description = xprofile_get_field_data( bimber_bp_get_short_description_field_id(), bp_displayed_user_id() );
						?>
						<p class="item-header-user-desc">
							<?php echo esc_html( strip_tags( $description ) ); ?>
						</p>
					<?php endif; ?>
				</div>

				<div class="item-header-body-side">
					<?php
						add_filter( 'mycred_bp_profile_header', function( $output ) {
							$output = preg_replace(
                                '/<div class="mycred-balance mycred-mycred_default">[\sa-z:]+([\d\.,]+)/i',
                                '<div class="mycred-balance mycred-mycred_default"><div>' . __( 'Points', 'bimber' ) . '</div> <div class="g1-alpha g1-alpha-1st">\1</div>',
                                $output
							);

							return $output;
						} );

						if ( bimber_can_use_plugin( 'mycred/mycred.php' ) ) {
							$bimber_module = mycred_get_module( 'badges' );
							if ( $bimber_module ) {
								if ( $bimber_module->badges['buddypress'] == 'header' || $bimber_module->badges['buddypress'] == 'both' ) {
									$bimber_module->insert_into_buddypress();
								}
							}

							$bimber_module = mycred_get_module( 'buddypress', MYCRED_DEFAULT_TYPE_KEY );
							if ( $bimber_module ) {
								if ( $bimber_module->buddypress['balance_location'] == 'top' || $bimber_module->buddypress['balance_location'] == 'both' ) {
									$bimber_module->show_balance();
								}
							}
						}
					?>
				</div>
			</div>

			<?php
			/**
			 * Fires after the display of a member's header.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_after_member_header' );
			?>
		</div>
	</div>
	<div class="g1-row-background g1-background-bp-profile">
	</div>
</div><!-- #item-header -->
