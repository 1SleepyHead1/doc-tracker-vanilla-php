<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../globals.php";

try {

    $proc1 = $c->prepare("CALL get_expired_items();");
    $proc1->execute();

    $proc2 = $c->prepare("CALL get_soon_to_expire_items();");
    $proc2->execute();

    $q1 = $c->prepare("
        SELECT
            a.id,
            a.donation_no AS reference_no,
            CONCAT(IFNULL(b.first_name,''),' ',IFNULL(b.middle_name,''),' ',IFNULL(b.last_name,'')) AS donor,
            c.description AS item,
            d.unit_name,
            a.quantity,
            a.timestamp AS tstamp,
            a.expiry_date,
            a.is_read
        FROM soon_to_expire_items_logs a
        LEFT JOIN donors b ON a.donor = b.id
        LEFT JOIN items c ON a.item = c.id
        LEFT JOIN units d ON c.unit = d.id
        -- ORDER BY a.timestamp DESC;
    ");
    $q1->execute();
    $logs = $q1->fetchAll();

    $data = ['data' => convertToUTF8($logs)];

    echo json_encode($data);
} catch (\Throwable $e) {
    echo "<b>Error. Please Contact System Administrator. <br/></b>" . $e->getMessage();
}
