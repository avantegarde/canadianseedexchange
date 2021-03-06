<?php
/**
 * New post form for format "Video"
 *
 * @package snax
 * @subpackage Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>

<?php
global $snax_post_format;
$snax_post_format = 'video';
$snax_has_files = snax_has_user_cards( $snax_post_format );

// HTML classes of the form.
$snax_class = array(
	'snax',
	'snax-form-frontend',
	'snax-form-frontend-format-' . $snax_post_format,
);
if ( ! $snax_has_files ) {
	$snax_class[] = 'snax-form-frontend-without-media';
	add_filter( 'snax_form_file_upload_no_media', '__return_true' );
}
?>

<?php do_action( 'snax_before_frontend_submission_form', $snax_post_format ); ?>

	<form action="" method="post" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_class ) ); ?>">
		<?php do_action( 'snax_frontend_submission_form_start', $snax_post_format ); ?>

		<div class="snax-form-main">
			<h2 class="snax-form-main-title screen-reader-text"><?php esc_html_e( 'Share your story', 'snax' ); ?></h2>

			<?php snax_get_template_part( 'posts/form-edit/row-title' ); ?>

			<div class="snax-edit-post-row-media">
				<?php
				$snax_adding_method = snax_video_get_adding_method();

				switch( $snax_adding_method ) {
					case 'upload_embed':
						$snax_forms = snax_get_new_item_forms( 0, array( $snax_post_format ), true);
						break;

					case 'upload':
						$snax_forms = snax_get_new_item_forms( 0, array( $snax_post_format ), true);
						remove_filter( 'snax_form_file_upload_methods', 'snax_upload_media_from_embed_code', 10 );
						break;

					case 'embed':
						$snax_forms = snax_get_new_item_forms( 0, array( 'embed' ), true);
						break;
				}
				?>

				<?php
				if ( count( $snax_forms ) > 1 ) {
					snax_render_snax_new_item_tabs( array(
						'add_new'   => 'add_new_items',
						'forms'     => $snax_forms,
						'classes'   => array( 'snax-tabs-nav-' . ( $snax_has_files ? 'hidden' : 'visible' ) ),
					) );
				}
				?>

				<?php foreach ( $snax_forms as $snax_key => $snax_value ) : ?>
					<?php
					$snax_class = array(
						'snax-tab-content',
						'snax-tab-content-' . $snax_key,
						'snax-tab-content-' . ( $snax_has_files ? 'hidden' : 'visible' ),
					);

					if ( snax_get_selected_new_item_form( $snax_forms ) === $snax_key ) {
						$snax_class[] = 'snax-tab-content-current';
					}
					?>
					<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $snax_class ) ); ?>">
						<?php add_filter( 'snax_plupload_config', 'snax_plupload_allow_multi_selection' ); ?>
						<?php snax_get_template_part( 'posts/form-edit/new', $snax_key ); ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="snax-cards snax-edit-post-row-video">
				<?php if ( $snax_has_files ) : ?>

					<?php snax_get_template_part( 'loop-video' ); ?>

				<?php endif; ?>
			</div><!-- .snax-cards -->

			<?php snax_get_template_part( 'posts/form-edit/row-source' ); ?>
			<?php snax_get_template_part( 'posts/form-edit/row-description' ); ?>
			<?php snax_get_template_part( 'posts/form-edit/row-referral' ); ?>

		</div><!-- .snax-form-main -->

		<div class="snax-form-side">
			<h2 class="snax-form-side-title screen-reader-text"><?php esc_html_e( 'Publish Options', 'snax' ); ?></h2>

			<input type="hidden" name="snax-post-format" value="video"/>

			<?php
			if ( snax_video_show_featured_media_field() ) {
				snax_get_template_part( 'posts/form-edit/row-featured-image' );
			}
			?>

			<?php
			if ( snax_video_show_category_field() ) {
				snax_get_template_part( 'posts/form-edit/row-categories' );
			}
			?>

			<?php snax_get_template_part( 'posts/form-edit/row-tags' ); ?>

			<?php snax_get_template_part( 'posts/form-edit/row-legal' ); ?>

			<?php snax_get_template_part( 'posts/form-edit/row-actions' ); ?>
		</div><!-- .snax-form-side -->

		<?php do_action( 'snax_frontend_submission_form_end', $snax_post_format ); ?>
	</form>

<?php do_action( 'snax_after_frontend_submission_form', $snax_post_format ); ?>
