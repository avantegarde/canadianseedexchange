<?php
/**
 * Snax MyCred Hook class
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class Snax_myCRED_Hook
 */
class Snax_myCRED_Hook extends myCRED_Hook {

	/**
	 * Removes creds from a given user
	 *
	 * @param string $ref           Optional. Reference type (e.g. snax_vote).
	 * @param int    $ref_id        Optional. Reference id (e.g post id).
	 * @param int    $user_id       Optional. Id of the user that points belongs to.
	 * @param array  $data          Optional. Extra data saved in a log entry.
	 * @param string $type          Optional. myCRED type.
	 *
	 * @return bool
	 */
	protected function remove_creds( $ref, $ref_id, $user_id, $data, $type ) {
		$log_entry = $this->get_log_entry( $ref, $ref_id, $user_id, $data, $type );

		if ( ! $log_entry ) {
			return false;
		}

		$user_id = $log_entry->user_id;
		$amount  = -1 * $log_entry->creds;

		// Remove log entry.
		global $wpdb;

		$rows = $wpdb->delete( $this->core->get_log_table(), array( 'id' => $log_entry->id ), array( '%d' ) );

		if ( $rows ) {
			// Update balance
			$this->core->update_users_balance( (int) $user_id, $amount, $this->mycred_type );

			// Update total balance (if enabled).
			if ( MYCRED_ENABLE_TOTAL_BALANCE ) {
				$this->core->update_users_total_balance( (int) $user_id, $amount, $this->mycred_type );
			}

			return true;
		}

		return false;
	}

	/**
	 * Finds a log entry
	 *
	 * @param string $ref           Optional. Reference type.
	 * @param int    $ref_id        Optional. Reference id.
	 * @param int    $user_id       Optional. Id of the user that points from this entry log belongs to.
	 * @param array  $data          Optional. Extra data saved in a log entry.
	 * @param string $type          Optional. myCRED type.
	 *
	 * @return Log entry object on success or false on fail
	 */
	protected function get_log_entry( $ref = null, $ref_id = null, $user_id = null, $data = null, $type = null ) {
		global $wpdb;

		$wheres   = array();

		if ( $ref !== null )
			$wheres[] = $wpdb->prepare( "ref = %s", $ref );

		if ( $ref_id !== null )
			$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

		if ( $user_id !== null )
			$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );

		if ( $data !== null )
			$wheres[] = $wpdb->prepare( "data = %s", maybe_serialize( $data ) );

		if ( $type === null ) $type = $this->get_point_type_key();
		$wheres[] = $wpdb->prepare( "ctype = %s", $type );

		$where    = implode( ' AND ', $wheres );

		if ( ! empty( $wheres ) ) {

			$entry = $wpdb->get_row( "SELECT * FROM {$this->core->get_log_table()} WHERE {$where};" );

			return $entry;
		}

		return false;
	}
}
