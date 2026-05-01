<?php
/**
 * Clear Beaver Builder cache and fix CSS
 * DELETE THIS FILE AFTER USE
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-load.php';

echo "<h2>Cache Clear & CSS Fix</h2>";

// 1. Clear Beaver Builder cache
$bb_cache = WP_CONTENT_DIR . '/uploads/bb-plugin/cache/';
if (is_dir($bb_cache)) {
    array_map('unlink', glob("$bb_cache*"));
    echo "<p>✅ Beaver Builder cache cleared</p>";
} else {
    echo "<p>⚠️ BB cache dir not found: $bb_cache</p>";
}

// 2. Clear BB asset cache via DB
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_fl_builder_draft'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_fl_builder_%' AND option_name LIKE '%cache%'");
echo "<p>✅ BB DB cache entries cleared</p>";

// 3. Clear any LiteSpeed cache
$ls_cache = WP_CONTENT_DIR . '/litespeed/';
if (is_dir($ls_cache)) {
    $dirs = ['cache', 'cssjs'];
    foreach ($dirs as $d) {
        $path = $ls_cache . $d;
        if (is_dir($path)) {
            exec("rm -rf " . escapeshellarg($path) . "/*");
            echo "<p>✅ LiteSpeed $d cache cleared</p>";
        }
    }
}

// 4. Clear general cache
$cache_dir = WP_CONTENT_DIR . '/cache/';
if (is_dir($cache_dir)) {
    exec("rm -rf " . escapeshellarg($cache_dir) . "/*");
    echo "<p>✅ General cache cleared</p>";
}

// 5. Flush rewrite rules
flush_rewrite_rules();
echo "<p>✅ Rewrite rules flushed</p>";

// 6. Check for remaining old URLs
$old_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_value LIKE '%mm10academy.local%'");
echo "<p>Remaining 'mm10academy.local' in wp_options: <b>$old_count</b></p>";

$old_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%mm10academy.local%'");
echo "<p>Remaining 'mm10academy.local' in wp_posts: <b>$old_posts</b></p>";

$old_meta = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE '%mm10academy.local%'");
echo "<p>Remaining 'mm10academy.local' in wp_postmeta: <b>$old_meta</b></p>";

echo "<hr><p><b>Now go to WP Admin → Settings → Permalinks and click 'Save Changes'</b></p>";
echo "<p>Then hard refresh the site (Ctrl+Shift+R)</p>";
echo "<hr><p style='color:red'><b>DELETE THIS FILE NOW!</b></p>";
