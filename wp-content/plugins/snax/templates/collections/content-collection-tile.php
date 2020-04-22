<?php
/**
 * Snax Collection Tile Template Part
 *
 * @package snax 1.11
 * @subpackage Theme
 */
?>
<div class="snax-collection snax-collection-tpl-tile">
	<figure class="snax-collection-media">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail(); ?>
		</a>
	</figure>

	<div class="snax-collection-body">

		<?php if ( $snax_collections_elements['author'] ) : ?>
		<p class="snax-collection-before-title">
			<?php if ( $snax_collections_elements['author'] ) : ?>
				<?php
					snax_render_collection_author( array(
						'avatar_size' => 32,
					) );
				?>
			<?php endif; ?>
		</p>
		<?php endif; ?>

		<?php snax_render_collection_title(); ?>
		<p>
			<?php snax_render_collection_item_count(); ?>
		</p>
	</div>
</div>