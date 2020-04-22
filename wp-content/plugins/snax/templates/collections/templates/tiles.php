<?php
/**
 * Snax Tiles Collection Template.
 *
 * @package snax
 * @subpackage Collections
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}
?>
<?php if ( $snax_collections_query->have_posts() ) : ?>
	<section class="snax-collections snax-collections-tpl-tiles">
		<?php
			set_query_var( 'snax_collections_title', $snax_collections_title );
			set_query_var( 'snax_collections_title_size', $snax_collections_title_size );
			set_query_var( 'snax_collections_title_align', $snax_collections_title_align );
			snax_get_template_part( 'collections/header' );
		?>

		<ul class="snax-collections-items">
		<?php while ( $snax_collections_query->have_posts() ) :  $snax_collections_query->the_post(); ?>
			<li class="snax-collections-item">
				<?php snax_get_template_part( 'collections/content-collection', 'tile' ); ?>
			</li>
		<?php endwhile; ?>
		</ul>
	</section>
<?php endif;
