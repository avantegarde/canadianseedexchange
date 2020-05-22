<?php
/**
 * Comment functions
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package Bimber_Theme
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Custom function for displaying comments
 *
 * @param object $comment Comment object.
 * @param array  $args Arguments.
 * @param int    $depth Depth.
 */
function bimber_wp_list_comments_callback2( $comment, $args, $depth ) {
	add_filter( 'get_avatar', 'bimber_add_avatar_microdata', 99 );
	add_filter( 'get_comment_author', 'bimber_add_comment_author_microdata' );
	add_filter( 'get_comment_author_link', 'bimber_add_comment_author_link_microdata' );
	var_dump($comment->user_id);

	$avatar_size = ( 1 === $depth ) ? 36 : 30;

	switch ( $comment->comment_type ) :
		case '' :
			?>
			<li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="li-comment-<?php comment_ID(); ?>">

			<article <?php bimber_render_comment_body_class(); ?> id="comment-<?php comment_ID(); ?>" itemscope itemtype="http://schema.org/Comment">
				<footer class="comment-meta">
					<div class="comment-author" itemprop="author" itemscope itemtype="http://schema.org/Person">
						<?php echo get_avatar( $comment, $avatar_size ); ?>
						<b class="g1-epsilon g1-epsilon-1st fn"><?php comment_author_link(); ?></b> <span
							class="says"><?php esc_html_e( 'says:', 'bimber' ); ?></span>
					</div><!-- .comment-author -->

					<div class="g1-meta comment-metadata">
						<a itemprop="url" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time itemprop="datePublished"
							      datetime="<?php echo esc_attr( get_comment_date( 'Y-m-d' ) . 'T' . get_comment_time( 'H:i:s' ) . bimber_get_iso_8601_utc_offset() ); ?>">
								<?php printf( esc_html_x( '%1$s at %2$s', '1: date, 2: time', 'bimber' ), get_comment_date(), get_comment_time() ); ?>
							</time>
						</a>
						<?php edit_comment_link( __( 'Edit', 'bimber' ) ); ?>
					</div><!-- .comment-metadata -->

				</footer><!-- .comment-meta -->

				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'bimber' ); ?></p>
				<?php endif; ?>

				<div class="comment-content" itemprop="text">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<?php
				$args = array(
					'field'   => 'Signature', // Field name or ID.
					'user_id' => $comment->user_id
					);
				$signature = bp_get_profile_field_data( $args );
				?>
				
				<style>
					.comment-signature {
						display: block;
						padding: 0;
						border: 1px solid #eaeaea;
						margin-top: -1.25em;
						margin-bottom: 1.25em;
					}
					.comment-signature .title {
						display: none;
						padding: 5px;
						background: rgba(0,0,0,0.05);
					}
					.comment-signature .signature-content {
						display: block;
						padding: 5px;
					}
				</style>
				<div class="comment-signature">
					<span class="title">Signature</span>
					<div class="signature-content">
						<?php echo $signature; ?>
					</div>
				</div>

				<div class="g1-meta reply">
					<?php comment_reply_link( array_merge( $args, array(
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
					) ) ); ?>
				</div>
			</article><!-- .comment-body -->
			<?php
			break;
		case 'pingback'  :
		case 'trackback' :
			?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'bimber' ); ?><?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'bimber' ), '<span class="edit-link">', '</span>' ); ?></p>
			<?php
			break;
	endswitch;

	remove_filter( 'get_avatar', 'bimber_add_avatar_microdata', 99 );
	remove_filter( 'get_comment_author', 'bimber_add_comment_author_microdata' );
	remove_filter( 'get_comment_author_link', 'bimber_add_comment_author_link_microdata' );
}