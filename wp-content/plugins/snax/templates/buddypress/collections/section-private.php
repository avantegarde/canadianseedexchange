<?php
/**
 * User Private Collections
 *
 * @package snax 1.11
 * @subpackage Templates
 */
?>
<div class="snax">
	<?php do_action( 'snax_template_before_user_private_collections' ); ?>

	<?php snax_get_template_part( 'buddypress/collections/user-predefined-collections' ); ?>

	<div id="snax-user-private-collections" class="snax-user-private-collections">
		<div class="snax-user-section">

			<?php if ( snax_has_user_private_collections( bp_displayed_user_id() ) && bp_is_my_profile() ) : ?>

				<?php snax_get_template_part( 'buddypress/collections/pagination', 'top' ); ?>

				<?php snax_get_template_part( 'buddypress/collections/loop-collections' ); ?>

				<?php snax_get_template_part( 'buddypress/collections/pagination', 'bottom' ); ?>

			<?php else : ?>

				<?php snax_get_template_part( 'buddypress/collections/empty-section-private' ); ?>

			<?php endif; ?>

		</div>
	</div>

	<?php do_action( 'snax_template_after_user_private_collections' ); ?>
</div>
