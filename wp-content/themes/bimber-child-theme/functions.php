<?php
// Prevent direct script access
if ( !defined( 'ABSPATH' ) )
	die ( 'No direct script access allowed' );

/**
* Child Theme Setup
* 
* Always use child theme if you want to make some custom modifications. 
* This way theme updates will be a lot easier.
*/
function bimber_childtheme_setup() {
}

add_action( 'after_setup_theme', 'bimber_childtheme_setup' );

/**
 * Filter User Statistics and change points to "Trades"
 */
add_filter('yz_get_user_statistics_details','convert_user_stats');
function convert_user_stats() {
  $statistics = array(
		'posts'     => __( 'Posts', 'youzer' ),
		'comments'  => __( 'Comments', 'youzer' ),
		'views'     => __( 'Views', 'youzer' ),
		'ratings'   => __( 'Ratings', 'youzer' ),       
		'followers' => __( 'Followers', 'youzer' ),       
		'following' => __( 'Following', 'youzer' ),  
		'points'    => __( 'Trades', 'youzer' )  
	);
	return $statistics;
}

/**
 * Render author information for entry.
 *
 * @param array $args Arguments.
 */
add_filter('bimber_entry_author_html','author_with_flair2');
function author_with_flair2( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'avatar'        => true,
		'avatar_size'   => 30,
		'use_microdata' => false,
	) );
	ob_start();
	?>
	<?php if ( $args['use_microdata'] ) : ?>
		<span class="entry-author" itemscope="" itemprop="author" itemtype="http://schema.org/Person">
	<?php else : ?>
		<span class="entry-author">
	<?php endif; ?>

		<span class="entry-meta-label"><?php esc_html_e( 'by', 'bimber' ); ?></span>
			<?php
				printf(
					'<a href="%s" title="%s" rel="author">',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_attr( sprintf( __( 'Posts by %s', 'bimber' ), get_the_author() ) )
				);
			?>

			<?php
			if ( $args['avatar'] ) :
				echo get_avatar( get_the_author_meta( 'email' ), $args['avatar_size'] );
			endif;
			?>

			<?php if ( $args['use_microdata'] ) : ?>
				<strong itemprop="name"><?php echo esc_html( get_the_author() ); ?></strong>
			<?php else : ?>
				<strong><?php echo esc_html( get_the_author() ); ?></strong>
			<?php endif; ?>
		</a>
		<?php
		global $authordata;
		$authorID = is_object( $authordata ) ? $authordata->ID : null;
		$trades = mycred_get_users_balance( $authorID, 'trades' );
		$trades_num = (int)$trades;
		if ($trades_num >= 15) {
			$trades_label = 'Prime Member';
		} else {
			$trades_label = 'Trades ' . $trades;
		}
		?>
		<span class="trades-flair"><?php echo $trades_label; ?></span>
	</span>
	<?php
	$out = ob_get_clean();
	return $out;
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
				if ($signature) : ?>
				<div class="comment-signature">
					<span class="title">Signature</span>
					<div class="signature-content">
						<?php echo $signature; ?>
					</div>
				</div>
				<?php endif; ?>

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

/**
 * Display Member Trades on member card
 */
function yz_get_md_mycred_statistics_trades( $user_id ) {

	?>

    <?php if ( 'on' == yz_option( 'yz_enable_md_user_points_statistics', 'on' ) ) :  ?>
				 <?php
					$points = mycred_get_users_balance( $user_id );
					$trades_num = (int)$points;
				?>
				<?php if ($trades_num >= 15) : ?>
					<span class="prime-member">Prime Member</span><br>
				<?php endif;?>
        <span class="yz-data-item yz-data-points trades-points">
            <!-- <span class="dashicons dashicons-randomize"></span> -->
						<span><?php echo sprintf($points); ?></span>
        </span>
				<span class="trades"><?php echo sprintf( _n( 'trade', 'trades', $points, 'youzer' ), $points ); ?></span>
    <?php endif; ?>

	<?php

}
remove_action( 'yz_after_members_directory_card_statistics', 'yz_get_md_mycred_statistics' );
add_action( 'yz_after_members_directory_card_statistics', 'yz_get_md_mycred_statistics_trades' );