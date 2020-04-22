<?php
/**
 * Snax Post Row Featured Image
 *
 * @package snax 1.11
 * @subpackage Theme
 */

// @todo - refactor!
global $snax_post_format;
global $snax_post_id;
global $snax_featured_image;
global $snax_set_as_featured;
global $snax_source_form;

if ( ! isset( $snax_set_as_featured ) ) {
	$snax_set_as_featured = true;
}

if ( ! isset( $snax_source_form ) ) {
	$snax_source_form = '';
}

$snax_user_id = get_current_user_id();

if ( empty( $snax_post_id ) ) {
	$snax_post_id = (int) filter_input( INPUT_GET, snax_get_url_var( 'post' ), FILTER_SANITIZE_NUMBER_INT );
}

if ( empty( $snax_featured_image ) ) {
	$snax_featured_image = snax_get_format_featured_image( $snax_post_format, $snax_user_id, $snax_post_id );
}
?>

<div class="snax-edit-post-row-media">
	<?php
	$snax_class = array(
		'snax-tab-content',
		'snax-tab-content-current',
		'snax-tab-content-' . ( $snax_featured_image ? 'hidden' : 'visible' ),
		'snax-tab-content-featured-image',
	);
	?>
	<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_class ) ); ?>" data-snax-parent-post-id="<?php echo absint( $snax_post_id ); ?>" data-snax-featured="<?php echo $snax_set_as_featured ? 'standard' : 'none'; ?>" data-snax-source-form="<?php echo esc_attr( $snax_source_form ); ?>">
		<?php
		add_filter( 'snax_form_file_upload_featured_image','__return_true' );
		snax_get_template_part( 'posts/form-edit/new', 'image' );
		remove_filter( 'snax_form_file_upload_featured_image','__return_true' ); ?>

		<input type="hidden" name="snax-delete-media-nonce" value="<?php echo esc_attr( wp_create_nonce( 'snax-delete-media' ) ); ?>"/>
	</div>

	<div id="snax-featured-image">
		<?php
		if ( $snax_featured_image ) {
			global $post;
			$post = $snax_featured_image;
			setup_postdata( $post );

			snax_get_template_part( 'featured-image' );

			wp_reset_postdata();
		}
		?>
	</div>
</div>
