<?php
/**
 * Options Page
 *
 * @package G1 Socials
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_action( 'admin_menu', 'g1_socials_add_options_page' );
/**
 * Add options page.
 */
function g1_socials_add_options_page() {
	add_submenu_page(
		'options-general.php',
		esc_html__( 'G1 Socials', 'g1_socials' ), // Page title.
		esc_html__( 'G1 Socials', 'g1_socials' ), // Menu title.
		'manage_options', // Capability.
		g1_socials_options_page_slug(), // Slug.
		'g1_socials_options_page_renderer_callback' // Page renderer callback.
	);
}

/**
 * Options page renderer.
 */
function g1_socials_options_page_renderer_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'g1_socials' ) );
	}
	// Declare tabs. In array for future.
	$tabs = array(
		'g1_socials'    => array(
			'path'     => add_query_arg( array(
				'page' => g1_socials_options_page_slug(),
				'tab'  => G1_Socials()->get_option_name(),
			), '' ),
			'label'    => esc_html__( 'Socials', 'g1_socials' ),
			'settings' => 'g1_socials_options',
		),
	);
	$tabs = apply_filters( 'g1_socials_options_tabs', $tabs );
	// Get active tab, check if any is selected.
	$current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
	if ( null === $current_tab ) {
		$current_tab = key( $tabs );
	}
	?>
	<?php do_action( 'g1_plugin_before_admin_page' ); ?>
	<?php do_action( 'g1_socials_before_admin_page' ); ?>
	<div class="wrap">
		<h2><?php esc_html_e( 'G1 Socials Options', 'g1_socials' ); ?></h2>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $tab_key => $tab ) : ?>
				<a href="<?php echo( esc_attr( $tab['path'] ) ); ?>" class="nav-tab <?php echo( sanitize_html_class( $current_tab === $tab_key ? 'nav-tab-active' : '' ) ); ?>">
					<?php echo( esc_html( $tab['label'] ) ); ?>
				</a>
			<?php endforeach; ?>
		</h2>
		<form id="<?php echo( sanitize_html_class( $current_tab ) ); ?>-form" method="post" action="options.php">
			<?php
			settings_fields( $current_tab );
			if ( G1_Socials()->get_option_name() === $current_tab ) {
				include( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'options-page-socials.php' );
			} else {
				do_settings_sections( $current_tab );
			}
			submit_button();
			?>
		</form>
	<?php do_action( 'g1_socials_after_admin_page' ); ?>
	<?php do_action( 'g1_plugin_after_admin_page' ); ?>
	</div><!-- .wrap -->
	<?php
}
