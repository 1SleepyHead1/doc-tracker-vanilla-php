<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../conn.php";
require_once "../globals.php";

try {
    $response = ['status' => true, 'message' => ''];
    $id = sanitize($_POST['id']);
    $token = sanitize($_POST['token']);

    $query = $c->prepare("SELECT id FROM login_logs WHERE token=? AND user_account_id=?;");
    $query->execute([$token, $id]);

    if ($query->rowCount() == 0) {
        $response['status'] = false;
    }
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
