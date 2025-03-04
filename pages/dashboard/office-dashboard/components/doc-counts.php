<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $officeId = $_POST['officeId'];
    $statuses = ['forwarded', 'for_release', 'rejected'];
    $counts = [];
    $docsInNeedOfAction = 0;

    // for documents in need of action
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
            MAX(log.step) + 1 AS current_step
        FROM submitted_documents doc
        LEFT JOIN document_transaction_logs log ON doc.doc_number = log.doc_number
        WHERE doc.status NOT IN('Rejected','For Release') 
            AND doc.doc_type IN(?)
        GROUP BY doc.doc_number;
    ");
    $getDocuments->execute([$settings]);
    $docs = $getDocuments->fetchAll();

    $checkDocument = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type=? AND step=? AND office=?;");

    foreach ($docs as $key => $doc) {
        $checkDocument->execute([$doc['doc_type'], $doc['current_step'], $officeId]);

        if ($checkDocument->rowCount() > 0) {
            $docsInNeedOfAction++;
        }
    }

    $counts['in_need'] = $docsInNeedOfAction;
    // end

    // for documents forwarded, rejected, released
    $getLogsCount = $c->prepare("SELECT COUNT(id) FROM document_transaction_logs WHERE office=? AND LOWER(status)=?;");

    foreach ($statuses as $key => $value) {
        $queryValue = str_replace("_", " ", $value);
        $getLogsCount->execute([$officeId, $queryValue]);
        $counts[$value] = $getLogsCount->fetchColumn();
    }

    $response['data'] = $counts;
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
