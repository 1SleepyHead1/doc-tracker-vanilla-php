<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $id = sanitize($_POST['id']);
    $currentPassword = sanitize($_POST['cword']);
    $password = sanitize($_POST['pword']);

    $checkCurrent = $c->prepare("SELECT id FROM user_accounts WHERE password=? AND id=?");
    $checkCurrent->execute([md5($currentPassword), $id]);

    if ($checkCurrent->rowCount() == 0) {
        $response['status'] = false;
        $response['message'] = "Current password does not match.";

        echo json_encode($response);
        exit;
    }

    $update = $c->prepare("UPDATE user_accounts SET password=? WHERE id=?");
    $update->execute([md5($password), $id]);

    if ($response['status']) {
        $c->commit();
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
