<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $year = sanitize($_POST['year']);
    $months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    $counts = [];

    $getCount = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE YEAR(tstamp)=? AND MONTH(tstamp)=?;");

    foreach ($months as $value) {
        $getCount->execute([$year, $value]);

        array_push($counts, $getCount->fetchColumn());
    }

    $response['data'] = $counts;
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
