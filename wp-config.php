<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'canadianseedexchange' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '^{<Y2~b=Qy0Ld7fD+SRSqBl;>N<W+!zhUdZ)#q&r^QFM-MueU/OQ!lH%?fxA-.}Y' );
define( 'SECURE_AUTH_KEY',  '):U9W0nv05}q6&F,Y5E^[RkV%!ZIDpnK@uMa~_-{:i-.<JH0{ *lFdIc~9X+xtF7' );
define( 'LOGGED_IN_KEY',    'dY]{Mmzsr2iQ Le+q_S8WV@OL9Ti9|0}9|^},VA{+E|]._k#(gc2ul}v@U%32<Oy' );
define( 'NONCE_KEY',        'mdo9AG.KVMTe0JJkPNn){g>@xci9g~<l03qbXqB<Fm/XWSeYzraNiMJ||GOtX>l+' );
define( 'AUTH_SALT',        '5[|%?sNe>NFcIh%umGtU~r`DgewMZ>,bsJU2dh=%RvX$m&{Mn(LQ+Vb72a<ZkJJJ' );
define( 'SECURE_AUTH_SALT', 'kXcFZvN`!)4NEvfEX6tQmA!ow:H5PO#QA/80tf+qGfv>zGpj{ad}k%XSs0]4+7TD' );
define( 'LOGGED_IN_SALT',   ']5@!6DE)eI&F*g36jk-i2B+1|%R]T}Q*n#V~DUTlp0YZ6#niBnG~u|)2z!x{`2]3' );
define( 'NONCE_SALT',       'lEN89Y`9u cwyhiHg>>r:/zemL~BP^/,wlV_V4Y:.(s$wXenr#+;]~ujztJ>Kabh' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
