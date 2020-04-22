<?php
/**
 * Social Login functions
 *
 * @package snax
**/

require_once plugin_dir_path( __FILE__ ) . 'lib/class-snax-social-login-logger.php';
require_once plugin_dir_path( __FILE__ ) . 'lib/class-snax-social-login.php';

add_action( 'snax_login_form_top',      'snax_slog_render_links_in_snax_login_form' );
add_action( 'login_form',               'snax_slog_render_links_in_login_form' );
add_action( 'login_init',               'snax_slog_dont_render_links_on_admin_form' );
add_action( 'bp_before_register_page',  'snax_slog_render_links_on_register_page', 10 );
add_action( 'wp_footer',                'snax_slog_render_login_error', 10 );
add_filter( 'get_avatar',               'snax_slog_get_avatar', 10, 5 );
add_action( 'wp_head',                  'snax_slog_facebook_after_authentication', 1 );

// BuddyPress integration.
add_filter( 'bp_core_fetch_avatar',     'snax_slog_bp_core_fetch_avatar', 10, 3 );

/**
 * Social Login init actions
 */
function snax_slog_init() {
	Snax_Social_Login::get_instance();
	snax_slog_wpsl_migration();
	snax_slog_bimber_migration();
}

/**
 * Return config
 *
 * @return array        Config array.
 */
function snax_slog_get_config() {
	$config = array();

	$providers        = snax_slog_get_providers();
	$providers_creds  = snax_slog_get_providers_creds();
	$query_var        = snax_get_url_var( 'social-login-with', '-' );

	foreach( $providers as $provider => $provider_config ) {
		$config[ $provider ] = array(
			// Location where to redirect users once they authenticate with the network.
			'callback' => home_url( sprintf( '?%s=%s', $query_var, $provider ) ),
			// Application credentials.
			'keys' => array(
				'id'        => isset( $providers_creds[ $provider ] ) ? $providers_creds[ $provider ]['app_id'] : '',
				'secret'    => isset( $providers_creds[ $provider ] ) ? $providers_creds[ $provider ]['app_secret'] : '',
			),
		);
	}

	return apply_filters( 'snax_slog_config', $config );
}

/**
 * Render social buttons in the Snax login popup
 */
function snax_slog_render_links_in_snax_login_form() {
	$template = snax_slog_location_popup_tpl();

	snax_slog_render_links( array(
		'template' => $template,
	) );
}

/**
 * Render social buttons in WordPress login form
 */
function snax_slog_render_links_in_login_form() {
	$template = snax_slog_location_login_form_tpl();
	?>
	<div>
		<p><?php echo esc_html_x( 'Or log in with social network:', 'Social Login', 'snax' ); ?></p>
		<?php snax_slog_render_links( array(
			'template' => $template,
		) ); ?>
	</div>
	<?php
}

function snax_slog_dont_render_links_on_admin_form() {
	remove_action( 'login_form', 'snax_slog_render_links_in_login_form' );
}

/**
 * Render social buttons before the BuddyPress register page
 */
function snax_slog_render_links_on_register_page() {
	if ( 'completed-confirmation' == bp_get_current_signup_step() ) {
		return;
	}

	if ( apply_filters( 'snax_bp_register_page_with_sidebar', true ) ) {
		$heading = _x( 'Register with your social network:', 'BuddyPress register form', 'snax' );
	} else {
		$heading = _x( 'With social network:', 'BuddyPress register form', 'snax' );
	}

	$template = snax_slog_location_register_page_tpl();
	?>
	<div class="bp-register-wpsl">
		<h3 class="g1-beta"><?php echo esc_html( $heading ); ?></h3>
		<?php snax_slog_render_links( array(
			'template' => $template,
		) ); ?>
	</div>

	<h3 class="g1-beta"><?php echo esc_html_x( 'Or register with your email:', 'BuddyPress register form', 'snax' ); ?></h3>
	<?php
}

/**
 * Render social login links
 *
 * @param array $atts       Parameters.
 */
