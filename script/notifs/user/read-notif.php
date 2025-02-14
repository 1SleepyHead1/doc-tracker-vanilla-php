<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../globals.php";

try {
    $c->beginTransaction();
    $action = sanitize($_POST['action']);

    if ($action == 0) {
        $id = sanitize($_POST['id']);
        $type = sanitize($_POST['type']);

        if ($type == "donation") {
            $table = "external_d_items_logs";
            $field = "id";
        } else {
            $table = "expired_items_logs";
            $field = "uid";
        }

        $q = $c->prepare("UPDATE $table SET is_read = 1 WHERE $field = ?");
        $q->execute([$id]);
    } else {
        session_start();
        $id = sanitize($_SESSION['donor_id']);

        $q1 = $c->prepare("UPDATE external_d_items_logs SET is_read = 1 WHERE donor = ?");
        $q2 = $c->prepare("UPDATE expired_items_logs SET is_read = 1 WHERE donor = ?");

        $q1->execute([$id]);
        $q2->execute([$id]);
    }

    $c->commit();
    echo "s";
} catch (\Throwable $e) {
    echo "<b>Error. Please Contact System Administrator. <br/></b>" . $e->getMessage();
}
