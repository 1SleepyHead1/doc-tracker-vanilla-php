<?php
try {
    $settings = parse_ini_file('config/connection.cnf', true);
    define('DB_HOST', $settings['servername']);
    define('DB_NAME', $settings['databasename']);
    define('DB_USER', $settings['u']);
    define('DB_PASSWORD', $settings['p']);

    $c = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASSWORD);
    $c->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c->setAttribute(PDO::FETCH_ASSOC, true);
    $c->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
