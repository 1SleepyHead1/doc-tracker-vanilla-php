<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $userId = $_POST['u'];
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
