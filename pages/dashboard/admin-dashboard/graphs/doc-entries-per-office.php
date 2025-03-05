<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $from = sanitize($_POST['from']);
    $to = sanitize($_POST['to']);
    $offices = json_decode($_POST['offices'], true);

    foreach ($offices as $office) {
        $count = 0;
        $getSettings = $c->prepare("
            SELECT
                GROUP_CONCAT(DISTINCT setting.doc_type)
            FROM document_transaction_setting setting
            LEFT JOIN document_types type
                ON setting.doc_type = type.id
            WHERE setting.office = ?;
        ");
        $getSettings->execute([$office]);
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
            WHERE doc.doc_type IN(?)
                AND DATE(doc.tstamp) BETWEEN ? AND ?
            GROUP BY doc.doc_number;
        ");
        $getDocuments->execute([$settings, $from, $to]);
        $docs = $getDocuments->fetchAll();

        $checkDocumentInLogs = $c->prepare("SELECT id FROM document_transaction_logs WHERE doc_number=? AND office=?;");
        $checkDocumentInOffice = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ? AND step = ? AND office = ?;");

        foreach ($docs as $doc) {
            $checkDocumentInLogs->execute([$doc['doc_number'], $office]);

            if ($checkDocumentInLogs->rowCount() > 0) {
                $count++;
            } else {
                $checkDocumentInOffice->execute([$doc['doc_type'], $doc['current_step'], $office]);

                if ($checkDocumentInOffice->rowCount() > 0) {
                    $count++;
                }
            }
        }

        array_push($response['data'], $count);
    }
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
