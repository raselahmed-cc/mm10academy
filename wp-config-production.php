<?php
/**
 * MM10 Academy - Production WordPress Configuration
 * 
 * This file reads sensitive values from environment variables or .env file.
 * Copy .env.example to .env and fill in your production values.
 */

// Load .env file if it exists (for non-containerized deployments)
if ( file_exists( __DIR__ . '/.env' ) ) {
    $env_lines = file( __DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    foreach ( $env_lines as $line ) {
        if ( strpos( trim( $line ), '#' ) === 0 ) continue;
        if ( strpos( $line, '=' ) === false ) continue;
        list( $key, $value ) = array_map( 'trim', explode( '=', $line, 2 ) );
        $value = trim( $value, '"\'');
        if ( ! getenv( $key ) ) {
            putenv( "$key=$value" );
            $_ENV[$key] = $value;
        }
    }
}

// Helper function
function env( $key, $default = '' ) {
    $value = getenv( $key );
    return $value !== false ? $value : $default;
}

// ========================
// Environment Detection
// ========================
define( 'WP_ENVIRONMENT_TYPE', env( 'WP_ENVIRONMENT_TYPE', 'production' ) );

// ========================
// Database Configuration
// ========================
define( 'DB_NAME',     env( 'DB_NAME', 'mm10academy' ) );
define( 'DB_USER',     env( 'DB_USER', '' ) );
define( 'DB_PASSWORD', env( 'DB_PASSWORD', '' ) );
define( 'DB_HOST',     env( 'DB_HOST', 'localhost' ) );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

$table_prefix = env( 'DB_PREFIX', 'wp_' );

// ========================
// Authentication Keys & Salts
// ========================
define( 'AUTH_KEY',         env( 'AUTH_KEY' ) );
define( 'SECURE_AUTH_KEY',  env( 'SECURE_AUTH_KEY' ) );
define( 'LOGGED_IN_KEY',    env( 'LOGGED_IN_KEY' ) );
define( 'NONCE_KEY',        env( 'NONCE_KEY' ) );
define( 'AUTH_SALT',        env( 'AUTH_SALT' ) );
define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
define( 'LOGGED_IN_SALT',   env( 'LOGGED_IN_SALT' ) );
define( 'NONCE_SALT',       env( 'NONCE_SALT' ) );

// ========================
// URLs
// ========================
define( 'WP_SITEURL', env( 'WP_SITEURL', 'https://mm10academy.com' ) );
define( 'WP_HOME',    env( 'WP_HOME', 'https://mm10academy.com' ) );

// ========================
// Security Settings
// ========================
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', env( 'DISALLOW_FILE_MODS', 'false' ) === 'true' );
define( 'FORCE_SSL_ADMIN', true );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'ALLOW_UNFILTERED_UPLOADS', false );
define( 'WP_ALLOW_REPAIR', false );
define( 'RELOCATE', false );

// ========================
// Performance Settings
// ========================
define( 'WP_CACHE', true );
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '1024M' );
define( 'CONCATENATE_SCRIPTS', false );
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

// Redis Object Cache (uncomment when Redis is available)
// define( 'WP_REDIS_HOST', env( 'REDIS_HOST', '127.0.0.1' ) );
// define( 'WP_REDIS_PORT', (int) env( 'REDIS_PORT', '6379' ) );
// define( 'WP_REDIS_DISABLED', false );

// ========================
// Cron
// ========================
define( 'DISABLE_WP_CRON', env( 'DISABLE_WP_CRON', 'false' ) === 'true' );

// ========================
// Debug (controlled by environment)
// ========================
$is_production = ( WP_ENVIRONMENT_TYPE === 'production' );
define( 'WP_DEBUG',         ! $is_production );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG',     ! $is_production );
define( 'SCRIPT_DEBUG',     ! $is_production );
define( 'DIEONDBERROR',     false );

// ========================
// File System
// ========================
define( 'FS_METHOD', 'direct' );
define( 'FS_CHMOD_DIR',  ( 0755 & ~ umask() ) );
define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );

// ========================
// PHP ini overrides
// ========================
@ini_set( 'max_execution_time', '300' );
@ini_set( 'max_input_time', '300' );
@ini_set( 'max_input_vars', '10000' );
@ini_set( 'upload_max_filesize', '256M' );
@ini_set( 'post_max_size', '256M' );

/* That's all, stop editing! Happy publishing. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
