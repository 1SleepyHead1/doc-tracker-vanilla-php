<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $docType = sanitize($_POST['docType']);
    $settings = json_decode($_POST['settings'], true);

    $checkSetting = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ?");
    $checkSetting->execute([$docType]);

    if ($checkSetting->rowCount() > 0) {
        $delete = $c->prepare("DELETE FROM document_transaction_setting WHERE doc_type = ?");
        $delete->execute([$docType]);
    }

    $insert = $c->prepare("
        INSERT INTO document_transaction_setting(
            step,
            doc_type,
            office
        ) VALUES(?,?,?);
    ");

    foreach ($settings as $setting) {
        $insert->execute([$setting['order'], $docType, $setting['office']]);
    }

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
