<?php
/**
 * Plugin Name: Youzer
 * Plugin URI:  https://youzer.kainelabs.com
 * Description: Youzer is a Community & User Profiles Management Solution with a Secure Membership System, Front-end Account Settings, Powerful Admin Panel, 14 Header Styles, +20 Profile Widgets, 16 Color Schemes, Advanced Author Widgets, Fully Responsive Design, Extremely Customizable and a Bunch of Unlimited Features Provided By KaineLabs.
 * Version:     2.4.7
 * Author:      Youssef Kaine
 * Author URI:  https://www.kainelabs.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: youzer
 * Domain Path: /languages/
 */
// return;
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! class_exists( 'Buddypress' ) ) {
    deactivate_plugins( plugin_basename( __FILE__ ) );
    wp_die( __( 'Please install and activate <strong><a href="https://wordpress.org/plugins/buddypress/">Buddypress</strong></a> plugin to use the <strong>Youzer</strong> plugin .', 'youzer' ), 'Youzer dependency check', array( 'back_link' => true ) );
    return;
}

// Youzer Basename
define( 'YOUZER_BASENAME', plugin_basename( __FILE__ ) );

define( 'YOUZER_FILE', __FILE__ );

require dirname( __FILE__ ) . '/class-youzer.php';

/**
 * The main function responsible for returning the one true BuddyPress Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $bp = buddypress(); ?>
 *
 * @return BuddyPress|null The one true BuddyPress Instance.
 */
function youzer() {
    return Youzer::instance();
}

/*
 * Hook BuddyPress early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Youzer,
 * to get their actions, filters, and overrides setup without
 * Youzer being in the way.
 */
if ( defined( 'YOUZER_LATE_LOAD' ) ) {
    add_action( 'plugins_loaded', 'youzer', (int) YOUZER_LATE_LOAD );
} else {

    do_action( 'before_youzer_init' );
    
    // Add Legacy Theme Support.
    add_theme_support( 'buddypress-use-legacy' );
    
    // Set Globals.    
    $GLOBALS['Youzer'] = youzer();

    do_action( 'after_youzer_init' );
}

/**
 * Determine whether BuddyPress is in the process of being deactivated.
 */
function yz_is_deactivation( $basename = '' ) {
    if ( ! function_exists( 'buddypress' ) ) {
        return;
    }

    $bp     = buddypress();
    $action = false;

    if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
        $action = $_REQUEST['action'];
    } elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
        $action = $_REQUEST['action2'];
    }

    // Bail if not deactivating.
    if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
        return false;
    }

    // The plugin(s) being deactivated.
    if ( 'deactivate' == $action ) {
        $plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
    } else {
        $plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
    }

    // Set basename if empty.
    if ( empty( $basename ) && !empty( $bp->basename ) ) {
        $basename = $bp->basename;
    }

    // Bail if no basename.
    if ( empty( $basename ) ) {
        return false;
    }

    // Is bbPress being deactivated?
    return in_array( $basename, $plugins );

}
/**
 * Youzer Init
 */
function youzer_init() {
    do_action( 'youzer_init' );
}

add_action( 'init', 'youzer_init' );