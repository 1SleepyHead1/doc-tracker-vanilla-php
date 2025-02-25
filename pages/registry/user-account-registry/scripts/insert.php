<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $user = sanitize($_POST['user']);
    $name = sanitize($_POST['name']);
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);
    $category = sanitize($_POST['category']);
    $hashedPassword = md5($password);

    $checkUser = $c->prepare("SELECT id FROM user_accounts WHERE username = ?");
    $checkUser->execute([$username]);
    $userExists = $checkUser->fetchColumn();

    if ($userExists) {
        $response['status'] = false;
        $response['message'] = 'Username already exists. Please use a different username.';

        echo json_encode($response);
        exit;
    }

    $insert = $c->prepare("
        INSERT INTO user_accounts(
            user_id,
            username,
            password
        ) VALUES(?,?,?);
    ");

    $insert->execute([$user, $username, $hashedPassword]);
    $id = $c->lastInsertId();

    $getTstamp = $c->prepare("SELECT tstamp FROM user_accounts WHERE id = ?");
    $getTstamp->execute([$id]);
    $tstamp = $getTstamp->fetchColumn();

    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'name' => $name,
            'category' => $category == 1 ? "Office Personnel" : "Document Submitters",
            'username' => $username,
            'tstamp' => $tstamp
        ];
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
