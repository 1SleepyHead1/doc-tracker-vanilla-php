<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {

    $response = ['status' => true, 'message' => ''];

    $getUsersWithoutAccounts = $c->prepare("
        SELECT
            id,
            is_office_personnel,
            CONCAT(LOWER(REPLACE(SUBSTRING_INDEX(first_name, ' ', 2), ' ', '_')),generate_number(5)) AS first_name,
            CONCAT(LOWER(REPLACE(SUBSTRING_INDEX(last_name, ' ', 2), ' ', '_')),generate_number(5)) AS last_name,
            name
        FROM users
        WHERE id NOT IN (SELECT user_id FROM user_accounts)
        ORDER BY name;
    ");
    $getUsersWithoutAccounts->execute();
    $usersWithoutAccounts = $getUsersWithoutAccounts->fetchAll();

    if ($response['status']) {
        $response['data'] = $usersWithoutAccounts;
    }
} catch (PDOException $e) {

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
