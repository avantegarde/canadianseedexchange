<?php
/**
 * Empty state for "No upvotes collections"
 *
 * @package snax 1.11
 * @subpackage Templates
 */
?>

<div class="snax-empty">
	<div class="snax-empty-icon">
		<?php snax_render_svg( 'empty-state-default', 'default' ); ?>
	</div>

	<h2 class="snax-empty-title"><?php esc_html_e( 'No Upvotes', 'snax' ); ?></h2>

	<?php if ( bp_is_my_profile() ) : ?>
		<p class="snax-empty-desc"><?php esc_html_e( 'Upvote a post and it will show up here.', 'snax' ); ?></p>
	<?php else : ?>
		<p class="snax-empty-desc"><?php echo esc_html( sprintf( __( '%s hasn\'t upvoted any post yet.', 'snax' ), bp_get_displayed_user_fullname() ) ); ?></p>
	<?php endif; ?>
</div>
