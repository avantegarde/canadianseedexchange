<?php
/**
 * The Template for displaying pages.
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme 4.10
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
get_header();
?>
	<div id="primary" class="g1-primary-max bimber-buddypress-profile">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

			<?php if ( bp_has_groups() ) : ?>
				<?php while ( bp_groups() ) : bp_the_group(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope=""
					         itemtype="<?php echo esc_attr( bimber_get_entry_microdata_itemtype() ); ?>">
						<div id="buddypress">

							<div class="g1-row-notices">
								<?php
								/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
								do_action( 'template_notices' );
								?>
							</div>

							<?php
								/**
								 * If the cover image feature is enabled, use a specific header
								 */
								if ( bp_group_use_cover_image_header() ) :
									bp_get_template_part( 'groups/single/cover-image-header' );
								else :
									bp_get_template_part( 'groups/single/group-header' );
								endif;
							?>

							<?php
							$bimber_class = array(
								'g1-row',
								'g1-row-layout-page',
							);

							if ( bp_group_use_cover_image_header() ) {
								$bimber_class[] = 'item-wrapper-with-cover-image';
							} else {
								$bimber_class[] = 'item-wrapper-without-cover-image';
							}
							?>

							<?php get_template_part( 'buddypress/groups/single/after-header' ); ?>


							<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $bimber_class ) ); ?>" id="item-wrapper">
								<div class="g1-row-background">
								</div>
								<div class="g1-row-inner">
									<div class="g1-column g1-column-2of3">
										<div id="item-nav">
											<div class="item-list-tabs no-ajax" id="object-nav" aria-label="<?php esc_attr_e( 'Group primary navigation', 'buddypress' ); ?>" role="navigation">
												<ul>
													<?php bp_get_options_nav(); ?>

													<?php
													/**
													* Fires after the display of group options navigation.
													*
													* @since 1.2.0
													*/
													do_action( 'bp_group_options_nav' );
													?>
												</ul>
											</div>
										</div><!-- #item-nav -->

										<div id="item-content">
											<div class="entry-content">
												<?php
												the_content();
												wp_link_pages();
												?>
											</div><!-- .entry-content -->
										</div>
									</div><!-- .g1-column -->

									<?php get_sidebar(); ?>
								</div>
							</div><!-- .g1-row -->

						</div><!-- #buddypress -->

					</article><!-- #post-## -->

				<?php endwhile; ?>
			<?php endif; ?>

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer();
