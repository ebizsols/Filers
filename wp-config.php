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
define( 'DB_NAME', 'filers_wp_2jttj' );

/** MySQL database username */
define( 'DB_USER', 'filers_wp_ecr1r' );

/** MySQL database password */
define( 'DB_PASSWORD', 's_1fD!wwmILrD1T5' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('DISABLE_WP_CRON', true);

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'G0K|U6(cH711]ss3z[~V24O%!1HuK%|]2:E)q5N8hUSW026AS2E8q%2a](24/hrG');
define('SECURE_AUTH_KEY', 'hKw)G|J6rgtz8]11DK_7Hgf8Bup6Vt~9R|yomq6*;1my5S0SV1X|peObM@(ikJ+2');
define('LOGGED_IN_KEY', '1qEPpB[JJ4#(3Bhu6RuTE4L3viodG9V%X79(;3jO&(l~2;22Q])v;tb[;a)A!uwM');
define('NONCE_KEY', 'K;Jx1S*#7g&Z_+3xrkR1|vaFo#8S:Ob47k/iF[8f1k_oWlB(zA_A|z#WE@xHB88l');
define('AUTH_SALT', 'X_a8R[I&94#|9Mzm]wn6_Lq8_lfi2Mc7dDyMbQ|ybq]u(@N2j6]C2~2[A(LZ4;Ss');
define('SECURE_AUTH_SALT', '6553UY3iF[7k7l/-S]g5_;prg/KTs2g0X~;Enx2g9yxE8*SAMa)/T[97;VC429FX');
define('LOGGED_IN_SALT', '&-4#UhBI]9ECg1#|TW0H1_32fs5RfM/Y3xinUo)VM0YX71o0N3on++l@_Z2-s0/v');
define('NONCE_SALT', 't-Y[f&S5D%d3F454i&Dk!QOc-D2%0fE_6JlNcQauOup3o10&Ab12l:sh0Dr:0B9f');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'jjQFDEb5w_';


define('WP_ALLOW_MULTISITE', true);
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';