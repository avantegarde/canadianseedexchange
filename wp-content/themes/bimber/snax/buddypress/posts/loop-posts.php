<?php
/**
 * Post items Loop
 *
 * @package snax 1.11
 * @subpackage Votes
 */

?>
<?php do_action( 'snax_template_before_bp_posts_loop' ); ?>

<div class="snax-posts">
	<div class="g1-collection g1-collection-columns-2">
		<?php
		$bimber_elements = array(
			'featured_media' => true,
			'categories'     => false,
			'shares'         => false,
			'views'          => false,
			'comments_link'  => false,
			'downloads'      => false,
			'votes'          => false,
			'subtitle'       => false,
			'summary'        => false,
			'author'         => false,
			'avatar'         => false,
			'date'           => true,
			'call_to_action' => true,
			'action_links'   => true,
		);

		bimber_set_template_part_data( array(
			'elements'                     => $bimber_elements,
			'card_style'                   => 'none',
			'call_to_action_hide_buttons'  => 'read_more',
		) );
		?>
		<div class="g1-collection-viewport">
			<ul class="g1-collection-items">
				<?php while ( snax_user_posts() ) : snax_the_post(); ?>
					<li class="g1-collection-item">
						<?php get_template_part( 'template-parts/content-grid-standard', get_post_format() ); ?>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
		<?php bimber_reset_template_part_data(); ?>
	</div><!-- .g1-collection -->
</div>

<?php do_action( 'snax_template_after_bp_posts_loop' );

