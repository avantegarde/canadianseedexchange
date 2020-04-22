<?php
/**
 * Snax Collection Items Loop Template Part
 *
 * @package snax 1.11
 * @subpackage Theme
 */
?>
<?php if ( 'public' === snax_get_collection_visibility() || snax_user_is_collection_owner() || ( is_user_logged_in() && snax_is_abstract_collection() ) ) : ?>
	<?php
		global $page;
		global $snax_collection_id;
		$snax_collection_id = get_the_ID();

		$snax_ids = snax_get_collection_posts_on_page( $snax_collection_id, $page );

		$snax_items_query = new WP_Query( array(
			'post__in'          => ! empty( $snax_ids ) ? $snax_ids : array( '-1' ),
			'orderby'           => 'post__in',
			'posts_per_page'    => -1,
			'post_type'         => 'any',
		) );
	?>

	<?php do_action( 'snax_before_collection_items' ); ?>

	<?php if ( $snax_items_query->have_posts() ) : ?>
		<ul class="snax-collection-items snax-collection-items-tpl-olistxs" data-snax-collection="<?php the_ID(); ?>">
			<?php while ( $snax_items_query->have_posts() ) : $snax_items_query->the_post(); ?>
			<li class="snax-collection-item">
				<?php snax_get_template_part( 'collections/content-item' ); ?>
			</li>
			<?php endwhile; ?>
		</ul>
	<?php else: ?>
		<p><?php esc_html_e( 'Collection is empty', 'snax' ); ?></p>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>

	<?php do_action( 'snax_after_collection_items' ); ?>
<?php endif;
