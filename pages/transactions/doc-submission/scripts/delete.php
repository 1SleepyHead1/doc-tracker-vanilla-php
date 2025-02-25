<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $uploadDir = "../../../../assets/uploads/qr-codes/";
    $id = sanitize($_POST['id']);

    $getDocNo = $c->prepare("SELECT doc_number FROM submitted_documents WHERE id=?");
    $getDocNo->execute([$id]);
    $docNo = $getDocNo->fetchColumn();

    $file = $uploadDir . "$docNo.png";
    if (file_exists($file)) {
        unlink($file);
    }

    $deleteLogs = $c->prepare("DELETE FROM document_transaction_logs WHERE doc_number=?");
    $deleteLogs->execute([$docNo]);

    $delete = $c->prepare("DELETE FROM submitted_documents WHERE id=?");
    $delete->execute([$id]);

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
