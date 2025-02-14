<?php

// Include the mysqldump-php library (make sure you have downloaded or installed it)
require_once '../../../vendor/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

try {

    $settings = parse_ini_file('../../../config/connection.cnf', true);
    define('DB_HOST', $settings['servername']);
    define('DB_NAME', $settings['databasename']);
    define('DB_USER', $settings['u']);
    define('DB_PASSWORD', $settings['p']);

    // Set up database connection parameters
    $host = DB_HOST;
    $dbname = DB_NAME;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $data = [];

    // Initialize Mysqldump
    $dump = new IMysqldump\Mysqldump(
        "mysql:host=$host;dbname=$dbname",  // DSN
        $username,                         // Username
        $password                          // Password
    );


    // Start the backup process and output the SQL directly to the browser
    $backupFile = '../../../sql/backup/' . $dbname . '_backup.sql';

    if (file_exists($backupFile)) {
        unlink($backupFile);
    }

    $dump->start($backupFile);

    $data = [
        'status' => 200,
        'file_name' => basename($backupFile)
    ];

    echo json_encode($data);
    exit;
} catch (\Exception $e) {
    // Catch and display any errors that occur during the backup process
    $data = [
        'status' => 500,
        'message' => 'mysqldump-php error: ' . $e->getMessage()
    ];

    echo json_encode($data);
}
