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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */


define('AUTOSAVE_INTERVAL', 300 ); // seconds
define('WP_POST_REVISIONS', false );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'singhabeerfinder');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '123456789');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '[p$v}v0?mK6-?<iSP%9cwj32*W=VV]%Wx: ?5T[gGN*XW!%Lz8_Qc:5TOx$}k]n&');
define('SECURE_AUTH_KEY',  'l!hXRmz9-I+e:.;_sgt_rK%~a]4B0-zwA8H)eox[|&VO(m:NvsiPQm6P)03a5ZF6');
define('LOGGED_IN_KEY',    'g7=Obg{8T8&S}boiMmZG,UEzEi?7!Zf9_I]*wXQ<kK7gnO!uSmw ]#f6jFPp;)ni');
define('NONCE_KEY',        'dYAp]AcM?dIX@?^c>u*r)xGe]hjkx%C1P(.5{hV:1_gGnBPW0>=tvU^+Fu@)^YS[');
define('AUTH_SALT',        'Fr(m4Ae 3G~G0q?C{SF|m8Ji6I>>vwQfU>LM1X,S1WlP= g/He`M?qaVJ@?w>wUB');
define('SECURE_AUTH_SALT', 'ar!x[t+@pb&~xcoI[.DyhXoZ9j@3}=87g}5}WZD&,j&*cxk {@D;=ygq95v)cagX');
define('LOGGED_IN_SALT',   'rrJRD~2h[^UOt)4mVKqEZeu&lt)3q)Rap,:La`/5(-sJ><.bS?)Oy58/2Yx]!G&x');
define('NONCE_SALT',       '5,MJ|vH+2N5[U;B|O{9U`S5[sIrnOg~_)OGQ[z9zUuQ%BJ;mhoDx$&MP[%~xgn9w');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sbf_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
