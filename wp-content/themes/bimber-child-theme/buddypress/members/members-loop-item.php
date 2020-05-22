<?php
/**
 * BuddyPress - Members Loop item
 *
 * @package Bimber
 */

 
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>


<div class="g1-members-item">
	<?php if ( bp_displayed_user_use_cover_image_header() ) : ?>
		<?php
		$bimber_image_url = bp_attachments_get_attachment('url', array(
			'object_dir'    => 'members',
			'item_id'       => bp_get_member_user_id(),
		));

		// Default cover image fallback.
		if ( empty( $bimber_image_url ) ) {
			$bimber_settings  = bp_attachments_get_cover_image_settings();
			$bimber_image_url = $bimber_settings['default_cover'];
		}
		?>
		<a
			class="item-cover"
			href="<?php echo esc_url( bp_get_member_permalink() ); ?>"
			title="<?php echo esc_attr( sprintf( __( 'Profile page of %s', 'bimber'), apply_filters( 'bp_member_name', bp_get_member_name() ) ) ); ?>"
		>
			<?php echo apply_filters( 'bimber_buddypress_members_cover_image', '<img src="' . esc_url( $bimber_image_url ) . '" width="160" height="90" alt="" />' ); ?>
		</a>
	<?php endif; ?>

	<div class="item-avatar">
		<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(array(
				'width'     => 80,
				'height'    => 80,
				'type'      => 'full',
			));
			do_action( 'bimber_buddypress_memebers_after_avatar', bp_get_member_user_id() );
			?>
		</a>
	</div>

	<div class="g1-gamma g1-gamma-1st entry-title item-title">
		<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
	</div>

	<?php if ( function_exists( 'xprofile_get_field_data' ) ) : ?>
	<div class="item-desc">
		<?php
		$bimber_description = xprofile_get_field_data( bimber_bp_get_short_description_field_id(), bp_get_member_user_id() );
		echo esc_html( strip_tags( $bimber_description ) );?>
	</div>
	<?php endif; ?>

	<div class="item-extras">
		<?php
			/**
			 * Fires inside the display of a directory member item.
			 *
			 * @since 1.1.0
		`	 */
			do_action( 'bp_directory_members_item' );
		?>
	</div>
</div>
