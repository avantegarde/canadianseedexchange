<?php
/**
 * Snax Collections
 *
 * @package snax
 * @subpackage Formats
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

require_once trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-abstract-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-history-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-custom-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'lib/class-snax-user-collection.php';

require_once trailingslashit( dirname( __FILE__ ) ) . 'functions.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'helpers.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'template.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'ajax.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'abstract/functions.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'abstract/history-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'abstract/read-later-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'abstract/favourites-collection.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'custom/functions.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'shortcodes.php';

// BuddyPress integration.
if ( snax_can_use_plugin( 'buddypress/bp-loader.php' ) ) {
	require_once trailingslashit( dirname( __FILE__ ) ) . 'plugins/buddypress/functions.php';
}

if ( is_admin() ) {
	require_once trailingslashit( dirname( __FILE__ ) ) . 'settings.php';
}