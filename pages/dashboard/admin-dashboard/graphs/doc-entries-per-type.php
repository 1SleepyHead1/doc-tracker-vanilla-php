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
    $types = json_decode($_POST['types'], true);

    $getCount = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE DATE(tstamp) BETWEEN ? AND ? AND doc_type=?;");

    foreach ($types as $type) {
        $getCount->execute([$from, $to, $type]);

        array_push($response['data'], $getCount->fetchColumn());
    }
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
