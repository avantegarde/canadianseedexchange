<?php
/**
 * Empty state for "No reaction"
 *
 * @package whats-your-reaction
 * @subpackage Templates
 */
?>
<div class="wyr-empty">
	<div class="wyr-empty-icon">
		<?php wyr_render_svg( 'empty-state-default', 'default' ); ?>
	</div>

	<h2 class="wyr-empty-title"><?php esc_html_e( 'No Reactions', 'wyr' ); ?></h2>

	<?php if ( bp_is_my_profile() ) : ?>
		<p class="wyr-empty-desc"><?php esc_html_e( 'React on a post and it will show up here.', 'wyr' ); ?></p>
	<?php else : ?>
		<p class="wyr-empty-desc"><?php echo esc_html( sprintf( __( '%s hasn\'t reacted on any post yet.', 'wyr' ), bp_get_displayed_user_fullname() ) ); ?></p>
	<?php endif; ?>
</div>
