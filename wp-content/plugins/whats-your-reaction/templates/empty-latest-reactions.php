<?php
/**
 * Empty state for "Latest reactions"
 *
 * @package snax 1.11
 * @subpackage Theme
 */
?>
<div class="wyr-empty">
	<div class="wyr-empty-icon">
		<?php wyr_render_svg( 'empty-state-default', 'default', array(
			'width'     => 100,
			'height'    => 100,
		) ); ?>
	</div>

	<p class="wyr-empty-title"><strong><?php esc_html_e( 'No Reactions', 'wyr' ); ?></strong></p>

	<?php if ( bp_is_my_profile() ) : ?>
		<p class="wyr-empty-desc"><?php esc_html_e( 'React on a post and it will show up here.', 'wyr' ); ?></p>
	<?php else : ?>
		<p class="wyr-empty-desc"><?php echo esc_html( sprintf( __( '%s hasn\'t reacted on any post yet.', 'wyr' ), bp_get_displayed_user_fullname() ) ); ?></p>
	<?php endif; ?>
</div>