function snax_slog_render_links( $atts ) {
	extract( shortcode_atts( array(
		'template' => 'buttons',
	), $atts, 'snax_slog_links' ) );

	$items_class = array(
		'snax-social-login-items',
		'snax-social-login-items-tpl-' . $template,
	);

	$providers = snax_slog_get_active_providers();
	$config    = snax_slog_get_config();

	?>
	<div class="snax-social-login-links snax-social-login-links-visible">
		<ul class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $items_class ) ); ?>">
			<?php foreach( $providers as $provider ) : ?>
				<?php
				$provider_login_url = add_query_arg(
					array( 't' => 'user' ), // Triggered by a user.
					$config[ $provider ]['callback']
				);
				$provider_class = array(
					'snax-social-login',
					'snax-social-login-' . strtolower( $provider ),
				);

				$link_title = sprintf( esc_attr_x( 'Connect with %s', 'Social Login', 'snax' ), $provider );
				?>
				<li class="snax-social-login-item">
					<a rel="nofollow" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $provider_class ) ); ?>" href="<?php echo esc_url( $provider_login_url ); ?>" title="<?php echo esc_attr( $link_title ); ?>">
						<?php echo esc_html( $provider ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	do_action( 'snax_slog_after_links' );
}

/**
 * Render failed login message to a user
 */
function snax_slog_render_login_error() {
	$query_var = snax_get_url_var( 'social-login-failed', '-' );
	$session = filter_input( INPUT_GET, $query_var, FILTER_SANITIZE_STRING );

	if ( empty( $session ) ) {
		return;
	}

	$transient_name = sprintf( 'snax_social_login_error_%s', $session );

	$transient = get_transient( $transient_name );

	if ( ! $transient ) {
		return;
	}

	$error_message = $transient['message'];

	if ( current_user_can( 'administrator' ) ) {
		$error_message .= ' ' . sprintf( '(code: %s, session: %s)', $transient['code'], $session );
	}

	set_query_var( 'snax_error_message', $error_message );
	snax_get_template_part( 'social-login/note-error' );

	// Clean up.
	delete_transient( $transient );
}

/**
 * Run WPSL migration
 */
function snax_slog_wpsl_migration() {
	// Run migration just once.
	if ( get_option( 'snax_slog_wpsl_migrated' ) ) {
		return;
	}

	$wpsl_config = array(
		'Facebook' => array(
			'enabled'    => (bool) get_option( 'wsl_settings_Facebook_enabled', false ),
			'app_id'     => get_option( 'wsl_settings_Facebook_app_id', '' ),
			'app_secret' => get_option( 'wsl_settings_Facebook_app_secret', '' ),
		),
		'Google' => array(
			'enabled'    => (bool) get_option( 'wsl_settings_Google_enabled', false ),
			'app_id'     => get_option( 'wsl_settings_Google_app_id', '' ),
			'app_secret' => get_option( 'wsl_settings_Google_app_secret', '' ),
		),
		'Twitter' => array(
			'enabled'    => (bool) get_option( 'wsl_settings_Twitter_enabled', false ),
			'app_id'     => get_option( 'wsl_settings_Twitter_app_key', '' ),
			'app_secret' => get_option( 'wsl_settings_Twitter_app_secret', '' ),
		),
		'Instagram' => array(
			'enabled'    => (bool) get_option( 'wsl_settings_Instagram_enabled', false ),
			'app_id'     => get_option( 'wsl_settings_Instagram_app_id', '' ),
			'app_secret' => get_option( 'wsl_settings_Instagram_app_secret', '' ),
		),
		'LinkedIn' => array(
			'enabled'    => (bool) get_option( 'wsl_settings_LinkedIn_enabled', false ),
			'app_id'     => get_option( 'wsl_settings_LinkedIn_app_key', '' ),
			'app_secret' => get_option( 'wsl_settings_LinkedIn_app_secret', '' ),
		),
	);

	$providers        = snax_slog_get_providers();
	$providers_creds  = (array) get_option( 'snax_slog_providers_creds', array() );
	$active_providers = (array) get_option( 'snax_slog_active_providers', array() );

	foreach ( $providers as $provider => $provider_config ) {
		if ( ! isset( $wpsl_config[ $provider ] ) ) {
			continue;
		}

		// Run only if the provider is not activated.
		if ( ! in_array( $provider, $active_providers ) && $wpsl_config[ $provider ]['enabled'] ) {
			$active_providers[] = $provider;
		}

		// Run only if the provider's config is not set.
		if ( ! isset( $providers_creds[ $provider ] ) ) {
			$providers_creds[ $provider ] = array(
				'app_id' => $wpsl_config[ $provider ]['app_id'],
				'app_secret' => $wpsl_config[ $provider ]['app_secret'],
			);
		}
	}

	update_option( 'snax_slog_providers_creds', $providers_creds );
	update_option( 'snax_slog_active_providers', $active_providers );

	// Migration done.
	update_option( 'snax_slog_wpsl_migrated', date('Y-m-d H:i:s') );
}

