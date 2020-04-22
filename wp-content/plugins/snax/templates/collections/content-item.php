<?php
/**
 * Collection Single Item Template Part
 *
 * @package snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

global $snax_collection_id;
?>

<article class="snax-entry snax-entry-tpl-olistxs">
	<div class="snax-entry-counter">
	</div>
	<figure class="snax-entry-media">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'thumbnail' ); ?>
		</a>
	</figure>

	<div class="snax-entry-body">
		<p class="snax-entry-title"><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></p>

		<?php snax_render_entry_author(); ?>
		<?php snax_render_entry_date(); ?>
		<?php snax_render_collection_item_action_links( $snax_collection_id ); ?>
	</div>
</article>
