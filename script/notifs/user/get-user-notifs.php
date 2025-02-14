<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
session_start();
require_once "../../../conn.php";
require_once "../../globals.php";

try {
    $id = sanitize($_SESSION['donor_id']);

    //get donated
    // $q1 = $c->prepare("
    //    SELECT
    //         a.uid AS id,
    //         a.donation_no as reference_no,
    //         NULL AS grantee,
    //         b.description AS item,
    //         c.unit_name,
    //         a.quantity,
    //         a.timestamp AS tstamp,
    //         a.is_read,
    //         'expiry' AS type
    //     FROM expired_items_logs a
    //     LEFT JOIN items b ON a.item = b.id
    //     LEFT JOIN units c ON b.unit = c.id
    //     WHERE a.donor = ?
    //     UNION ALL
    //     SELECT
    //         a.id AS id,
    //         a.external_donation_no as reference_no,
    //         b.grantee AS grantee,
    //         c.description AS item,
    //         d.unit_name,
    //         SUM(a.quantity) AS quantity,
    //         a.timestamp AS tstamp,
    //         a.is_read,
    //         'donation' AS type
    //     FROM external_d_items_logs a
    //     LEFT JOIN external_d_s b ON a.external_d_s = b.id
    //     LEFT JOIN items c ON a.item = c.id
    //     LEFT JOIN units d ON c.unit = d.id
    //     WHERE a.donor = ?
    //     GROUP BY a.external_donation_no, a.item
    //     -- ORDER BY tstamp DESC;
    // ");
    $q1 = $c->prepare("
        SELECT
            a.id AS id,
            a.external_donation_no as reference_no,
            b.grantee AS grantee,
            c.description AS item,
            d.unit_name,
            SUM(a.quantity) AS quantity,
            a.timestamp AS tstamp,
            a.is_read,
            'donation' AS type
        FROM external_d_items_logs a
        LEFT JOIN external_d_s b ON a.external_d_s = b.id
        LEFT JOIN items c ON a.item = c.id
        LEFT JOIN units d ON c.unit = d.id
        WHERE a.donor = ?
        GROUP BY a.external_donation_no, a.item
        -- ORDER BY tstamp DESC;
    ");
    $q1->execute([$id]);
    $donatedLogs = $q1->fetchAll();

    $data = ['data' => convertToUTF8($donatedLogs)];

    echo json_encode($data);
} catch (\Throwable $e) {
    echo "<b>Error. Please Contact System Administrator. <br/></b>" . $e->getMessage();
}
