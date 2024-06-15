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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'portfolio' );

/** Database username */
define( 'DB_USER', 'MaiThien' );

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
define( 'AUTH_KEY',         'J;oS3rT=Ei#v~2zniJ?#cMuiIrTi11LUY#*~>8$+x_(c>oIn4MWu+;`I9?SC,;+x' );
define( 'SECURE_AUTH_KEY',  'qcJ0yYS]9]Ggg[w!Oc[S>CGT!^0XejU?oG#x`#8Tx^C4apr6OGT(E>/C ,sko)]m' );
define( 'LOGGED_IN_KEY',    'XdH>7SCfy9M=I6P`+1ymP6|4^ Ljpaz{@ekHlXXSg3sndZI]I9{Rgv}T=6bt7jug' );
define( 'NONCE_KEY',        '~Z!S6fI]E}^1/W(R`<,j6=hF w6<egIKxfJ%A&H|87)^AftJHDC$*^46m) )K<@3' );
define( 'AUTH_SALT',        'Bo6fL7ffu+_6zIIKrF?M!2;xjvaltU_U 9bIKZavU u528.Z.XV/~&:-+qf@pp^#' );
define( 'SECURE_AUTH_SALT', 'Dh!ATipuuP?ts:t|%5OgM6yN*~_wzcHuY.D$YlA2*.IiVfc-cCo+p/;vC~@z8H2S' );
define( 'LOGGED_IN_SALT',   't^c%uu2@@O!SZBWcSc-)Y;N6(6sS{P6[6&AGk{*0}1YQh^MGm,>;2`JOj#6? Q/S' );
define( 'NONCE_SALT',       '8s(WJ9tE~RBNYm]+:GB9K@tR_VBGoklmxxr[:cEzvpq?#z3,2rFG)<+v04;iR}Mi' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
