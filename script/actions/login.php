<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../conn.php";
require_once "../globals.php";

try {
    session_start();
    $response = ['status' => true, 'message' => ''];

    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);

    $getUser = $c->prepare("
        SELECT 
            acc.id AS user_account_id, 
            user.id as user_id, 
            acc.is_admin,
            user.user_type,
            user.is_office_personnel 
        FROM user_accounts acc
        JOIN users user ON acc.user_id = user.id
        WHERE `username` = ? AND `password` = ?;");
    $getUser->execute([$username, md5($password)]);

    if ($getUser->rowCount() == 0) {
        $response['status'] = false;
        $response['message'] = "Invalid user credentials. ";

        echo json_encode($response);
        exit;
    }

    $user = $getUser->fetch();

    if ($response['status']) {
        $token = generateDocNo();

        $_SESSION['log_token'] = $token;
        $_SESSION['user_account_id'] = $user['user_account_id'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['is_office_personnel'] = $user['is_office_personnel'];

        saveLoginLogs($c, $token, $user['user_account_id']);
    }
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
