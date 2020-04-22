<?php
/**
 * Options Page for Twitter
 *
 * @package G1 Socials
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'g1_socials_options_tabs', 'g1_socials_twitter_add_options_tab' );
add_action( 'admin_menu', 'g1_socials_add_twitter_options_sections_and_fields' );

/**
 * Add Options Tab
 */
function g1_socials_twitter_add_options_tab( $tabs = array() ) {
	$tabs['g1_socials_twitter'] = array(
		'path'     => add_query_arg( array(
			'page' => g1_socials_options_page_slug(),
			'tab'  => 'g1_socials_twitter',
		), '' ),
		'label'    => esc_html__( 'Twitter', 'g1_socials' ),
		'settings' => 'g1_socials_twitter',
	);
	return $tabs;
}

/**
 * Add options page sections, fields and options.
 */
function g1_socials_add_twitter_options_sections_and_fields() {
	// Add setting section.
	add_settings_section(
		'g1_socials_twitter', // Section id.
		'', // Section title.
		'g1_socials_options_twitter_description_renderer_callback', // Section renderer callback with args pass.
		'g1_socials_twitter' // Page.
	);

	// Consumer key.
	add_settings_field(
		'g1_socials_twitter_consumer_key', // Field ID.
		esc_html( 'Consumer Key', 'g1_socials' ), // Field title.
		'g1_socials_options_twitter_fields_renderer_callback', // Callback.
		'g1_socials_twitter', // Page.
		'g1_socials_twitter', // Section.
		array(
			'field_for' => 'g1_socials_twitter_consumer_key',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_twitter', // Option group.
		'g1_socials_twitter_consumer_key' // Option name.
	);

	// Consumer secret.
	add_settings_field(
		'g1_socials_twitter_consumer_secret', // Field ID.
		esc_html( 'Consumer Secret', 'g1_socials' ), // Field title.
		'g1_socials_options_twitter_fields_renderer_callback', // Callback.
		'g1_socials_twitter', // Page.
		'g1_socials_twitter', // Section.
		array(
			'field_for' => 'g1_socials_twitter_consumer_secret',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_twitter', // Option group.
		'g1_socials_twitter_consumer_secret' // Option name.
	);

	// Access token.
	add_settings_field(
		'g1_socials_twitter_access_token', // Field ID.
		esc_html( 'Access Token', 'g1_socials' ), // Field title.
		'g1_socials_options_twitter_fields_renderer_callback', // Callback.
		'g1_socials_twitter', // Page.
		'g1_socials_twitter', // Section.
		array(
			'field_for' => 'g1_socials_twitter_access_token',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_twitter', // Option group.
		'g1_socials_twitter_access_token' // Option name.
	);

	// Access token secret.
	add_settings_field(
		'g1_socials_twitter_access_token_secret', // Field ID.
		esc_html( 'Access Token Secret', 'g1_socials' ), // Field title.
		'g1_socials_options_twitter_fields_renderer_callback', // Callback.
		'g1_socials_twitter', // Page.
		'g1_socials_twitter', // Section.
		array(
			'field_for' => 'g1_socials_twitter_access_token_secret',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_twitter', // Option group.
		'g1_socials_twitter_access_token_secret' // Option name.
	);
}

function g1_socials_options_twitter_description_renderer_callback() {
	?>
	<p>
		<?php esc_html_e( 'In order to get the Twitter feed working you need to provide access API keys and tokens.', 'g1_socials' ); ?><br />
		<?php esc_html_e( 'To get your API keys and tokens please do as follows:', 'g1_socials' ); ?><br />
	</p>
	<ol>
		<li>
			<?php printf( esc_html__( 'Go to the %s (log in if necessary)', 'g1_socials' ), '<a href="'. esc_url( 'https://apps.twitter.com/app/new' ) .'" target="_blank">'. esc_url( 'https://apps.twitter.com/app/new' ) .'</a>' ); ?>
		</li>
		<li>
			<?php esc_html_e( 'Enter all required fields', 'g1_socials' ); ?>
		</li>
		<li>
			<?php esc_html_e( 'Accept the Terms of Service and submit the form', 'g1_socials' ); ?>
		</li>
		<li>
			<?php esc_html_e( 'Your keys will be in the Keys and Access Tokens tab', 'g1_socials' ); ?>
		</li>
	</ol>
	<?php
}

/**
 * Options fields renderer.
 *
 * @param array $args Field arguments.
 */
function g1_socials_options_twitter_fields_renderer_callback( $args ) {
	// Action to render outside fields. For example other plugins supported fields.
	do_action( 'g1_socials_options_sponsor_field_renderer_action', $args );
	// Switch field.
	switch ( $args['field_for'] ) {
		case 'g1_socials_twitter_consumer_key':
			$option = get_option( $args['field_for'] );
			?>
			<input class="widefat" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<?php
			break;

		case 'g1_socials_twitter_consumer_secret':
			$option = get_option( $args['field_for'] );
			?>
			<input class="widefat" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<?php
			break;

		case 'g1_socials_twitter_access_token':
			$option = get_option( $args['field_for'] );
			?>
			<input class="widefat" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<?php
			break;

		case 'g1_socials_twitter_access_token_secret':
			$option = get_option( $args['field_for'] );
			?>
			<input class="widefat" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<?php
			break;
	}
}

