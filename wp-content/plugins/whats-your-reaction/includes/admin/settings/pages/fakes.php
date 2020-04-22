<?php
/**
 * Fakes Settings page
 *
 * @package whats-your-reaction
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'wyr_settings_pages', 'wyr_register_fakes_settings_page', 10 );

function wyr_get_fakes_settings_page_id() {
	return apply_filters( 'wyr_fakes_settings_page_id', 'wyr-fakes-settings' );
}

function wyr_get_fakes_settings_page_config() {
	return apply_filters( 'wyr_fakes_settings_config', array(
		'tab_title'                 => _x( 'Fakes', 'Settings Page', 'wyr' ),
		'page_title'                => '',
		'page_description_callback' => 'wyr_fakes_settings_page_description',
		'page_callback'             => 'wyr_fakes_settings_page',
		'fields'                    => array(
			'wyr_fake_reaction_count_base' => array(
				'title'             => _x( 'Count base', 'Settings Page', 'wyr' ),
				'callback'          => 'wyr_fakes_setting_reaction_count_base',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'wyr_fake_reactions_randomize' => array(
				'title'             => _x( 'Randomize fake reactions?', 'Settings Page', 'wyr' ),
				'callback'          => 'wyr_fakes_setting_reactions_randomize',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'wyr_fake_reactions_disable_for_new' => array(
				'title'             => _x( 'Disable for new submissions?', 'Settings Page', 'wyr' ),
				'callback'          => 'wyr_fakes_setting_disable_for_new',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	) );
}

function wyr_register_fakes_settings_page( $pages ) {
	$pages[ wyr_get_fakes_settings_page_id() ] = wyr_get_fakes_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function wyr_fakes_settings_page_description() {}

/**
 * Settings page
 */
function wyr_fakes_settings_page() {
	$page_id        = wyr_get_fakes_settings_page_id();
	$page_config    = wyr_get_fakes_settings_page_config();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'What\'s Your Reaction Settings', 'wyr' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php wyr_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'wyr' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

function wyr_fakes_setting_reaction_count_base() {
	?>
	<input type="number" name="wyr_fake_reaction_count_base" id="wyr_fake_reaction_count_base" value="<?php echo esc_attr( wyr_get_fake_reaction_count_base() ); ?>" />
	<br />
	<small>
		<?php
		echo _x( 'Fake reactions for a post are calculated based on this value and a post creation date (older posts\' reactions are closer to the count base).', 'Settings Page', 'wyr' ) .
		'<br />' .
		_x( 'Leave empty to not use the "Fake reactions" feature.', 'Settings Page', 'wyr' );
		?>
	</small>
	<?php
}

function wyr_fakes_setting_reactions_randomize() {
	$randomize = wyr_randomize_fake_reactions();
	?>
	<input type="checkbox" name="wyr_fake_reactions_randomize" id="wyr_fake_reactions_randomize" value="standard"<?php checked( $randomize, true ); ?> />
	<?php
}

function wyr_fakes_setting_disable_for_new() {
	$disable = wyr_disable_fake_reactions_for_new_submissions();
	?>
	<input type="checkbox" name="wyr_fake_reactions_disable_for_new" id="wyr_fake_reactions_disable_for_new" value="standard"<?php checked( $disable, true ); ?> />
	<?php
}
