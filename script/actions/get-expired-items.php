<?php
require_once "../../conn.php";

try {
    $c->beginTransaction();

    $q1 = $c->prepare("CALL get_expired_items();");
    $q1->execute();

    $q2 = $c->prepare("CALL get_soon_to_expire_items();");
    $q2->execute();

    $c->commit();
} catch (PDOException $e) {
    $c->rollBack();
    echo "Error. Please Contact System Administrator. <br/>" . $e->getMessage();
}
