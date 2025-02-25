<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../../conn.php";
require_once "../../../../script/globals.php";
require_once "../../../../vendor/autoload.php";

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $uploadDir = "../../../../assets/uploads/qr-codes/";
    $docType = sanitize($_POST['docType']);
    $docTypeId = sanitize($_POST['docTypeId']);
    $purpose = sanitize($_POST['purpose']);
    $submitter = sanitize($_POST['submitter']);
    $submitterId = sanitize($_POST['submitterId']);
    $docNo = generateDocNo();

    $checkSetting = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ?");
    $checkSetting->execute([$docTypeId]);

    if ($checkSetting->rowCount() == 0) {
        $response['status'] = false;
        $response['message'] = 'No existing setting for this document type.';
        echo json_encode($response);
        exit;
    }

    $insert = $c->prepare("
        INSERT INTO submitted_documents(
            doc_number,
            doc_type,
            user_id,
            purpose
        ) VALUES(?,?,?,?)
    ");
    $insert->execute([$docNo, $docTypeId, $submitterId, $purpose]);
    $id = $c->lastInsertId();

    $getTstamp = $c->prepare("SELECT tstamp FROM submitted_documents WHERE id = ?");
    $getTstamp->execute([$id]);
    $tstamp = $getTstamp->fetchColumn();

    if ($response['status']) {

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $docNo . '.png';
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: 'http://localhost/document-tracker/login.php',
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 500,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            // logoPath: $filePath,
            // logoResizeToWidth: 50,
            // logoPunchoutBackground: true,
            labelText: $docNo,
            labelFont: new OpenSans(20),
            labelAlignment: LabelAlignment::Center
        );

        $result = $builder->build();
        $result->saveToFile($filePath);

        saveDocTransactionLogs($c, $docNo);

        $c->commit();
        $response['data'] = [
            'id' => $id,
            'doc_no' => $docNo,
            'type' => $docType,
            'submitter' => $submitter,
            'purpose' => $purpose,
            'status' => 'New',
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
