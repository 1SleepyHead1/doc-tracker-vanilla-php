<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {

    $response = ['status' => true, 'message' => ''];

    $getOfficePersonnels = $c->prepare(" 
        SELECT 
            id,
            name
        FROM users
        WHERE is_office_personnel = 1
        AND id NOT IN (SELECT person_in_charge FROM offices WHERE person_in_charge IS NOT NULL)
        ORDER BY name;
    ");
    $getOfficePersonnels->execute();
    $officePersonnels = $getOfficePersonnels->fetchAll();

    if ($response['status']) {
        $response['data'] = $officePersonnels;
    }
} catch (PDOException $e) {

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
