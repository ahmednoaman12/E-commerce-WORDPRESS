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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'U$=!#gOqTq9dw]O- M/2}vRgmLt/a)aN%Nwn{/C)4e1~fcsakyuhh(Mpifu4a md' );
define( 'SECURE_AUTH_KEY',  '=a&I>HvE<Gxqid2s(Jj3Sqo73F2Us1pThX6[u#+Z&io{i^BS(cfg<fedq{<8RwSi' );
define( 'LOGGED_IN_KEY',    'jYq(L::eYLk@9z)%JIX2 :o]%FQ3Vq&L$)Y6y1@< -}.K.aw#gqT^T$Jlt`&gyvV' );
define( 'NONCE_KEY',        'j1g*k9~;Y>L/]/5)[.M`Vyts)&bq.bd,@hj&dz1htd@t^Y/KH{0s)k$=1}Q4jo]K' );
define( 'AUTH_SALT',        'OmBFyI4!]0tC@R:u6n1B20SPK|)Jpx;6BBaD7TlJN<*9r;0ih~)%{NMw%8SuMK0T' );
define( 'SECURE_AUTH_SALT', '=ER{t9K^}}~c;nntrYZf=yS,~Dh>:uph%q3QH:R=rxZp?n?pHr:{o?~[VYc3yQeU' );
define( 'LOGGED_IN_SALT',   'f{F;t%gZ=|i(p2n`9?7cI#0oe*#FQ[*gI~^Jx|+`7[p!/-1Kj%h>=1G<rM)6o]/,' );
define( 'NONCE_SALT',       'S:X{h}VqO~S$p;)Ci_&Nd0^08faT-F-2T?W!8ZM90}.{+p+qB>F6L< p|V>p4G,:' );

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
