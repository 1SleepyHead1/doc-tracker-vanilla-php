<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $response = ['status' => true, 'message' => '', 'data' => []];
    $statuses = ['for_release', 'rejected'];
    $counts = [];

    $getTotalSubmission = $c->prepare("SELECT COUNT(*) FROM submitted_documents");
    $getTotalSubmission->execute();
    $totalSubmitted = $getTotalSubmission->fetchColumn();
    $counts['entries'] = $totalSubmitted;

    $getPendingSubmission = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE status IN('New','Forwarded')");
    $getPendingSubmission->execute();
    $totalPending = $getPendingSubmission->fetchColumn();
    $counts['pending'] = $totalPending;

    $getSubmission = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE LOWER(status)=?;");

    foreach ($statuses as $key => $value) {
        $queryValue = str_replace("_", " ", $value);
        $getSubmission->execute([$queryValue]);
        $counts[$value] = $getSubmission->fetchColumn();
    }

    $response['data'] = $counts;
} catch (PDOException $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
