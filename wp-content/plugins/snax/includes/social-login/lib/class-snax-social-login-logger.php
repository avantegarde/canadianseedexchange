<?php
/**
 * Social Login Logger
 *
 * @package snax
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Snax_Social_Login_Logger' ) ) :

	/**
	 * Class Snax_Social_Login_Logger
	 */
	class Snax_Social_Login_Logger {

		const TYPE_INFO  = 'INFO';
		const TYPE_ERROR = 'ERROR';

		private static $log_option_name = 'snax_slog_log';

		/**
		 * Log info
		 *
		 * @param string $session   Session the message belongs to.
		 * @param string $message   Log message.
		 */
		public static function add_info( $session, $message ) {
			self::add( self::TYPE_INFO, $session, $message );
		}

		/**
		 * Log error
		 *
		 * @param string $session   Session the message belongs to.
		 * @param string $message   Log message.
		 */
		public static function add_error( $session, $message ) {
			self::add( self::TYPE_ERROR, $session, $message );
		}

		/**
		 * Log
		 *
		 * @param string $type      Type of log entry.
		 * @param string $session   Session the message belongs to.
		 * @param string $message   Log message.
		 */
		public static function add( $type, $session, $message ) {
			$log = self::get_log();

			// Init.
			if ( ! isset( $log[ $session ] ) ) {
				$log[ $session ] = array();
			}

			$log_entry = array(
				'type'      => $type,
				'date'      => current_time( 'mysql' ),
				'message'   => $message,
			);

			$log[ $session ][] = $log_entry;

			$max_stored_sessions = defined( 'SNAX_SLOG_MAX_SESSIONS' ) ? SNAX_SLOG_MAX_SESSIONS: 100;

			// Record only $max_stored_sessions last sessions in the log.
			if ( count( $log ) > $max_stored_sessions ) {
				// Remove first entry.

				$session_ids = array_keys( $log );
				unset( $log[ $session_ids[0] ] );
			}

			update_option( self::$log_option_name, $log );
		}

		public static function get_log() {
			return (array) get_option( self::$log_option_name, array() );
		}

	}

endif;

