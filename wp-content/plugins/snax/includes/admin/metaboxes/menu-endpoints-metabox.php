<?php
/**
 * Snax Menu Endpoints Metabox
 *
 * @package snax
 * @subpackage Metaboxes
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Register metabox
 */
function snax_add_menu_endpoints_metabox() {
	add_meta_box(
		'snax_menu_endpoints',
		__( 'Snax', 'snax' ),
		'snax_menu_endpoints_metabox',
		'nav-menus',
		'side',
		'default'
	);

	do_action( 'snax_register_menu_endpoints_metabox' );
}

/**
 * Render metabox
 *
 * @param WP_Post $post         Post object.
 */
function snax_menu_endpoints_metabox( $post ) {
	?>
	<div id="posttype-snax" class="posttypediv">
		<h4><?php esc_html_e( 'Logged-In', 'snax' ); ?></h4>

		<p><?php esc_html_e( 'Links visible only for logged in users.', 'snax' ); ?></p>

		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php esc_html_e( 'Log Out', 'snax' ); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php esc_html_e( 'Log Out', 'snax' ); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="snax-logout-nav">
				</li>
			</ul>
		</div>

		<h4><?php esc_html_e( 'Logged-Out', 'snax' ); ?></h4>

		<p><?php esc_html_e( 'Links visible only for logged out users.', 'snax' ); ?></p>

		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="-2"> <?php esc_html_e( 'Log In', 'snax' ); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="<?php esc_html_e( 'Log In', 'snax' ); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-2][menu-item-classes]" value="snax-login-nav">
				</li>
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-3][menu-item-object-id]" value="-3"> <?php esc_html_e( 'Register', 'snax' ); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-3][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-3][menu-item-title]" value="<?php esc_html_e( 'Register', 'snax' ); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-3][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-3][menu-item-classes]" value="snax-register-nav">
				</li>
			</ul>
		</div>

		<?php if ( snax_waiting_room_enabled() ) : ?>

		<h4><?php esc_html_e( 'Waiting Room', 'snax' ); ?></h4>

		<p><?php esc_html_e( 'Show all pending posts.', 'snax' ); ?></p>

		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-4][menu-item-object-id]" value="-4"> <?php esc_html_e( 'Waiting Room', 'snax' ); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-4][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-4][menu-item-title]" value="<?php esc_html_e( 'Waiting Room', 'snax' ); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-4][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-4][menu-item-classes]" value="menu-item-snax-waiting-room snax-waiting-room-nav">
				</li>
			</ul>
		</div>

		<?php endif; ?>

		<h4><?php esc_html_e( 'Collections', 'snax' ); ?></h4>

		<?php
		$abstract_collections = snax_get_abstract_collections();
		$menu_item_id = -5;
		$available_collections = 0;
		?>
		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<?php $snax_my_collections_url = snax_get_my_collections_url(); ?>

				<?php if ( ! empty( $snax_my_collections_url ) ): ?>
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $menu_item_id ); ?>"> <?php echo esc_html_x( 'My Collections', 'Menu item title', 'snax' ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-type]" value="custom">
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-title]" value="<?php echo esc_attr_x( 'My Collections', 'Menu item title', 'snax' ); ?>">
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-url]" value="#">
						<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-classes]" value="menu-item-snax-my-collections snax-my-collections-nav">
					</li>
					<?php $menu_item_id--; ?>
				<?php endif; ?>

				<?php foreach( $abstract_collections as $collection_slug => $collection_config ): ?>
				<?php
					if ( ! snax_is_abstract_collection_activated( $collection_slug ) ) {
						continue;
					}
				?>

				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $menu_item_id ); ?>"> <?php echo esc_html( $collection_config['title'] ); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-title]" value="<?php echo esc_attr( $collection_config['title'] ); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $menu_item_id ); ?>][menu-item-classes]" value="menu-item-snax-collection-<?php echo sanitize_html_class( $collection_slug ); ?> snax-abstract_<?php echo sanitize_html_class( $collection_slug ); ?>-nav">
				</li>
				<?php $menu_item_id--; ?>
				<?php $available_collections++; ?>
				<?php endforeach; ?>
			</ul>

			<?php if ( 0 === $available_collections ) : ?>
				<p><?php printf( esc_html__( 'No active collections. Please activate them in %s settings.', 'snax' ), snax_get_collections_settings_link() ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Actions -->
		<p class="button-controls wp-clearfix">
			<span class="add-to-menu">
				<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-post-type-menu-item" id="<?php echo esc_attr( 'submit-posttype-snax' ); ?>" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
<?php
}

