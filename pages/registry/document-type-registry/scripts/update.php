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
    $docTypeCode = sanitize($_POST['docTypeCode']);
    $docTypeName = sanitize($_POST['docTypeName']);

    $checkDocType = $c->prepare("SELECT id FROM document_types WHERE LOWER(doc_type_code) = LOWER(?) AND LOWER(doc_type_name) = LOWER(?) AND id<>?");
    $checkDocType->execute([$docTypeCode, $docTypeName, $id]);
    $docTypeExists = $checkDocType->fetchColumn(0);

    if ($docTypeExists) {
        $response['status'] = false;
        $response['message'] = 'Document type already exists.';

        echo json_encode($response);
        exit;
    }

    $update = $c->prepare("
        UPDATE document_types SET
            doc_type_code = ?,
            doc_type_name = ?
        WHERE id = ?;
    ");

    $docTypeCode = strtoupper($docTypeCode);
    $docTypeName = ucfirst($docTypeName);

    $update->execute([$docTypeCode, $docTypeName, $id]);

    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'doc_type_code' => $docTypeCode,
            'doc_type_name' => $docTypeName,
        ];
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
