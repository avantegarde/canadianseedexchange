<?php
/**
 * Lazy Load Settings page
 *
 * @package media-ace
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'mace_settings_pages', 'mace_register_lazy_load_settings_page', 10 );

function mace_get_lazy_load_settings_page_id() {
	return apply_filters( 'mace_lazy_load_settings_page_id', 'mace-lazy_load-settings' );
}

function mace_get_lazy_load_settings_page_config() {
	$config =  array(
		'tab_title'                 => __( 'Lazy Load', 'mace' ),
		'page_title'                => __( 'Don\'t load images and embeds until the point at which they are really needed', 'mace' ),
		'page_description_callback' => 'mace_lazy_load_settings_page_description',
		'page_callback'             => 'mace_lazy_load_settings_page',
		'fields'                    => array(
			'mace_lazy_load_enabled' => array(
				'title'             => __( 'Enabled?', 'mace' ),
				'callback'          => 'mace_lazy_load_setting_enabled',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
		),
	);

	if ( mace_lazy_load_enabled() ) {
		$config['fields'] = array_merge( $config['fields'], array(
			'mace_lazy_load_images' => array(
				'title'             => __( 'Lazy load images', 'mace' ),
				'callback'          => 'mace_lazy_load_setting_images',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_lazy_load_images_unveilling_effect' => array(
				'title'             => __( 'Load image with unveilling effects', 'mace' ),
				'callback'          => 'mace_lazy_load_setting_images_unveilling_effect',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_lazy_load_embeds' => array(
				'title'             => __( 'Lazy load embeds', 'mace' ),
				'callback'          => 'mace_lazy_load_setting_embeds',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array(),
			),
			'mace_lazy_load_yt_player_args' => array(
				'title'             => __( 'YouTube player', 'mace' ),
				'callback'          => 'mace_lazy_load_setting_yt_player_args',
				'sanitize_callback' => 'mace_sanitize_text_array',
				'args'              => array(),
			),
		) );
	}

	return apply_filters( 'mace_lazy_load_settings_config', $config );
}

function mace_register_lazy_load_settings_page( $pages ) {
	$pages[ mace_get_lazy_load_settings_page_id() ] = mace_get_lazy_load_settings_page_config();

	return $pages;
}

/**
 * Settings page description
 */
function mace_lazy_load_settings_page_description() {}

/**
 * Settings page
 */
function mace_lazy_load_settings_page() {
	$page_id        = mace_get_lazy_load_settings_page_id();
	$page_config    = mace_get_lazy_load_settings_page_config();
	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'MediaAce Settings', 'mace' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php mace_admin_settings_tabs( $page_config['tab_title'] ); ?></h2>
		<form action="options.php" method="post">

			<?php settings_fields( $page_id ); ?>
			<?php do_settings_sections( $page_id ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'mace' ); ?>" />
			</p>

		</form>
	</div>

	<?php
}

/**
 * Lazy Load Enabled
 */
function mace_lazy_load_setting_enabled() {
	?>
	<input name="mace_lazy_load_enabled" id="mace_lazy_load_enabled" type="checkbox" <?php echo checked( mace_lazy_load_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Lazy Load Images
 */
function mace_lazy_load_setting_images() {
	?>
	<input name="mace_lazy_load_images" id="mace_lazy_load_images" type="checkbox" <?php echo checked( mace_lazy_load_images_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Lazy Load Images Unveilling Effect
 */
function mace_lazy_load_setting_images_unveilling_effect() {
	?>
	<input name="mace_lazy_load_images_unveilling_effect" id="mace_lazy_load_images_unveilling_effect" type="checkbox" <?php echo checked( mace_lazy_load_images_unveilling_effect_enabled() ); ?> value="standard" />
	<?php
}

/**
 * Lazy Load Embeds
 */
function mace_lazy_load_setting_embeds() {
	?>
	<input name="mace_lazy_load_embeds" id="mace_lazy_load_embeds" type="checkbox" <?php echo checked( mace_lazy_load_embeds_enabled() ); ?> value="standard" />

	<p class="description">
		<?php esc_html_e( 'For embeds that use iframe embed method (like YouTube or Instagram).', 'mace' ); ?>
	</p>
	<?php
}

/**
 * YouTube player arguments
 */
function mace_lazy_load_setting_yt_player_args() {
	$args = mace_get_lazy_load_yt_player_args();
	?>
	<p>
		<input name="mace_lazy_load_yt_player_args[rel]" id="mace_lazy_load_yt_player_args_rel" type="checkbox" <?php echo checked( 1, $args['rel'] ); ?> value="1" />
		<?php esc_html_e( 'show related videos when a playback is paused or ended', 'mace' ); ?>
	</p>

	<?php
}