<?php
/**
 * Settings Functions
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

class MAce_Settings_Page {
	/**
	 * Admin capability
	 *
	 * @var string
	 */
	public $capability;

	/**
	 * Admin settings page
	 *
	 * @var string
	 */
	public $page;

	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Variables
	 */
	private function setup_globals() {
		// Main capability.
		$this->capability = mace_get_capability();

		// Main settings page.
		$this->page = 'options-general.php';
	}

	/**
	 * Resources
	 */
	private function includes() {
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'navigation.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'sections.php' );
	}

	/**
	 * Define all hooks
	 */
	private function setup_hooks() {
		add_action( 'admin_menu',           array( $this, 'add_page' ) );
		add_action( 'admin_head',           array( $this, 'hide_subpages' ) );
		add_action( 'admin_init',           array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_page() {
		$hooks = array();

		$settings_pages = mace_get_settings_pages();

		foreach( $settings_pages as $page_id => $page_config ) {
			$hooks[] = add_options_page(
				__( 'MediaAce', 'mace' ),
				__( 'MediaAce', 'mace' ),
				$this->capability,
				$page_id,
				$page_config['page_callback']
			);
		}

		// Move the menu position just below media.
		if ( apply_filters( 'media_ace_move_option_page_below_media', false ) ) {
			global $submenu;
			if ( isset( $submenu['options-general.php'] ) ) {
				foreach ( $submenu['options-general.php'] as $key => $value ) {
					if ( __( 'MediaAce', 'mace' ) === $value[0] ) {
						unset( $submenu['options-general.php'][ $key ] );
						$submenu['options-general.php'] = mace_array_insert_after( 30, $submenu['options-general.php'], 31, $value );
					}
				}
			}
		}
		// Highlight Settings > MediaAce menu item regardless of current tab.
		foreach ( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'mace_admin_settings_menu_highlight' );
		}
	}

	/**
	 * Hide submenu items under the Settings section
	 */
	public function hide_subpages() {
		$pages = mace_get_settings_pages();
		$index = 0;

		foreach( $pages as $page_id => $page ) {
			if ( 0 === $index++ ) {
				continue;
			}

			remove_submenu_page( $this->page, $page_id );
		}
	}

	/**
	 * Register settings
	 *
	 * @return void
	 */
	public function page_init() {
		// Bail if no sections available.
		$sections = mace_admin_get_settings_sections();

		if ( empty( $sections ) ) {
			return;
		}

		// Loop through sections.
		foreach ( (array) $sections as $section_id => $section ) {

			// Only add section and fields if section has fields.
			$fields = mace_admin_get_settings_fields_for_section( $section_id );

			if ( empty( $fields ) ) {
				continue;
			}

			$page = $section['page'];

			// Add the section.
			add_settings_section(
				$section_id,
				$section['title'],
				$section['callback'],
				$page
			);

			// Loop through fields for this section.
			foreach ( (array) $fields as $field_id => $field ) {

				// Add the field.
				if ( ! empty( $field['callback'] ) && ! empty( $field['title'] ) ) {
					add_settings_field(
						$field_id,
						$field['title'],
						$field['callback'],
						$page,
						$section_id,
						$field['args']
					);
				}

				// Register the setting.
				register_setting( $page, $field_id, $field['sanitize_callback'] );
			}
		}
	}
}

// Init.
new MAce_Settings_Page();


function mace_select_image_control( $control_id, $media_id ) {
	wp_enqueue_media();
	?>
	<style>
		.mace-preview img {
			display: block;
		}
	</style>
	<div class="mace-media-library-image <?php echo sanitize_html_class( $media_id > 0 ? 'mace-image-set' : 'mace-image-not-set' );  ?>">
		<div class="mace-preview">
			<?php echo wp_get_attachment_image( $media_id, 'medium' ); ?>
			<a href="#" class="mace-remove-image button"><?php esc_html_e( 'Remove', 'mace' ); ?></a>
			<a href="#" class="mace-select-image button"><?php esc_html_e( 'Select Image', 'mace' ); ?></a>
		</div>

		<input class="mace-image-id" name="<?php echo esc_attr( $control_id ); ?>" id="<?php echo esc_attr( $control_id ); ?>" type="hidden" value="<?php echo absint( $media_id ); ?>" />
	</div>
	<?php
}
