<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $id = sanitize($_POST['id']);

    $checkIfUsed = $c->prepare("SELECT id FROM offices WHERE person_in_charge=?;");
    $checkIfUsed->execute([$id]);
    $checkIfUsed = $checkIfUsed->fetchColumn();

    if ($checkIfUsed) {
        $response['status'] = false;
        $response['message'] = 'User is currently assigned to an office.';

        echo json_encode($response);
        exit;
    }

    $deleteUserAccount = $c->prepare("DELETE FROM user_accounts WHERE user_id=?;");
    $deleteUserAccount->execute([$id]);

    $deleteUser = $c->prepare("DELETE FROM users WHERE id=?;");
    $deleteUser->execute([$id]);

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
