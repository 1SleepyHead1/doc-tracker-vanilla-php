<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $docTypeCode = sanitize($_POST['docTypeCode']);
    $docTypeName = sanitize($_POST['docTypeName']);

    $checkDocTypeCode = $c->prepare("SELECT id FROM document_types WHERE LOWER(doc_type_code) = LOWER(?)");
    $checkDocTypeCode->execute([$docTypeCode]);
    $docTypeCodeExists = $checkDocTypeCode->fetchColumn();

    $checkDocTypeName = $c->prepare("SELECT id FROM document_types WHERE LOWER(doc_type_name) = LOWER(?)");
    $checkDocTypeName->execute([$docTypeName]);
    $docTypeNameExists = $checkDocTypeName->fetchColumn();

    if ($docTypeCodeExists || $docTypeNameExists) {
        $response['status'] = false;
        $response['message'] = 'Document type already exists.';

        echo json_encode($response);
        exit;
    }

    $insert = $c->prepare("
        INSERT INTO document_types(
            doc_type_code,
            doc_type_name
        ) VALUES(?,?);
    ");

    $docTypeCode = strtoupper($docTypeCode);
    $docTypeName = ucfirst($docTypeName);

    $insert->execute([$docTypeCode, $docTypeName]);
    $id = $c->lastInsertId();

    $getTstamp = $c->prepare("SELECT tstamp FROM document_types WHERE id = ?");
    $getTstamp->execute([$id]);
    $tstamp = $getTstamp->fetchColumn();

    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'doc_type_code' => $docTypeCode,
            'doc_type_name' => $docTypeName,
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
