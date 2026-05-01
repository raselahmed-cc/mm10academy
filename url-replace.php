<?php
/**
 * WordPress Search & Replace - handles serialized data properly
 * DELETE THIS FILE AFTER USE
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load WordPress
define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-load.php';

$old_url = 'http://mm10academy.local';
$new_url = 'https://mm10academy.com';

echo "<h2>MM10 Academy URL Search & Replace</h2>";
echo "<p>Replacing: <code>$old_url</code> → <code>$new_url</code></p>";

global $wpdb;

// 1. Check current siteurl and home
$siteurl = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");
$home = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home'");
echo "<p><b>Current siteurl:</b> $siteurl</p>";
echo "<p><b>Current home:</b> $home</p>";

// 2. Get all tables
$tables = $wpdb->get_col("SHOW TABLES");
$total_changes = 0;

foreach ($tables as $table) {
    $columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
    
    foreach ($columns as $column) {
        $col_name = $column->Field;
        
        // Check if column contains old URL
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `$table` WHERE `$col_name` LIKE %s",
            '%' . $wpdb->esc_like($old_url) . '%'
        ));
        
        if ($count > 0) {
            // Get primary key
            $primary_key = null;
            foreach ($columns as $c) {
                if ($c->Key === 'PRI') {
                    $primary_key = $c->Field;
                    break;
                }
            }
            
            if (!$primary_key) continue;
            
            // Get rows with old URL
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `$primary_key`, `$col_name` FROM `$table` WHERE `$col_name` LIKE %s",
                    '%' . $wpdb->esc_like($old_url) . '%'
                )
            );
            
            $changed = 0;
            foreach ($rows as $row) {
                $old_value = $row->$col_name;
                $new_value = recursive_unserialize_replace($old_url, $new_url, $old_value);
                
                if ($old_value !== $new_value) {
                    $wpdb->update($table, [$col_name => $new_value], [$primary_key => $row->$primary_key]);
                    $changed++;
                }
            }
            
            if ($changed > 0) {
                echo "<p>✅ <b>$table.$col_name</b> — $changed rows updated</p>";
                $total_changes += $changed;
            }
        }
    }
}

echo "<hr><p><b>Total rows updated: $total_changes</b></p>";

// Verify
$siteurl = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");
$home = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home'");
echo "<p><b>New siteurl:</b> $siteurl</p>";
echo "<p><b>New home:</b> $home</p>";
echo "<hr><p style='color:red'><b>DELETE THIS FILE NOW!</b></p>";

/**
 * Recursively search & replace in serialized data
 */
function recursive_unserialize_replace($from, $to, $data, $serialised = false) {
    if (is_string($data)) {
        $unserialized = @unserialize($data);
        
        if ($unserialized !== false || $data === 'b:0;') {
            $data = serialize(recursive_unserialize_replace($from, $to, $unserialized, true));
        } else {
            $data = str_replace($from, $to, $data);
        }
        
        if ($serialised) {
            // Fix broken serialized string lengths
            $data = preg_replace_callback('!s:(\d+):"(.*?)";!s', function($m) {
                return 's:' . strlen($m[2]) . ':"' . $m[2] . '";';
            }, $data);
        }
    } elseif (is_array($data)) {
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[recursive_unserialize_replace($from, $to, $key, false)] = 
                recursive_unserialize_replace($from, $to, $value, false);
        }
        $data = $new_data;
    } elseif (is_object($data)) {
        $props = get_object_vars($data);
        foreach ($props as $key => $value) {
            $data->$key = recursive_unserialize_replace($from, $to, $value, false);
        }
    }
    
    return $data;
}
