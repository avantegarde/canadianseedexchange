<?php
/**
 * The template part for displaying content
 *
 * @package Bimber_Theme 4.10
 */

?>
<?php
$bimber_entry_data = bimber_get_template_part_data();
$bimber_elements   = $bimber_entry_data['elements'];

// Normalize.
$bimber_elements['call_to_action'] = $bimber_elements['call_to_action'] && bimber_has_entry_call_to_action( $bimber_entry_data['call_to_action_hide_buttons'] );

if ( ! isset( $bimber_elements['voting_box'] )  ) {
	$bimber_elements['voting_box'] = false;
}
$bimber_elements['share_buttons'] = true;
$bimber_elements['action_links'] = $bimber_elements['action_links'] && bimber_has_entry_action_links();

// Shortcuts.
$bimber_elements['byline'] = $bimber_elements['author'] || $bimber_elements['date'];
$bimber_elements['todome'] = $bimber_elements['call_to_action'] || $bimber_elements['voting_box'] || $bimber_elements['share_buttons'] || $bimber_elements['action_links'];
?>
<article <?php post_class( 'entry-tpl-listxl' ); ?>>
	<?php
	if ( $bimber_elements['featured_media'] ) :
		bimber_render_entry_featured_media( array(
			'size' => 'bimber-list-standard',
		) );
	endif;
	?>

	<?php
	bimber_render_open_list_badge();
	bimber_render_entry_flags(); ?>

	<div class="entry-body">
		<header class="entry-header">
			<div class="entry-before-title">
				<?php
				bimber_render_entry_stats( array(
					'share_count'       => $bimber_elements['shares'],
					'view_count'        => $bimber_elements['views'],
					'comment_count'     => $bimber_elements['comments_link'],
					'download_count'    => $bimber_elements['downloads'],
					'vote_count'        => $bimber_elements['votes'],
					'class'             => 'g1-meta g1-current-background',
				) );
				?>

				<?php
				if ( $bimber_elements['categories'] ) :
					bimber_render_entry_categories();
				endif;
				?>
			</div>

			<?php bimber_render_entry_title( '<h3 class="g1-beta g1-beta-1st entry-title"><a href="%1$s" rel="bookmark">', '</a></h3>' ); ?>

			<?php
			if ( $bimber_elements['subtitle'] ) {
				bimber_render_entry_subtitle( '<p class="g1-delta g1-delta-3rd entry-subtitle">', '</p>' );
			}
			?>
		</header>

		<?php if ( $bimber_elements['summary'] ) : ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<footer>
			<?php if ( $bimber_elements['byline'] ) : ?>
				<p class="g1-meta entry-meta entry-byline <?php if ( $bimber_elements['avatar'] ) { echo sanitize_html_class( 'entry-byline-with-avatar' );}?>">
					<?php
					if ( $bimber_elements['author'] ) :
						bimber_render_entry_author( array( 'avatar' => $bimber_elements['avatar'] ) );
					endif;
					?>

					<?php
					if ( $bimber_elements['date'] ) :
						bimber_render_entry_date();
					endif;
					?>
				</p>
			<?php endif; ?>
		</footer>

		<?php if ( $bimber_elements['todome'] ) : ?>
			<div class="entry-todome g1-dropable snax">
				<?php
					if ( $bimber_elements['call_to_action'] ) :
						bimber_render_entry_cta_button( array( 'class' => 'g1-button g1-button-simple g1-button-s' ) );
					endif;
				?>

				<?php
					if ( $bimber_elements['voting_box'] ) :
						do_action( 'bimber_entry_voting_box', 's' );
					endif;
				?>

				<?php
					if ( $bimber_elements['share_buttons'] ) :
						bimber_render_mini_share_buttons();
					endif;
				?>

				<?php
					if ( $bimber_elements['action_links'] ) :
						bimber_render_entry_action_links();
					endif;
				?>
			</div>
		<?php endif; ?>
	</div>
</article>
