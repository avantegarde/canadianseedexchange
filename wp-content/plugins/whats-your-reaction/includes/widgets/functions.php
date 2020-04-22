<?php
/**
 * Plugin resources loader
 *
 * @license For the full license information, please view the Licensing folder
 * that was distributed with this source code.
 *
 * @package whats-your-reaction
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once( trailingslashit( wyr_get_plugin_dir() ) . 'includes/widgets/wyr-widget-latest-reactions.class.php' );

add_action( 'widgets_init', 'wyr_widgets_init' );

/**
 * Init widgets
 */
function wyr_widgets_init() {
	register_widget( 'Wyr_Widget_Latest_Reactions' );
}