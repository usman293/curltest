<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'curltest');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '111111');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'Fu+3- MGMRf^y Ve/}*9%t/t R68Y*B?<X(DjP?k[4c6C5:L?,litQ0%-(h:i~ c');
define('SECURE_AUTH_KEY',  '4rG@xqnG{a+o:W_0YxvJ_+N5Jl0LatdS>b#zUr?x<T[iF!+{-rm|w*sh`*~(USp)');
define('LOGGED_IN_KEY',    'PuE;>NbrSRQT],Z@J>`Un)u1jt]9Wr( ZKNhU;CI,|TVEhFB889oHbEoDGz=^>#h');
define('NONCE_KEY',        'Ly=elC_>t|Xz>zHbrc)PZ?P-Noq;R VhO}yx[%g|8/B:+VSBg^+F|*9~cqx2GW{|');
define('AUTH_SALT',        '6R[I?=59G+~N&LWvH@zgxkyUXHu,`-%#p`jbJdRUf{|eP-} BS|wQ+3$^:hwrD)B');
define('SECURE_AUTH_SALT', '|#bxDOcRDp,KK3k{`%/D{%6{7_hYF9BnSZ{Tcg+q^oC;q_Hj;hT3o{UBi$qD4|;x');
define('LOGGED_IN_SALT',   'SSqMAPg7>]9[i@@nHEM}~Xa2|FWWgG0mv!QZr]t~+9:@{i]LNE*b~ryi+h&pXc{r');
define('NONCE_SALT',       '5N}P2Xgve}$s#p5PmhfBBX[=d.kl*$]3>{/%37HM;U(.<ALq`uyK<A5JY5|VzYk7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define( 'FS_METHOD', 'direct' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
