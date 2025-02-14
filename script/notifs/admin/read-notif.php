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
        $q = $c->prepare("UPDATE soon_to_expire_items_logs SET is_read = 1 WHERE id = ?");
        $q->execute([$id]);
    } else {
        $q = $c->prepare("UPDATE soon_to_expire_items_logs SET is_read = 1");
        $q->execute();
    }

    $c->commit();
    echo "s";
} catch (\Throwable $e) {
    echo "<b>Error. Please Contact System Administrator. <br/></b>" . $e->getMessage();
}
