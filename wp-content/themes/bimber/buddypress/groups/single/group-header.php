<?php
/**
 * The Template for displaying group header.
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
				<a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">
					<?php
					bp_group_avatar( array(
						'width'     => 160,
						'height'    => 160,
						'type'      => 'full',
					) );
					?>
				</a>
				<?php if ( bimber_bp_show_group_photo_change_link()  ) : ?>
					<?php bimber_bp_render_group_photo_change_link(); ?>
				<?php endif; ?>
			</div><!-- #item-header-avatar -->

			<div class="item-header-body">
				<div class="item-header-body-main">
					<h1 class="g1-alpha g1-alpha-1st entry-title"><?php bp_group_name(); ?>
						<sup><?php bp_group_type(); ?></sup>
					</h1>

					<div class="item-header-user-desc">
						<?php bp_group_description(); ?>
					</div>
				</div>

				<div class="item-header-body-side">
					<?php if ( bp_group_is_visible() ) : ?>

						<div class="csstodoziom">
							<h3 class="csstodo g1-epsilon g1-epsilon-1st" style="margin-bottom: 6px;"><?php _e( 'Group Admins', 'buddypress' ); ?></h3>

							<?php bp_group_list_admins();

							/**
							 * Fires after the display of the group's administrators.
							 *
							 * @since 1.1.0
							 */
							do_action( 'bp_after_group_menu_admins' );
						?>
						</div>

						<div class="csstodoziom">
							<?php if ( bp_group_has_moderators() ) :

								/**
								 * Fires before the display of the group's moderators, if there are any.
								 *
								 * @since 1.1.0
								 */
								do_action( 'bp_before_group_menu_mods' ); ?>

								<h3 class="csstodo g1-epsilon g1-epsilon-1st" style="margin-bottom: 6px;"><?php _e( 'Group Mods' , 'buddypress' ); ?></h3>

								<?php bp_group_list_mods();

								/**
								 * Fires after the display of the group's moderators, if there are any.
								 *
								 * @since 1.1.0
								 */
								do_action( 'bp_after_group_menu_mods' );

							endif; ?>
						</div>

					<?php endif; ?>

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