/**
 * Run Bimber migration
 */
function snax_slog_bimber_migration() {
	// Run migration just once.
	if ( get_option( 'snax_slog_bimber_migrated' ) ) {
		return;
	}

	$bimber_options = get_option( 'bimber_theme_options', array() );

	// Enabled?
	if ( ! empty( $bimber_options['gdpr_enabled'] ) ) {
		update_option( 'snax_slog_gdpr_enabled', 'standard' );
	}

	// Consent text.
	if ( ! empty( $bimber_options['gdpr_wpsl_consent'] ) ) {
		update_option( 'snax_slog_gdpr_consent_text', $bimber_options['gdpr_wpsl_consent'] );
	}

	// Migration done.
	update_option( 'snax_slog_bimber_migrated', date('Y-m-d H:i:s') );
}

function snax_slog_get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	// Only overwrite Gravatar avatars.
	if ( apply_filters( 'snax_slog_override_only_gravatar_profile_picture', true ) && ! stristr( strtolower( $avatar ), 'gravatar.com' ) ) {
		return $avatar;
	}

	$user_id = false;

	// Obtain numeric user id from mixed data.
	// $id_or_email:
	//  - author's User ID (an integer or string),
	//  - an E-mail Address (a string)
	//  - the comment object from the comment loop

	if ( is_numeric( $id_or_email ) ) {
		$user_id = (int) $id_or_email;
	} elseif ( is_string( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );

		if ( $user ) {
			$user_id = (int) $user->ID;
		}
	} elseif ( is_object( $id_or_email ) ) {
		$comment_obj = $id_or_email;

		$user = get_user_by( 'email', $comment_obj->comment_author_email );

		if ( $user ) {
			$user_id = (int) $user->ID;
		}
	}

	if ( ! $user_id ) {
		return $avatar;
	}

	$user_photo_url = snax_slog_get_user_photo_url( $user_id );

	if ( empty( $user_photo_url ) ) {
		return $avatar;
	}

	$slog_avatar = sprintf(
		'<img alt="%s" src="%s" class="%s" height="%d" width="%s" />',
		esc_attr( $alt ),
		esc_url( $user_photo_url ),
		implode( ' ', array_map( 'sanitize_html_class', array( 'avatar', 'avatar-' . $size, 'photo', 'snax-slog-avatar' ) ) ),
		absint( $size ),
		absint( $size )
	);

	return apply_filters( 'snax_slog_avatar', $slog_avatar, $avatar, $user_id, $size, $default, $alt );
}

/**
 * Reload page after successful authentication
 */
function snax_slog_facebook_after_authentication() {
	$fb_auth_hash = apply_filters( 'snax_slog_facebook_auth_hash', '#_=_' );

	// Short circuit. Use filter to empty the hash value and prevent reloading.
	if ( empty( $fb_auth_hash ) ) {
		return;
	}
	?>
	<script>if (0 === window.location.hash.indexOf('<?php echo esc_js( $fb_auth_hash ); ?>')) { window.location.hash = ''; history.pushState('', document.title, window.location.pathname); window.location.reload(); }</script>
	<?php
}

/**
 * Use Social Avatar as BP Profile Photo
 *
 * @param string $img_tag       Avatar HTML img tag.
 * @param array  $params        Avatar data.
 * @param int    $item_id       Item id.
 *
 * @return string       HTML tag
 */
function snax_slog_bp_core_fetch_avatar( $img_tag, $params, $item_id ) {
    if ( ! empty( $params['object'] ) && 'user' === $params['object'] ) {
        $img_tag = snax_slog_get_avatar( $img_tag, $item_id, $params['width'], '', $params['alt'] );
    }

    return $img_tag;
}
