<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../conn.php";
require_once "../globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];

    $officeId = $_POST['officeId'];

    $getSettings = $c->prepare("
        SELECT
            GROUP_CONCAT(DISTINCT setting.doc_type)
        FROM document_transaction_setting setting
        LEFT JOIN document_types type
            ON setting.doc_type = type.id
        WHERE setting.office = ?;
    ");
    $getSettings->execute([$officeId]);
    $settings = $getSettings->fetchColumn();

    $getDocuments = $c->prepare("
        SELECT
            doc.id,
            doc.doc_number,
            doc.doc_type,
            MAX(log.step) + 1 AS current_step,
            user.name as submitter,
            doc_type.doc_type_name,
            doc.purpose,
            doc.status,
            doc.tstamp
        FROM submitted_documents doc
        LEFT JOIN document_transaction_logs log ON doc.doc_number = log.doc_number
        LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id 
        LEFT JOIN users user ON doc.user_id = user.id
        WHERE doc.status NOT IN('Rejected','For Release') 
            AND doc.doc_type IN(?)
        GROUP BY doc.doc_number;
    ");
    $getDocuments->execute([$settings]);
    $docs = $getDocuments->fetchAll();

    $checkDocument = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ? AND step = ? AND office = ?;");

    foreach ($docs as $doc) {
        $checkDocument->execute([$doc['doc_type'], $doc['current_step'], $officeId]);
        if ($checkDocument->rowCount() > 0) {
            $data = [
                'id' => $doc['id'],
                'doc_no' => $doc['doc_number'],
                'status' => $doc['status'],
                'tstamp' => $doc['tstamp']
            ];

            array_push($response['data'], $data);
        }
    }
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
