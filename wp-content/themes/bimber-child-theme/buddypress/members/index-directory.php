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

	<div id="primary" class="g1-primary-max">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope=""
				         itemtype="<?php echo esc_attr( bimber_get_entry_microdata_itemtype() ); ?>">
					<header class="g1-row g1-row-layout-page page-header page-header-01">
						<div class="g1-row-inner">
							<div class="g1-column g1-column-2of3">
								<?php the_title( '<h1 class="g1-alpha g1-alpha-2nd page-title">', '</h1>' ); ?>

								<?php
								if ( bimber_can_use_plugin( 'wp-subtitle/wp-subtitle.php' ) ) :
									the_subtitle( '<h2 class="g1-delta g1-delta-3rd page-subtitle">', '</h2>' );
								endif;
								?>
							</div>

							<div class="g1-column g1-column-1of3">
								<div id="members-dir-search" class="dir-search" role="search">
									<?php bp_directory_members_search_form(); ?>
								</div><!-- #members-dir-search -->
							</div>
						</div>
						<div class="g1-row-background">
						</div>
					</header><!-- .page-header -->

					<?php bimber_render_entry_featured_media(); ?>

					<div class="g1-row g1-row-layout-page g1-row-padding-m entry-body entry-body-row">
						<div class="g1-row-inner">

							<div class="g1-column g1-column-2of3">
								<div class="entry-content">
									<?php
									the_content();
									wp_link_pages();
									?>
								</div><!-- .entry-content -->
							</div>

							<?php get_sidebar(); ?>

						</div>
						<div class="g1-row-background">
						</div>
					</div>

				</article><!-- #post-## -->

			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer();
