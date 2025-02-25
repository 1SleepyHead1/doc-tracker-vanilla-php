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
    $docType = sanitize($_POST['docType']);
    $docTypeId = sanitize($_POST['docTypeId']);
    $purpose = sanitize($_POST['purpose']);
    $submitter = sanitize($_POST['submitter']);
    $submitterId = sanitize($_POST['submitterId']);

    $getDocument = $c->prepare("SELECT doc_number,doc_type,tstamp FROM submitted_documents WHERE id=?");
    $getDocument->execute([$id]);
    $documet = $getDocument->fetch();

    $docNo = $documet['doc_number'];
    $curreDocTypeId = $documet['doc_type'];

    $checkSetting = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ?");
    $checkSetting->execute([$docTypeId]);

    if ($checkSetting->rowCount() == 0) {
        $response['status'] = false;
        $response['message'] = 'No existing setting for this document type.';
        echo json_encode($response);
        exit;
    }

    // if new document type is set | reset logs and insert anew
    if ($curreDocTypeId != $docTypeId) {
        $resetLogs = $c->prepare("DELETE FROM document_transaction_logs WHERE doc_number = ?");
        $resetLogs->execute([$docNo]);

        saveDocTransactionLogs($c, $docNo);
    }

    $update = $c->prepare("
        UPDATE submitted_documents SET
            doc_type =?,
            user_id =?,
            purpose =?
        WHERE id =?
    ");
    $update->execute([$docTypeId, $submitterId, $purpose, $id]);

    if ($response['status']) {
        $c->commit();

        $response['data'] = [
            'id' => $id,
            'type' => $docType,
            'submitter' => $submitter,
            'purpose' => $purpose,
        ];
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
