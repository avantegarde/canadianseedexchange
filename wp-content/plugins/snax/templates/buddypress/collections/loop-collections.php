<?php
/**
 * Collections Loop
 *
 * @package snax 1.11
 * @subpackage Votes
 */

?>

<?php do_action( 'snax_template_before_bp_collections_loop' ); ?>

<div class="snax-collections snax-collections-tpl-icons">
	<ul class="snax-collections-items">
	<?php while ( snax_collections() ) : snax_the_collection(); ?>
		<li class="snax-collections-item">
			<?php
				set_query_var( 'snax_collections_elements', array(
					'author' => false,
				) );
				snax_get_template_part( '/collections/content-collection', 'icon' );
			?>
		</li>
	<?php endwhile; ?>
	</ul>
</div>

<?php do_action( 'snax_template_after_bp_collections_loop' ); ?>
