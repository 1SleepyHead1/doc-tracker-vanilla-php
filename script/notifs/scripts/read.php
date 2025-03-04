<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

session_start();
require_once "../../../conn.php";
require_once "../../globals.php";

try {
    $c->beginTransaction();

    $response = ['status' => true, 'message' => ''];
    $action = sanitize($_POST['action']);
    $id = sanitize($_POST['id']);

    if ($_SESSION['is_admin'] == 0) {
        $column = $_SESSION['is_office_personnel'] == 0 ? "submitter_read" : "office_read";
    } else {
        $column = "admin_read";
    }


    if ($action == 0) {
        $read = $c->prepare("UPDATE document_transaction_logs SET $column = 1 WHERE id = ?");
        $read->execute([$id]);
    } else {
        if ($_SESSION['is_admin'] == 0) {
            if ($_SESSION['is_office_personnel'] == 0) {
                $read = $c->prepare("
                    UPDATE document_transaction_logs log
                    LEFT JOIN submitted_documents doc ON log.doc_number = doc.doc_number
                    SET log.$column = 1 WHERE doc.user_id = ?
                ");
                $read->execute([$_SESSION['user_id']]);
            } else {
            }
        } else {
            $read = $c->prepare("
                UPDATE document_transaction_logs log
                LEFT JOIN submitted_documents doc ON log.doc_number = doc.doc_number
                SET log.$column = 1
            ");
            $read->execute();
        }
    }

    if ($response['status']) {
        $c->commit();
    }
} catch (PDOException $e) {
    $c->rollBack();

    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
