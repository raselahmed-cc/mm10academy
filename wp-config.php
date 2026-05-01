<?php
define( 'WP_CACHE', true );

// LiteSpeed Cache — enable on live server only
// 


// Core WordPress security & performance settings
define( 'RELOCATE', false );
define( 'WP_SITEURL', 'https://mm10academy.com' );
define( 'WP_HOME', 'https://mm10academy.com' );
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );
define( 'CONCATENATE_SCRIPTS', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_ALLOW_REPAIR', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DIEONDBERROR', false );
define( 'ALLOW_UNFILTERED_UPLOADS', false );
// WP Rocket removed - using LiteSpeed Cache
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mm10_mm10wpdb');
/** MySQL database username */
define('DB_USER', 'mm10_mm10wpdb');
/** MySQL database password */
define('DB_PASSWORD', 'A2an8Aqluj6#5Jxl');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
/**#@+
 * Authentication Unique Keys and Salts.
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY',         'x8#Fz!qR$7vLm@Wp3Yk&Tn9Bc*Hj2Sd6Ue0Ag5Ci1Do4Eq');
define('SECURE_AUTH_KEY',  'Kp7$Lm2#Qr9!Wx5@Yz3&Nv8*Bt1Hj6Cf0Dg4Ei1Fk5Go');
define('LOGGED_IN_KEY',    'Mn3@Pq8#Sv2!Ux6$Yz1&Bw5*Ct9Dj4Fh0Gk7El3Ai6Ho');
define('NONCE_KEY',        'Rw5!Tx9@Vy3#Az7$Cb1&De4*Fg8Hk2Ji6Lm0No5Pq3Su');
define('AUTH_SALT',        'Gt4@Hu8#Iv2!Jw6$Kx1&Ly5*Mz9Na3Ob7Pc0Qd4Re8Sf');
define('SECURE_AUTH_SALT', 'Wk6!Xl0@Ym4#Zn8$Ao2&Bp5*Cq9Dr3Es7Ft1Gu5Hv9Iw');
define('LOGGED_IN_SALT',   'Jx3@Ky7#Lz1!Ma5$Nb9&Oc2*Pd6Qe0Rf4Sg8Th2Ui6Vj');
define('NONCE_SALT',       'Wl0!Xm4@Yn8#Zo2$Ap6&Bq9*Cr3Ds7Et1Fu5Gv9Hw3Ix');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';
/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('FS_METHOD','direct');
define('WPLANG', '');
define('FS_CHMOD_DIR', (0775 & ~ umask()));
define('FS_CHMOD_FILE', (0664 & ~ umask()));
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('FORCE_SSL_ADMIN', true);

/** ====== Enterprise Performance Settings (WooCommerce + Heavy Design) ====== */
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '1024M');  // Admin-side limit

@ini_set('max_execution_time', '300');
@ini_set('max_input_time', '300');
@ini_set('max_input_vars', '10000');
@ini_set('upload_max_filesize', '256M');
@ini_set('post_max_size', '256M');
@ini_set('memory_limit', '512M');
/** ====== End Enterprise Performance Settings ====== */

/* Redis Object Cache — enable on live server with LiteSpeed Cache */
// define( 'WP_REDIS_HOST', '127.0.0.1' );
// define( 'WP_REDIS_PORT', 6379 );
// define( 'WP_REDIS_DISABLED', false );
define( 'DISABLE_WP_CRON', false );
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');