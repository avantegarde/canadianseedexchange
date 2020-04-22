<?php
/**
 * Social Login
 *
 * @package snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax_Social_Login' ) ) :

	/**
	 * Class Snax_Social_Login
	 */
	class Snax_Social_Login {

		/**
		 * Object instance
		 *
		 * @var Snax_Social_Login
		 */
		private static $instance;

		protected $config;
		protected $debug_mode;
		protected $logger_session;

		/**
		 * Snax_Social_Login constructor.
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'authenticate_user' ) );
		}

		/**
		 * Return the only existing instance of Snax_Social_Login object
		 *
		 * @return Snax_Social_Login
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Process user authentication
		 *
		 * @return void
		 */
		public function authenticate_user() {

			if ( is_user_logged_in() ) {
				return;
			}

			// Init env.
			$this->config     = snax_slog_get_config();
			$this->debug_mode = snax_slog_debug_mode_enabled();

			if ( ! $this->is_login_request() ) {
				return;
			}

			$this->init_logger();

			// The authenticate_user() function is triggered twice:
			// First, by a user when the social login action in triggered
			// Second, by provider when it fires associated callback
			$trigger = filter_input( INPUT_GET, 't', FILTER_SANITIZE_STRING );

			if ( 'user' !== $trigger ) {
				$this->log( sprintf( '%s login requested by a user', $this->get_requested_provider() ) );
				$this->log( sprintf( '%s got back after successful user authentication.', $this->get_requested_provider() ) );
			}

			if ( ! class_exists( 'Hybridauth\Hybridauth' ) ) {
				$plugin_dir = snax_get_plugin_dir();

				require_once  trailingslashit( $plugin_dir ) . 'includes/social-login/lib/hybridauth/src/autoload.php';
				require_once  trailingslashit( $plugin_dir ) . 'includes/social-login/lib/hybridauth/src/Hybridauth.php';
			}

			$provider     = $this->get_requested_provider();
			$user_profile = false;

			try {
				$class = "Hybridauth\\Provider\\$provider";

				// Instantiate adapter.
				$adapter = new $class( $this->config[ $provider ] );

				// Attempt to authenticate the user.
				$adapter->authenticate();

				if ( $adapter->isConnected() ) {
					$this->log( sprintf( 'User is connected to %s', $provider ) );

					$user_profile = $adapter->getUserProfile();
				} else {
					throw new Exception( 'Authentication failed. User is not connected' );
				}

				// Disconnect the adapter.
				$adapter->disconnect();

			} catch( Exception $e ) {
				$this->redirect_on_error( 'HYBRID_AUTH_FAILED', $e->getMessage() );
			}

			$uid        = $user_profile->identifier;
			$user_email = $user_profile->email;

			$this->log( sprintf( 'Looking for a user based on the profile data returned by %s (UID: %s, EMAIL: %s)', $provider, $uid, $user_email ) );

			$user_id = $this->find_user( $provider, $uid, $user_email );

			if ( $user_id ) {
				$this->log( sprintf( 'User with ID %d found', $user_id ) );

				// Network installation.
				if ( is_multisite() ) {
					$blog_id = get_current_blog_id();

					// Add the user to the current site.
					if ( ! is_user_member_of_blog( $user_id, $blog_id ) ) {
						$this->log( sprintf( 'User is not associated to the site (site ID within network: %d)', $blog_id ) );

						$blog_default_role = get_option( 'default_role' );

						if ( ! $blog_default_role ) {
		        			$blog_default_role = 'snax_author';
						}

						$this->log( sprintf( 'Adding user to the site using role "%s"', $blog_default_role ) );

						add_user_to_blog( $blog_id, $user_id, $blog_default_role );
					} else {
						$this->log( 'User is already associated to the current site' );
					}
				}

				$this->log( 'Success. User is logging in...' );

				$this->log_in_user( $user_id );
			} else {
				$this->log( 'User not found' );
				$this->log( 'Creating new account...' );

				if ( ! $this->is_registration_enabled() ) {
					$this->redirect_on_error( 'REGISTRATION_DISABLED', _x( 'Registration is disabled. Account was not created.', 'Social Login', 'snax' ) );
				}

				$user_id = $this->create_user( $provider, $user_profile );

				if ( $user_id ) {
					$this->log( sprintf( 'User with ID %d created', $user_id ) );
					$this->log( 'Success. User is logging in...' );

					$this->log_in_user( $user_id );
				}
			}
		}

		/**
		 * Create a new user account
		 *
		 * @param string   $provider            Provider name.
		 * @param stdClass $user_profile        User data.
		 *
		 * @return bool|int|WP_Error        User id on success, false or WP_Error when failed
		 */
		protected function create_user( $provider, $user_profile ) {
			$display_name = $this->get_user_display_name( $user_profile );
			$login        = $this->get_user_unique_login( $user_profile );
			$email        = $this->get_user_email( $user_profile );

			if ( empty( $email ) ) {
				$email = sprintf( '%s@fake-%s.com', $login, $provider );
				$email = apply_filters( 'snax_slog_user_fake_email', $email, $login, $provider );
			}

			$user_data = array(
				'user_login'    => $login,
				'user_email'    => $email,
				'display_name'  => $display_name,
				'first_name'    => $user_profile->firstName,
				'last_name'     => $user_profile->lastName,
				'user_url'      => $user_profile->profileURL,
				'description'   => $user_profile->description,
				'user_pass'     => wp_generate_password()
			);

			$user_id = wp_insert_user( $user_data );

			// Store user's meta.
			add_user_meta( $user_id, '_snax_slog_provider', $provider );
			add_user_meta( $user_id, '_snax_slog_uid', $user_profile->identifier );
			add_user_meta( $user_id, '_snax_slog_photo_url', $user_profile->photoURL );

			return $user_id;
		}

		/**
		 * Return user's display name
		 *
		 * @param stdClass $user_profile    User profile data.
		 *
		 * @return string
		 */
		protected function get_user_display_name( $user_profile ) {
			if ( ! empty( $user_profile->displayName ) ) {
				return $user_profile->displayName;
			}

			$display_name = '';

			if ( ! empty( $user_profile->firstName ) ) {
				$display_name .= $user_profile->firstName;
			}

			if ( ! empty( $user_profile->lastName ) ) {
				$display_name .= ' ' . $user_profile->lastName;
			}

			return $display_name;
		}

		/**
		 * Return user's unique login
		 *
		 * @param stdClass $user_profile    User profile data.
		 *
		 * @return string|bool              Login on success, false otherwise.
		 */
		protected function get_user_unique_login( $user_profile ) {
			static $i;

			// Sanitize user login.
			$login = preg_replace( '/\s+/', '', sanitize_user( $user_profile->displayName, true ) );

			if ( empty( $login ) ) {
				return false;
			}

			// Add next number to the login to make it unique.
			if ( $i ) {
				$login .= '-' . $i;
			}

			if ( ! username_exists( $login ) ) {
				$i = null;

				return $login;
			} else {
				$i++;

				return $this->get_user_unique_login( $user_profile );
			}
		}

		/**
		 * Return user's validated email
		 *
		 * @param stdClass $user_profile    User profile data.
		 *
		 * @return string|bool              Email on success, false otherwise.
		 */
		protected function get_user_email( $user_profile ) {

			if ( ! empty( $user_profile->email ) && is_email( $user_profile->email ) ) {
				return $user_profile->email;
			}

			return false;
		}

		/**
		 * Check whether new accounts can be created
		 */
		protected function is_registration_enabled() {
			if ( is_multisite() ) {
				$site_registration = get_site_option( 'registration' );

				$enabled = in_array( $site_registration, array( 'user', 'all' ) );
			} else {
				$enabled = (bool) get_option( 'users_can_register' );
			}

			return $enabled;
		}

		/**
		 * Log in user
		 *
		 * @param int    $user_id               User ID.
		 * @param string $redirection_url       URL where user will be redirected after successful login.
		 */
		protected function log_in_user( $user_id, $redirection_url = '' ) {
			$user = get_user_by( 'id', $user_id );
			$secure_cookie = is_ssl();

			wp_set_auth_cookie( $user_id, true, $secure_cookie );

			/**
			 * Fires after the user has successfully logged in.
			 *
			 * @since 1.5.0
			 *
			 * @param string  $user_login Username.
			 * @param WP_User $user       WP_User object of the logged-in user.
			 */
			do_action( 'wp_login', $user->user_login, $user );

			if ( empty( $redirection_url ) ) {
				if ( wp_get_referer() ) {
					$redirection_url = wp_get_referer();
				}
				else {
					$redirection_url = get_home_url();
				}
			}

			wp_safe_redirect( $redirection_url );
			exit;
		}

		/**
		 * Find existing user by provided data
		 *
		 * @param string $provider      Provider name (e.g. Facebook).
		 * @param string $uid           Unique user's ID on the connected provider.
		 * @param string $email         User's email.
		 *
		 * @return bool|int             User's ID or false if not found.
		 */
		protected function find_user( $provider, $uid, $email ) {
			$user_id = false;

			// Look by provider and uid.
			$users = get_users(
				array(
					'number' => 1,
					'fields' => 'ID',
					'meta_query' => array(
						array(
							'key'   => '_snax_slog_provider',
							'value' => strtolower( $provider ),
						),
						array(
							'key'   => '_snax_slog_uid',
							'value' => $uid,
						)
					)
				)
			);

			if ( ! empty( $users ) ) {
				$user_id = $users[0];
			}

			// Look by email.
			if ( ! $user_id && ! empty( $email ) ) {
				$user_id = email_exists( $email );
			}

			return $user_id;
		}

		/**
		 * Get the provider name from request URL
		 *
		 * @return mixed        Provider name on success, NULL or false otherwise.
		 */
		protected function get_requested_provider() {
			$query_var = snax_get_url_var( 'social-login-with', '-' );

			$login_query_arg = apply_filters( 'snax_slog_query_arg', $query_var );

			$provider = filter_input( INPUT_GET, $login_query_arg, FILTER_SANITIZE_STRING );

			return $provider;
		}

		/**
		 * Check whether a user requested login
		 *
		 * @return bool
		 */
		protected function is_login_request() {
			$provider = $this->get_requested_provider();

			if ( ! $provider ) {
				return false;
			}

			return $this->is_valid_provider( $provider );
		}

		/**
		 * Check whether the provider is registered
		 *
		 * @param string $provider      Provider name.
		 *
		 * @return bool
		 */
		protected function is_valid_provider( $provider ) {
			return in_array( $provider, $this->get_provider_list() );
		}

		/**
		 * Return a list of registered providers
		 *
		 * @return array
		 */
		protected function get_provider_list() {
			return array_keys( $this->config );
		}

		/**
		 * Init logger
		 */
		protected function init_logger() {
			$this->logger_session = uniqid();
		}

		/**
		 * Log info for debug purposes
		 *
		 * @param string $message       Log message
		 */
		protected function log( $message ) {
			if ( $this->debug_mode ) {
				Snax_Social_Login_Logger::add_info( $this->logger_session, $message );
			}
		}

		/**
		 * Log error and redirect
		 *
		 * @param string $code              Error code.
		 * @param string $message           Error message.
		 * @param string $redirection_url   Optional. Redirection URL.
		 */
		protected function redirect_on_error( $code, $message, $redirection_url = '' ) {
			if ( $this->debug_mode ) {
				Snax_Social_Login_Logger::add_error( $this->logger_session, $message );
			}

			if ( empty( $redirection_url ) ) {
				if ( wp_get_referer() ) {
					$redirection_url = wp_get_referer();
				}
				else {
					$redirection_url = get_home_url();
				}
			}

			$error_data = array(
				'code'    => $code,
				'message' => $message,
			);

			$transient_name = sprintf( 'snax_social_login_error_%s', $this->logger_session );

			set_transient( $transient_name, $error_data, 60 * 60 * 24 ); // TTL: 24h.

			$query_var = snax_get_url_var( 'social-login-failed', '-' );

			$redirection_url = add_query_arg(
				array(
					$query_var => $this->logger_session
				),
				$redirection_url
			);

			wp_safe_redirect( $redirection_url );
			exit;
		}
	}

endif;
