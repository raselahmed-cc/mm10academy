<?php
// Temporary DB connection test - DELETE after use
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_name = 'mm10_mm10wpdb';
$db_user = 'mm10_mm10wpdb';
$db_pass = 'DN0kb-Ksmfu#HaI#';
$db_host = 'localhost:3306';

echo "<h3>DB Connection Test</h3>";
echo "Host: $db_host<br>";
echo "User: $db_user<br>";
echo "DB: $db_name<br><br>";

// Test 1: mysqli connect
echo "<b>Test 1: mysqli to localhost</b><br>";
$conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if ($conn) {
    echo "SUCCESS - Connected to $db_name<br><br>";
    mysqli_close($conn);
} else {
    echo "FAILED: " . mysqli_connect_error() . "<br><br>";
}

// Test 2: try 127.0.0.1
echo "<b>Test 2: mysqli to 127.0.0.1</b><br>";
$conn2 = @mysqli_connect('127.0.0.1', $db_user, $db_pass, $db_name);
if ($conn2) {
    echo "SUCCESS - 127.0.0.1 works! Update DB_HOST to 127.0.0.1<br><br>";
    mysqli_close($conn2);
} else {
    echo "FAILED: " . mysqli_connect_error() . "<br><br>";
}

// Test 3: check if MySQL socket exists
echo "<b>Test 3: MySQL socket check</b><br>";
$sockets = ['/var/lib/mysql/mysql.sock', '/var/run/mysqld/mysqld.sock', '/tmp/mysql.sock'];
foreach ($sockets as $sock) {
    echo "$sock: " . (file_exists($sock) ? "EXISTS" : "not found") . "<br>";
}

// Test 4: try without DB name (just auth)
echo "<br><b>Test 4: Auth only (no DB name)</b><br>";
$conn3 = @mysqli_connect($db_host, $db_user, $db_pass);
if ($conn3) {
    echo "SUCCESS - Auth works. Checking if DB exists...<br>";
    $result = mysqli_query($conn3, "SHOW DATABASES LIKE '$db_name'");
    if (mysqli_num_rows($result) > 0) {
        echo "Database '$db_name' EXISTS<br>";
    } else {
        echo "Database '$db_name' DOES NOT EXIST<br>";
    }
    mysqli_close($conn3);
} else {
    echo "FAILED: " . mysqli_connect_error() . "<br>";
}

echo "<br><hr><small>DELETE this file after debugging!</small>";
