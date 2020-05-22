<?php
/**
 * Template Name: Trusted Members
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

	<div class="g1-primary-max">
		<div id="content" role="main">

			<?php
			while ( have_posts() ) : the_post();
			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope=""
						itemtype="<?php echo esc_attr( bimber_get_entry_microdata_itemtype() ); ?>">

					<?php
					// Get options.
					$bimber_options = bimber_get_page_header_options( $post->ID );

					// Prepare template part data.
					set_query_var( 'bimber_title', get_the_title() );

					if ( bimber_can_use_plugin( 'wp-subtitle/wp-subtitle.php' ) ) {
						set_query_var( 'bimber_subtitle', the_subtitle( '', '', false ) );
					}

					// Load template part.
					get_template_part( 'template-parts/page/header', $bimber_options['composition'] );
					?>

					<div <?php bimber_render_page_body_class(); ?>>
						<div class="g1-row-background">
						</div>
						<div class="g1-row-inner">

							<div id="primary" class="g1-column g1-column-2of3">
								<?php
								bimber_render_entry_featured_media( array(
									'size'          => 'bimber-grid-2of3',
									'class'         => 'entry-featured-media-main',
									'use_microdata' => true,
									'apply_link'    => false,
									'allow_video'   => true,
								) );
								?>

								<div class="entry-content trusted-members-content" <?php bimber_render_microdata( array( 'itemprop' => 'text' ) ); ?>>
									<!-- START:  Member List -->
									<?php if ( bp_has_members( 'type=alphabetical', bp_ajax_querystring( 'members' ) ) ) : ?>
 
										<div id="pag-top" class="pagination">

											<div class="pag-count" id="member-dir-count-top">

												<?php bp_members_pagination_count(); ?>

											</div>

											<div class="pagination-links" id="member-dir-pag-top">

												<?php bp_members_pagination_links(); ?>

											</div>

										</div>

										<?php do_action( 'bp_before_directory_members_list' ); ?>

										<ul id="members-list" class="item-list trusted-members" role="main">

										<?php while ( bp_members() ) : bp_the_member(); ?>

											<li>
												<div class="item-avatar">
														<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
												</div>

												<div class="item">
													<div class="item-title">
															<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
															<?php // if ( bp_get_member_latest_update() ) : ?>
																<!-- <span class="update"> <?php // bp_member_latest_update(); ?></span> -->
															<?php // endif; ?>
													</div>
													<div class="member-trades">
														<?php
															$user_id = bp_get_member_user_id();
															$points = mycred_get_users_balance( $user_id );
															$trades_num = (int)$points;
														?>
														<span class="trade-points">
															<span class="number"><?php echo sprintf($points); ?></span>
															<span class="trades"><?php echo sprintf( _n( 'trade', 'trades', $points, 'youzer' ), $points ); ?></span>
														</span>
														<?php if ($trades_num >= 15) : ?>
															<span class="prime-member">Prime Member</span>
														<?php endif;?>
													</div>

													<!-- <div class="item-meta"><span class="activity"><?php // bp_member_last_active(); ?></span></div> -->

													<?php do_action( 'bp_directory_members_item' ); ?>

													<?php
													/***
													 * If you want to show specific profile fields here you can,
													 * but it'll add an extra query for each member in the loop
													 * (only one regardless of the number of fields you show):
													 *
													 * bp_member_profile_data( 'field=the field name' );
													*/
													?>
												</div>

													<!-- <div class="action">
															<?php // do_action( 'bp_directory_members_actions' ); ?>
													</div> -->

												<div class="clear"></div>
											</li>

										<?php endwhile; ?>

										</ul>

										<?php do_action( 'bp_after_directory_members_list' ); ?>

										<?php bp_member_hidden_fields(); ?>

										<div id="pag-bottom" class="pagination">

											<div class="pag-count" id="member-dir-count-bottom">

													<?php bp_members_pagination_count(); ?>

											</div>

											<div class="pagination-links" id="member-dir-pag-bottom">

												<?php bp_members_pagination_links(); ?>

											</div>

										</div>

									<?php else: ?>

										<div id="message" class="info">
											<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
										</div>

									<?php endif; ?>
									<!-- END:  Member List -->
									
									<?php
									//the_content();
									//wp_link_pages();
									//get_template_part( 'template-parts/comments' );
									?>
								</div><!-- .entry-content -->
							</div>

							<?php get_sidebar(); ?>
						</div>
					</div>

				</article><!-- #post-## -->

			<?php endwhile;
			?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer();
