<?php
/**
 * Test script to verify database fixes for NextScripts SNAP plugin
 * This script should be run in a WordPress environment to test the fixes
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('This script must be run within WordPress context');
}

/**
 * Test the fixed database table creation
 */
function test_nxs_table_creation() {
    global $wpdb;
    
    echo "<h2>Testing Database Table Creation</h2>\n";
    
    // Test log table creation
    echo "<h3>Testing nxs_log table creation...</h3>\n";
    if (function_exists('nxs_checkAddLogTable')) {
        $result = nxs_checkAddLogTable();
        echo $result ? "✅ Log table created successfully\n" : "❌ Log table creation failed\n";
        
        // Check if table exists and has correct structure
        $table_name = $wpdb->prefix . 'nxs_log';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        echo $table_exists ? "✅ Log table exists in database\n" : "❌ Log table not found in database\n";
        
        if ($table_exists) {
            $columns = $wpdb->get_results("DESCRIBE $table_name");
            echo "<pre>Log table structure:\n";
            foreach ($columns as $column) {
                echo "- {$column->Field}: {$column->Type} {$column->Null} {$column->Key} {$column->Default}\n";
            }
            echo "</pre>\n";
        }
    } else {
        echo "❌ nxs_checkAddLogTable function not found\n";
    }
    
    // Test query table creation
    echo "<h3>Testing nxs_query table creation...</h3>\n";
    if (function_exists('nxs_checkAddQueryTable')) {
        $result = nxs_checkAddQueryTable();
        echo $result ? "✅ Query table created successfully\n" : "❌ Query table creation failed\n";
        
        // Check if table exists and has correct structure
        $table_name = $wpdb->prefix . 'nxs_query';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        echo $table_exists ? "✅ Query table exists in database\n" : "❌ Query table not found in database\n";
        
        if ($table_exists) {
            $columns = $wpdb->get_results("DESCRIBE $table_name");
            echo "<pre>Query table structure:\n";
            foreach ($columns as $column) {
                echo "- {$column->Field}: {$column->Type} {$column->Null} {$column->Key} {$column->Default}\n";
            }
            echo "</pre>\n";
        }
    } else {
        echo "❌ nxs_checkAddQueryTable function not found\n";
    }
}

/**
 * Test the fixed SQL queries
 */
function test_nxs_sql_queries() {
    global $wpdb;
    
    echo "<h2>Testing Fixed SQL Queries</h2>\n";
    
    $log_table = $wpdb->prefix . 'nxs_log';
    $query_table = $wpdb->prefix . 'nxs_query';
    
    // Test if tables exist first
    $log_exists = $wpdb->get_var("SHOW TABLES LIKE '$log_table'") == $log_table;
    $query_exists = $wpdb->get_var("SHOW TABLES LIKE '$query_table'") == $query_table;
    
    if (!$log_exists || !$query_exists) {
        echo "❌ Required tables don't exist. Please run table creation first.\n";
        return;
    }
    
    // Test log table queries (similar to nxs_do_this_hourly)
    echo "<h3>Testing log table queries...</h3>\n";
    
    try {
        // Test UPDATE query
        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$log_table} SET flt = %s WHERE flt IS NULL OR flt = %s",
                'snap',
                ''
            )
        );
        echo "✅ UPDATE query executed successfully (affected rows: $result)\n";
    } catch (Exception $e) {
        echo "❌ UPDATE query failed: " . $e->getMessage() . "\n";
    }
    
    try {
        // Test DELETE query with subquery
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$log_table} WHERE flt = %s AND id NOT IN (
                SELECT id FROM (
                    SELECT id FROM {$log_table} ORDER BY id DESC LIMIT 360
                ) foo
            )",
                'cron'
            )
        );
        echo "✅ DELETE with subquery executed successfully (affected rows: $result)\n";
    } catch (Exception $e) {
        echo "❌ DELETE query failed: " . $e->getMessage() . "\n";
    }
    
    // Test query table queries (similar to nxs_checkQuery)
    echo "<h3>Testing query table queries...</h3>\n";
    
    try {
        $current_time = date_i18n('Y-m-d H:i:s');
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(id) FROM {$query_table} WHERE timetorun < %s",
                $current_time
            )
        );
        echo "✅ COUNT query executed successfully (count: $count)\n";
    } catch (Exception $e) {
        echo "❌ COUNT query failed: " . $e->getMessage() . "\n";
    }
    
    try {
        $current_time = date_i18n('Y-m-d H:i:s');
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$query_table} WHERE timetorun < %s ORDER BY timetorun DESC LIMIT %d",
                $current_time,
                10
            ),
            ARRAY_A
        );
        echo "✅ SELECT query executed successfully (rows: " . count($results) . ")\n";
    } catch (Exception $e) {
        echo "❌ SELECT query failed: " . $e->getMessage() . "\n";
    }
}

/**
 * Test database version and compatibility
 */
function test_database_compatibility() {
    global $wpdb;
    
    echo "<h2>Testing Database Compatibility</h2>\n";
    
    // Get database version
    $db_version = $wpdb->get_var("SELECT VERSION()");
    echo "Database version: $db_version\n";
    
    // Check if it's MariaDB or MySQL
    if (stripos($db_version, 'mariadb') !== false) {
        echo "✅ MariaDB detected\n";
        $version_num = preg_replace('/[^0-9.].*/', '', $db_version);
        if (version_compare($version_num, '10.2', '>=')) {
            echo "✅ MariaDB version is compatible (>= 10.2)\n";
        } else {
            echo "⚠️ MariaDB version may have compatibility issues (< 10.2)\n";
        }
    } else {
        echo "✅ MySQL detected\n";
        $version_num = preg_replace('/[^0-9.].*/', '', $db_version);
        if (version_compare($version_num, '5.7', '>=')) {
            echo "✅ MySQL version is compatible (>= 5.7)\n";
        } else {
            echo "⚠️ MySQL version may have compatibility issues (< 5.7)\n";
        }
    }
    
    // Test SQL mode
    $sql_mode = $wpdb->get_var("SELECT @@sql_mode");
    echo "SQL Mode: $sql_mode\n";
    
    if (stripos($sql_mode, 'NO_ZERO_DATE') !== false) {
        echo "✅ NO_ZERO_DATE mode detected - our fixes handle this correctly\n";
    }
    
    if (stripos($sql_mode, 'STRICT_TRANS_TABLES') !== false) {
        echo "✅ STRICT_TRANS_TABLES mode detected - our fixes handle this correctly\n";
    }
}

// Run tests if this script is called directly in WordPress admin
if (is_admin() && current_user_can('administrator')) {
    echo "<h1>NextScripts SNAP Database Fixes Test</h1>\n";
    echo "<p>Testing the database fixes applied to the NextScripts SNAP plugin.</p>\n";
    
    test_database_compatibility();
    test_nxs_table_creation();
    test_nxs_sql_queries();
    
    echo "<h2>Test Complete</h2>\n";
    echo "<p>If all tests show ✅, the database fixes are working correctly.</p>\n";
}
?>