<?php
/**
 * Snax Collection Edit Form Template Part
 *
 * @package snax 1.11
 * @subpackage Theme
 */
// Load dependencies, on demand.
add_action( 'wp_footer', 'snax_render_feedback' );

$snax_collection = snax_get_collection_by_id( get_the_ID() );
?>
<form class="snax-edit-collection" data-snax-collection="<?php echo absint( get_the_ID() ); ?>">

	<div class="snax-edit-collection-main">
		<div class="snax-form-row snax-edit-collection-row-title">
			<label><?php esc_html_e( 'Title', 'snax' ); ?></label>
			<input type="text" name="snax-title" required value="<?php echo esc_attr( $snax_collection->get_title() ); ?>" />
		</div>

		<div class="snax-form-row snax-edit-collection-row-description">
			<label><?php esc_html_e( 'Description', 'snax' ); ?></label>
			<textarea name="snax-description" rows="4" cols="50" placeholder="<?php esc_attr_e( 'Describe your collection&hellip;', 'snax' ); ?>"><?php echo esc_textarea( $snax_collection->get_description() ); ?></textarea>
		</div>

		<div class="snax-form-row snax-edit-collection-row-visibility">
			<fieldset>
				<legend><?php esc_html_e( 'Visibility', 'snax' ); ?></legend>

				<label><input name="snax-visibility" value="public" type="radio"<?php checked( $snax_collection->get_visibility(), 'public' ); ?> /> <?php echo esc_html_x( 'Public', 'collection visibility', 'snax' ); ?>
				<label><input name="snax-visibility" value="private" type="radio"<?php checked( $snax_collection->get_visibility(), 'private' ); ?> /> <?php echo esc_html_x( 'Private', 'collection visibility', 'snax' ); ?>
			</fieldset>
		</div>
	</div>

	<div class="snax-edit-collection-side">
		<div class="snax-form-row snax-edit-collection-row-media">
			<label><?php esc_html_e( 'Image', 'snax' ); ?></label>
			<?php
			// @todo - refactor.
			global $snax_featured_image;
			global $snax_post_id;
			global $snax_set_as_featured;
			global $snax_source_form;

			$snax_post_id           = get_the_ID();
			$snax_featured_image    = get_post_thumbnail_id();
			$snax_set_as_featured   = false;
			$snax_source_form       = 'collection_edit_form';

			snax_frontend_submission_render_scripts();
			snax_get_template_part( 'posts/form-edit/row-featured-image' );
			?>
			<div class="snax-object-actions">
				<a href="#" class="snax-object-action snax-delete-collection-image" title="<?php esc_attr_e( 'Delete', 'snax' ); ?>"><?php esc_html_e( 'Delete', 'snax' ); ?></a>
			</div>

			<input type="hidden" name="snax-featured-image" value="<?php echo $snax_featured_image ? absint( $snax_featured_image ) : ''; ?>" />
		</div>
	</div>

	<?php snax_get_template_part( 'collections/form-edit-actions' ); ?>
</form>
