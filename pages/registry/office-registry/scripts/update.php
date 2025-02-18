<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $id = sanitize($_POST['id']);
    $officeCode = sanitize($_POST['officeCode']);
    $officeName = sanitize($_POST['officeName']);
    $personInCharge = sanitize($_POST['personInCharge']);
    $personInChargeId = empty($_POST['personInChargeId']) ? null : sanitize($_POST['personInChargeId']);

    $checkOfficeCode = $c->prepare("SELECT id FROM offices WHERE LOWER(office_code) = LOWER(?) AND id<>?");
    $checkOfficeCode->execute([$officeCode, $id]);
    $officeCodeExists = $checkOfficeCode->fetchColumn(0);

    $checkOfficeName = $c->prepare("SELECT id FROM offices WHERE LOWER(office_name) = LOWER(?) AND id<>?");
    $checkOfficeName->execute([$officeName, $id]);
    $officeNameExists = $checkOfficeName->fetchColumn(0);


    if ($officeCodeExists || $officeNameExists) {
        $response['status'] = false;
        $response['message'] = 'Office already exists.';

        echo json_encode($response);
        exit;
    }

    $update = $c->prepare("
        UPDATE offices SET
            office_code = ?,
            office_name = ?,
            person_in_charge = ?
        WHERE id = ?;
    ");

    $officeCode = strtoupper($officeCode);
    $officeName = ucfirst($officeName);

    $update->execute([$officeCode, $officeName, $personInChargeId, $id]);

    if ($response['status']) {
        $c->commit();
        $response['data'] = [
            'id' => $id,
            'office_code' => $officeCode,
            'office_name' => $officeName,
            'person_in_charge' => is_null($personInChargeId) ? "-" : $personInCharge,
        ];
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
