<?php
/**
 * Collection Actions
 *
 * @package snax 1.19
 * @subpackage Collections
 */

// Don't remove that phrase. Theme can use it!
$snax_add_desc_label = __( 'Add a description', 'snax' );
?>

<p class="snax-collection-actions" data-snax-collection="<?php echo absint( get_the_ID() ); ?>" data-snax-nonce="<?php echo esc_attr( wp_create_nonce( 'snax-collection-delete' ) ); ?>">
	<?php if ( snax_collection_has_items() ) : ?>
		<button class="snax-collection-action snax-collection-action-clear-all" name="snax-collection-action-clear-all"><?php esc_html_e( 'Clear All', 'snax' ); ?></button>
	<?php endif; ?>

	<?php if ( snax_user_can_edit_collection() && ! snax_is_collection_edit_view() ) : ?>
		<a href="<?php echo esc_url( snax_get_collection_edit_url() ); ?>" class="snax-collection-action snax-collection-action-edit"><?php esc_html_e( 'Edit', 'snax' ); ?></a>
	<?php endif; ?>

	<?php if ( snax_user_can_delete_collection() ) : ?>
		<button class="snax-collection-action snax-collection-action-delete"><?php esc_html_e( 'Delete', 'snax' ); ?></button>
	<?php endif; ?>
</p>
