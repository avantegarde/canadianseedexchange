<?php
/**
 * The Template for displaying row after member header.
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

if ( bimber_can_use_plugin( 'mycred/mycred.php' ) && bimber_mycred_is_addon_enabled( 'ranks' ) ) {
	$bimber_class[] = 'csstodo-with-rank';
}
?>
<div id="item-header-after" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $bimber_class ) ); ?>">
	<div class="g1-row-inner">
		<div class="g1-column">
			<div id="item-header-content">
				<?php
				/**
				 * Fires before the display of the member's header meta.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_before_member_header_meta' ); ?>
				<div id="item-meta">
					<?php
					/**
					 * Fires after the group header actions section.
					 *
					 * If you'd like to show specific profile fields here use:
					 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
					 *
					 * @since 1.2.0
					 */
					do_action( 'bp_profile_header_meta' );
					?>
				</div><!-- #item-meta -->
				<?php
				/**
				 * Fires after the display of the member's header meta.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_after_member_header_meta' ); ?>
			</div><!-- #item-header-content -->
			<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
			<div id="item-buttons" class="g1-dropable">
				<?php
				if ( ! is_user_logged_in() && bimber_can_use_plugin( 'snax/snax.php' ) ) {
					bimber_bp_actions_placeholder();
				} else {
					/**
					 * Fires in the member header actions section.
					 *
					 * @since 1.2.6
					 */
					do_action( 'bp_member_header_actions' );
				}
				?>
			</div><!-- #item-buttons -->

		</div>
	</div>
	<div class="g1-row-background">
	</div>
</div>
