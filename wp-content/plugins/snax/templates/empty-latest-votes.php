<?php
/**
 * Empty state for "Latest votes"
 *
 * @package snax 1.11
 * @subpackage Theme
 */
?>
<div class="snax-empty">
	<div class="snax-empty-icon">
		<?php snax_render_svg( 'empty-state-default', 'default', array(
			'width'     => 100,
			'height'    => 100,
		) ); ?>
	</div>

	<p class="snax-empty-title"><strong><?php esc_html_e( 'No Votes', 'snax' ); ?></strong></p>

	<?php if ( bp_is_my_profile() ) : ?>
		<p class="snax-empty-desc"><?php esc_html_e( 'Vote on a post and it will show up here.', 'snax' ); ?></p>
	<?php else : ?>
		<p class="snax-empty-desc"><?php echo esc_html( sprintf( __( '%s hasn\'t voted on any post yet.', 'snax' ), bp_get_displayed_user_fullname() ) ); ?></p>
	<?php endif; ?>
</div>
