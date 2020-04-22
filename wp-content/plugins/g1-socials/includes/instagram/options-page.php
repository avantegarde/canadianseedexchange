<?php
/**
 * Options Page for Instagram
 *
 * @package G1 Socials
 * @subpackage Functions
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

add_filter( 'g1_socials_options_tabs', 'g1_socials_instagram_add_options_tab' );
add_action( 'admin_menu', 'g1_socials_add_instagram_options_sections_and_fields' );

/**
 * Add Options Tab
 */
function g1_socials_instagram_add_options_tab( $tabs = array() ) {
	$tabs['g1_socials_instagram'] = array(
		'path'     => add_query_arg( array(
			'page' => g1_socials_options_page_slug(),
			'tab'  => 'g1_socials_instagram',
		), '' ),
		'label'    => esc_html__( 'Instagram', 'g1_socials' ),
		'settings' => 'g1_socials_instagram',
	);
	return $tabs;
}

/**
 * Add options page sections, fields and options.
 */
function g1_socials_add_instagram_options_sections_and_fields() {
	// Add setting section.
	add_settings_section(
		'g1_socials_instagram', // Section id.
		'', // Section title.
		'g1_socials_options_instagram_description_renderer_callback', // Section renderer callback with args pass.
		'g1_socials_instagram' // Page.
	);

	// Enabled.
	add_settings_field(
		'g1_socials_instagram_enabled', // Field ID.
		esc_html_x( 'Enabled', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_enabled',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_enabled' // Option name.
	);

	if ( ! get_option( 'g1_socials_instagram_enabled', false ) ) {
		return;
	}

	// App ID.
	add_settings_field(
		'g1_socials_instagram_app_id', // Field ID.
		esc_html_x( 'App ID', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_app_id',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_app_id' // Option name.
	);

	// App Secret.
	add_settings_field(
		'g1_socials_instagram_app_secret', // Field ID.
		esc_html_x( 'App Secret', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_app_secret',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_app_secret' // Option name.
	);


	$app_id = get_option( 'g1_socials_instagram_app_id' );
	$app_secret = get_option( 'g1_socials_instagram_app_secret' );

	if ( empty( $app_id ) || empty( $app_secret ) ) {
		return;
	}

	// Access token.
	add_settings_field(
		'g1_socials_instagram_token', // Field ID.
		esc_html_x( 'Access Token', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_token',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_token' // Option name.
	);

	// Follow text.
	add_settings_field(
		'g1_socials_instagram_follow_text', // Field ID.
		esc_html_x( 'Follow text', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_follow_text',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_follow_text' // Option name.
	);

	// Target.
	add_settings_field(
		'g1_socials_instagram_target', // Field ID.
		esc_html_x( 'Open links in', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_target',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_target' // Option name.
	);

	// Cache time.
	add_settings_field(
		'g1_socials_instagram_cache_time', // Field ID.
		esc_html_x( 'Cache time', 'Instagram Settings', 'g1_socials' ), // Field title.
		'g1_socials_options_instagram_fields_renderer_callback', // Callback.
		'g1_socials_instagram', // Page.
		'g1_socials_instagram', // Section.
		array(
			'field_for' => 'g1_socials_instagram_cache_time',
		) // Data for callback.
	);
	// Register setting.
	register_setting(
		'g1_socials_instagram', // Option group.
		'g1_socials_instagram_cache_time' // Option name.
	);
}

function g1_socials_options_instagram_description_renderer_callback() {}

/**
 * Options fields renderer.
 *
 * @param array $args Field arguments.
 */
function g1_socials_options_instagram_fields_renderer_callback( $args ) {

	// Action to render outside fields. For example other plugins supported fields.
	do_action( 'g1_socials_options_sponsor_field_renderer_action', $args );
	// Switch field.
	switch ( $args['field_for'] ) {

		case 'g1_socials_instagram_enabled':
			$option = get_option( $args['field_for'] );
			?>
			<input type="checkbox" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="standard"<?php checked( $option, 'standard' ); ?> />
			<?php
			break;

		case 'g1_socials_instagram_app_id':
			$option = get_option( $args['field_for'] );
			?>
			<input size="60" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<a href="#" onclick="jQuery('#g1-socials-client-id-info').toggle(); return false;">
				<?php echo esc_html_x( 'Where do I get this info?', 'Instagram Settings', 'g1_socials' ); ?>
			</a>
			<?php
			break;

		case 'g1_socials_instagram_app_secret':
			$option = get_option( $args['field_for'] );
			?>
			<input size="60" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $option ) ); ?>" />
			<a href="#" onclick="jQuery('#g1-socials-client-id-info').toggle(); return false;">
				<?php echo esc_html_x( 'Where do I get this info?', 'Instagram Settings', 'g1_socials' ); ?>
			</a>
			<div id="g1-socials-client-id-info" style="display: none;">
				<p>
					<?php printf( wp_kses_post( __( 'To get the App ID and App Secret, you will need to register a new application. This step-by-step <a href="%s" target="_blank">guide</a> will walk you through the whole process.', 'g1_socials' ) ), 'http://docs.bimber.bringthepixel.com/articles/g1-socials-plugin/instagram/index.html#setup' ); ?>
					<br />
					<br />
					<?php echo wp_kses_post( __( 'During the application creation, you will be asked to provide this <strong>Valid OAuth Redirect URIs</strong>:', 'g1_socials' ) ); ?>
					<br />
					<code>
						<?php echo esc_url( trailingslashit( home_url() ) ); ?>
					</code>
				</p>
			</div>
			<?php
			break;

		case 'g1_socials_instagram_token':
			// Read and save token before use.
			if ( ( $code = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING ) ) && ! filter_input( INPUT_GET, 'settings-updated' ) ) {
				$token = g1_socials_instagram_get_token( $code );

				if ( ! is_wp_error( $token ) ) {
					update_option( $args['field_for'], $token );
				} else {
					?>
					<div style="padding: 5px; color: #ff0000;"><?php echo esc_html( $token->get_error_message() ); ?></div>
					<?php
				}
			} else {
				$token = get_option( $args['field_for'] );
			}

			$token_owner      = g1_socials_instagram_get_token_owner( $token );
			$get_token_url = g1_socials_instagram_get_authorize_url();
			?>
			<input size="60" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( esc_html( $token ) ); ?>" />
			<a href="<?php echo esc_url( $get_token_url ); ?>" class="button button-primary"><?php echo esc_html_x( 'Get token', 'Instagram Settings', 'g1_socials' ); ?></a>
			<?php if ( $token && ! empty( $token_owner ) && $token_owner['token'] == $token ): ?>
			<br />
			<small>
				<?php printf( _x( 'The token is associated to account @%s', 'Instagram Settings', 'g1_socials' ), $token_owner['user']['username'] ) ?>
			</small>
		<?php endif; ?>
			<?php
			break;

		case 'g1_socials_instagram_follow_text':
			$option = get_option( $args['field_for'] );
			?>
			<input placeholder="<?php echo esc_html_x( 'Follow Me', 'Instagram Settings', 'g1_socials' ) ?>" size="40" type="text" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo wp_kses_post( $option ); ?>" />
			<br />
			<small>
				<?php echo esc_html_x( 'If not empty, the follow button to your Instagram account will be displayed', 'Instagram Settings', 'g1_socials' ); ?>
			</small>
			<?php
			break;

		case 'g1_socials_instagram_target':
			$option = get_option( $args['field_for'] );
			?>
			<select id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>">
				<option value="_blank"<?php selected( $option, '_blank' ) ?>><?php echo esc_html_x( 'New window', 'Instagram Settings', 'g1_socials' ); ?></option>
				<option value="_self"<?php selected( $option, '_self' ) ?>><?php echo esc_html_x( 'Current window', 'Instagram Settings', 'g1_socials' ); ?></option>
			</select>
			<?php
			break;

		case 'g1_socials_instagram_cache_time':
			$cache_time = get_option( $args['field_for'], 120 );
			?>
			<input size="10" type="number" id="<?php echo( esc_html( $args['field_for'] ) ); ?>" name="<?php echo( esc_html( $args['field_for'] ) ); ?>" value="<?php echo( absint( $cache_time ) ); ?>" placeholder="120" />
			<br />
			<small>
				<?php echo esc_html_x( 'In case of problems, set to 0 to disable cache. Cache can be disabled for testing purposes only', 'Instagram Settings', 'g1_socials' ); ?>
			</small>
			<?php
			break;

	}
}

function g1_socials_instagram_get_token( $auth_code ) {
	$app_id = get_option( 'g1_socials_instagram_app_id', '' );
	$app_secret = get_option( 'g1_socials_instagram_app_secret', '' );
	$redirect_uri = trailingslashit( home_url() ); // Not encoded.

	$response = wp_remote_post( 'https://api.instagram.com/oauth/access_token', array(
		'body' => array(
			'app_id'        => $app_id,
			'app_secret'    => $app_secret,
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => $redirect_uri,
			'code'          => $auth_code,
		),
	) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response_code = wp_remote_retrieve_response_code( $response );

	if ( 200 !== $response_code ) {
		$response_data = json_decode( wp_remote_retrieve_body( $response ), true );

		return new WP_Error( $response_data['code'], $response_data['message'] );
	}

	$response_body = wp_remote_retrieve_body( $response );

	$data = json_decode( $response_body, true );

	if ( empty( $data['access_token'] ) ) {
		return new WP_Error( 'missing_access_token', _x( 'Wrong response data. Access Token is missing.', 'Instagram', 'g1_socials' ) );
	}

	return $data['access_token'];
}

function g1_socials_instagram_get_token_owner( $token ) {
	$owner = get_option( 'g1_socials_instagram_token_owner', array() );

	// Update token.
	if ( $token && ( empty( $owner ) || $owner['token'] != $token ) ) {
		$ret = wp_remote_get( sprintf( 'https://graph.instagram.com/me?fields=id,username&access_token=%s', $token ) );

		$body = json_decode( $ret['body'], true );

		if ( ! empty( $body ) ) {
			$owner = array(
				'token' => $token,
				'user'  => $body,
			);

			update_option( 'g1_socials_instagram_token_owner', $owner );
		}
	}

	return $owner;
}

function g1_socials_instagram_get_authorize_url() {
	$app_id       = get_option( 'g1_socials_instagram_app_id', '' );
	$redirect_uri = urlencode( trailingslashit( home_url() ) );

	$url = sprintf( 'https://api.instagram.com/oauth/authorize?app_id=%s&redirect_uri=%s&scope=user_profile,user_media&response_type=code', $app_id, $redirect_uri );

	return apply_filters( 'g1_socials_instagram_update_token_url', $url );
}
