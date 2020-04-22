<?php
/**
 * User Public Collections
 *
 * @package snax 1.11
 * @subpackage Templates
 */

?>
<div class="snax">
	<?php do_action( 'snax_template_before_user_public_collections' ); ?>

	<div id="snax-user-public-collections" class="snax-user-public-collections">
		<div class="snax-user-section">

			<?php if ( snax_has_user_public_collections( bp_displayed_user_id() ) ) : ?>

				<?php snax_get_template_part( 'buddypress/collections/pagination', 'top' ); ?>

				<?php snax_get_template_part( 'buddypress/collections/loop-collections' ); ?>

				<?php snax_get_template_part( 'buddypress/collections/pagination', 'bottom' ); ?>

			<?php else : ?>

				<?php snax_get_template_part( 'buddypress/collections/empty-section-public' ); ?>

			<?php endif; ?>

		</div>
	</div>

	<?php do_action( 'snax_template_after_user_public_collections' ); ?>
</div>
