<?php
/**
 * Empty state for "No private collections"
 *
 * @package snax 1.11
 * @subpackage Templates
 */
?>

<div class="snax-empty">
	<div class="snax-empty-icon">
		<?php snax_render_svg( 'empty-state-default', 'default' ); ?>
	</div>

	<h2 class="snax-empty-title"><?php esc_html_e( 'No Private Collections', 'snax' ); ?></h2>

	<p class="snax-empty-desc"><?php esc_html_e( 'Create a private collection and it will show up here.', 'snax' ); ?></p>

	<p class="snax-empty-actions"><a class="g1-button g1-button-simple g1-button-s" href="<?php echo esc_url( snax_get_custom_collection_url() ); ?>"><?php esc_html_e( 'See how it works', 'snax' ); ?></a></p>
</div>
