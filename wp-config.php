<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testingwp' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         's])K=zS8L(60s/}V~~{{%le~2Mgk?%OmXM|Th`;qfT*an#x``]i,<zw$}^$Vw8|G' );
define( 'SECURE_AUTH_KEY',  'zr`uI:=YJW7lR*,4H/B*[h;YV*,Sfo0^tg+Xy>jxo,wK$XLp<.503!hHbE*WH|u#' );
define( 'LOGGED_IN_KEY',    'hTTm5<cH3t+!iA:L{NxDW=]Wv-n3at;:(.9YrT2m$5eXz]/jo5| bsMG.DrLN>_h' );
define( 'NONCE_KEY',        '5aY|Ea5OP7=^GlL87OaQ]bUz0-C!@!7QbZtca=hhM-u0MOL85f}4Rt7HTIAM3jP/' );
define( 'AUTH_SALT',        '<v oLXiQe<a4ieVzVlDgKLj,Kh.|WGb.&NOg$f:_eiBC{qO8UZGA? c_Vjv7=]Ec' );
define( 'SECURE_AUTH_SALT', 'l5e@!GH1Lb{46r8?rq,|<{Fi3XjPybk*d_RJ+z#at*Hi:uvRCMG0msryf>y`CG,o' );
define( 'LOGGED_IN_SALT',   'KVY02rU8}7Hs98gAc,<n[}|LtTT?s #+_]rdgd=.TnRHwO,|b%QI-Okh&:A-D@!&' );
define( 'NONCE_SALT',       '9omd1T&*#KhTkindB=3E`kBKLU{EU]lGu>RnJc%W~f%YJ,~LaDIb%8{ZWX,8{!<j' );

/**#@-*/

/**
 * WordPress database table prefix.
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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
