<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../conn.php";
require_once "../globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $userId = $_POST['u'];
    $existings = json_decode($_POST['existings'], true);

    $whereClause = "";

    if (!empty($existings)) {
        $placeholders = str_repeat('?,', count($existings) - 1) . '?';
        $whereClause = " AND log.id NOT IN ($placeholders)";
    }

    $getNotifs = $c->prepare(" 
       SELECT
            log.id,
            doc.doc_number AS doc_no,
            log.status,
            log.tstamp
        FROM document_transaction_logs log
        LEFT JOIN submitted_documents doc
            ON log.doc_number = doc.doc_number
        WHERE submitter_read = 0
            AND doc.user_id = ?
            $whereClause
        ORDER BY log.tstamp;
    ");

    if (!empty($existings)) {
        $params = array_merge([$userId], $existings);
        $getNotifs->execute($params);
    } else {
        $getNotifs->execute([$userId]);
    }

    $notifs = $getNotifs->fetchAll();

    // foreach ($notifs as $value) {
    //     $data = [
    //         'id' => $value['id'],
    //         'doc_no' => $value['doc_number'],
    //         'status' => $value['status'],
    //         'stamp' => getLogsStamp($value['tstamp'])
    //     ];

    //     array_push($response['data'], $data);
    // }

    $response['data'] = $notifs;
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
