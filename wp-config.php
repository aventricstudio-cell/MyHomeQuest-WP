<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'myhomequest' );

/** Database username */
define( 'DB_USER', 'myhomequest' );

/** Database password */
define( 'DB_PASSWORD', 'NqUe8Edji4reR6c2zTxudV' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY',         '<vc,c90tk;N5L:Mp^]_|D7wh<r-9SXg2HHy$0c<A7*$cj2aZq&=;TqO1j+Bkp=&`');
define('SECURE_AUTH_KEY',  'Y++TFmy+XQ:-15Cq$eoD.|Pp6,&`IEw?2UzqB%Kb m6}|5g5G;:k$Sn3)/~Y!q#R');
define('LOGGED_IN_KEY',    '59b_G {LlcA3W{rYA3B!o-*`<5606%Gd>u|I#;|r+EGPK-2&^N8{)^d&e)z3Cx&H');
define('NONCE_KEY',        '/IGf8p/SHJnAbNO|0-|n|R,8oNVyTW-tI]QIHgdUnq+=I|*O~S^2h=FA|Cw<6US#');
define('AUTH_SALT',        '~.0.+.TNrs0|+m:z%KE9-ID.,QD>9s6H|YgV6oIPk$|4:-#PY=L3Nm,47 K k|~Y');
define('SECURE_AUTH_SALT', 'K2?00`9ea7$?@-hx#m:->p-<A7Fk20o(HmstmFvg:fz*%ijqi%S@Xq,d-GOwmB|K');
define('LOGGED_IN_SALT',   't&+P hP?-D@|$:mvMG)6mIChvFBEyw{{S$_)-cg9.GuaF5/@A?H~VAIRdO<1;}aD');
define('NONCE_SALT',       'LN/n}pM}zP&?em|;4[3*C~4?;0>{E-R@>l?,G)wF/Er9)IH6CTD7C]n=p%z&Uv_U');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'mhmxd_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);          // debug ON (required for log)
define('WP_DEBUG_DISPLAY', false); // screen pe error nahi dikhega
define('WP_DEBUG_LOG', true);      // log file banega
@ini_set('display_errors', 0);     // extra safety

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );