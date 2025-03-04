<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Invalid request!');
}

require_once "../../conn.php";
require_once "../globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $user = sanitize($_POST['u']);
    $docNo = sanitize($_POST['docNo']);
    $docType = sanitize($_POST['docType']);
    $currentStep = sanitize($_POST['currentStep']);
    $remarks = sanitize($_POST['remarks']);
    $status = "Rejected";
    $doesNextStep = true;

    // get current office to handle the document
    $getCurrentOffice = $c->prepare("SELECT office FROM document_transaction_setting WHERE doc_type=? AND step=?;");
    $getCurrentOffice->execute([$docType, $currentStep]);
    $currentOffice = $getCurrentOffice->fetchColumn();

    $getMaxStep = $c->prepare("SELECT MAX(step) FROM document_transaction_setting WHERE doc_type=?;");
    $getMaxStep->execute([$docType]);
    $maxStep = $getMaxStep->fetchColumn();

    // compare person in charge with the current loggedin user
    $checkPersonInCharge = $c->prepare("SELECT id FROM offices WHERE id=? AND person_in_charge=?;");
    $checkPersonInCharge->execute([$currentOffice, $user]);

    if ($checkPersonInCharge->rowCount() == 0) {
        $response['status'] = false;
        $response['message'] = "You don not have permission for this action.";

        echo json_encode($response);
        exit;
    }

    $update = $c->prepare("UPDATE submitted_documents SET status=? WHERE doc_number=?;");
    $update->execute([$status, $docNo]);

    if ($response['status']) {
        saveDocTransactionLogs($c, $docNo, $currentStep, $status, $currentOffice, $user, $remarks === "" ? null : $remarks);

        $getTstamp = $c->prepare("SELECT tstamp FROM document_transaction_logs WHERE doc_number=? AND step=?");
        $getTstamp->execute([$docNo, $currentStep]);

        $c->commit();

        $response['does_next_step'] = $doesNextStep;
        $response['status'] = $status;
        $response['tstamp'] = $getTstamp->fetchColumn();
        $response['remarks'] = $remarks === "" ? "-" : $remarks;
    }
} catch (PDOException $e) {
    $c->rollBack();
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
