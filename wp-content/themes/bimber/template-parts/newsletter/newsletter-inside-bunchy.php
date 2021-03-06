<?php
/**
 * The template part for displaying a newsletter sign-up form inside a list collection.
 *
 * @package Bimber_Theme 4.10
 */

?>
<li class="g1-collection-item">
	<?php if ( bimber_can_use_plugin( 'mailchimp-for-wp/mailchimp-for-wp.php' ) ) : ?>
		<?php
			$newsletter_config = bimber_mc4wp_get_slot_config( 'in_collection' );

			if ( ! empty( $newsletter_config ) ) {
				$newsletter_classes = apply_filters( 'bimber_newsletter_inside_bunchy_class', array(
				) );

				echo do_shortcode( sprintf(
					'[bimber_mc4wp_form title="%s" subtitle="%s" avatar_id="%d" background_image_id="%d" template="%s" class="%s"]',
					$newsletter_config['title'],
					$newsletter_config['subtitle'],
					$newsletter_config['avatar_id'],
					$newsletter_config['background_image_id'],
					$newsletter_config['template'],
					implode( ' ', $newsletter_classes )
				));
			}
		?>
	<?php else : ?>

		<?php get_template_part( 'template-parts/newsletter/notice-plugin-required' ); ?>

	<?php endif; ?>
</li>
