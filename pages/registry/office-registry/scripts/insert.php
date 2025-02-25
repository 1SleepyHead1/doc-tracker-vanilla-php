<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $officeCode = sanitize($_POST['officeCode']);
    $officeName = sanitize($_POST['officeName']);
    $personInCharge = sanitize($_POST['personInCharge']);
    $personInChargeId = empty($_POST['personInChargeId']) ? null : sanitize($_POST['personInChargeId']);

    $checkOfficeCode = $c->prepare("SELECT id FROM offices WHERE LOWER(office_code) = LOWER(?)");
    $checkOfficeCode->execute([$officeCode]);
    $officeCodeExists = $checkOfficeCode->fetchColumn();

    $checkOfficeName = $c->prepare("SELECT id FROM offices WHERE LOWER(office_name) = LOWER(?)");
    $checkOfficeName->execute([$officeName]);
    $officeNameExists = $checkOfficeName->fetchColumn();

    if ($officeCodeExists || $officeNameExists) {
        $response['status'] = false;
        $response['message'] = 'Office already exists.';

        echo json_encode($response);
        exit;
    }

    $insert = $c->prepare("
        INSERT INTO offices(
            office_code,
            office_name,
            person_in_charge
        ) VALUES(?,?,?);
    ");

    $officeCode = strtoupper($officeCode);
    $officeName = ucfirst($officeName);

    $insert->execute([$officeCode, $officeName, $personInChargeId]);
    $id = $c->lastInsertId();

    $getTstamp = $c->prepare("SELECT tstamp FROM offices WHERE id = ?");
    $getTstamp->execute([$id]);
    $tstamp = $getTstamp->fetchColumn();

    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'office_code' => $officeCode,
            'office_name' => $officeName,
            'person_in_charge' => is_null($personInChargeId) ? "-" : $personInCharge,
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
