<?php
/**
 * The template part for a product inside a grid collection.
 *
 * @package Bimber_Theme 4.10
 */
$bimber_entry_data = bimber_get_template_part_data();
$bimber_card_style = $bimber_entry_data['card_style'];

$bimber_class = array(
	'g1-product',
	'entry-tpl-grid',
);
if ( 'none' !== $bimber_card_style ) {
	$bimber_class[] = 'g1-card';
	$bimber_class[] = 'g1-card-' . $bimber_card_style;
}
?>
<li class="g1-collection-item g1-collection-item-1of3 woocommerce">
	<?php if ( bimber_can_use_plugin( 'woocommerce/woocommerce.php' ) ) : ?>

		<?php
		$bimber_entry_data      = bimber_get_template_part_data();
		$bimber_elements        = $bimber_entry_data['elements'];
		$bimber_product_query   = bimber_wc_get_injected_product_query();
		?>

		<?php if ( $bimber_product_query->have_posts() ) : ?>
			<?php while ( $bimber_product_query->have_posts() ) : $bimber_product_query->the_post(); ?>

				<aside <?php post_class( $bimber_class ); ?>>
					<?php
					if ( $bimber_elements['featured_media'] ) :
						bimber_render_entry_featured_media( array(
							'size' => 'bimber-grid-standard',
						) );
					endif;
					?>
					<div class="entry-body">
						<header class="entry-header">
							<div class="entry-before-title">
								<?php
								if ( $bimber_elements['categories'] ) :
									bimber_render_product_categories( array(
										'class' => 'entry-categories-solid',
									) );
								endif;
								?>
							</div>

							<?php the_title( sprintf( '<h3 class="g1-gamma g1-gamma-1st entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
						</header>

						<?php if ( $bimber_elements['summary'] ) : ?>
							<div class="entry-summary">
								<?php the_excerpt(); ?>
							</div>
						<?php endif; ?>

						<div class="entry-todome g1-dropable snax">
							<?php woocommerce_template_loop_price(); ?>
							<?php woocommerce_template_loop_add_to_cart(); ?>
						</div>
					</div>
				</aside>

			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>

		<?php else: ?>

			<?php get_template_part( 'woocommerce/notice-no-products' ); ?>

		<?php endif; ?>

	<?php else : ?>

		<?php get_template_part( 'woocommerce/notice-plugin-required' ); ?>

	<?php endif; ?>

</li>
