<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $type = sanitize($_POST['type']);
    $officeId = sanitize($_POST['office']);
    $officeN = sanitize($_POST['officeN']);

    if ($type == "daily") {
        $date = sanitize($_POST['date']);
        $conditionA = "DATE(doc.tstamp) = '$date'";
        $conditionB = "AND DATE(doc.tstamp) = '$date'";
        $header = date("F d, Y", strtotime($date));
    } elseif ($type == "monthly") {
        $month = sanitize($_POST['month']);
        $monthN = sanitize($_POST['monthN']);
        $year = sanitize($_POST['year']);
        $conditionA = "MONTH(doc.tstamp) = $month AND YEAR(doc.tstamp) = $year";
        $conditionB = "AND MONTH(doc.tstamp) = $month AND YEAR(doc.tstamp) = $year";
        $header = "$monthN, $year";
    } elseif ($type == "yearly") {
        $year = sanitize($_POST['year']);
        $conditionA = "YEAR(doc.tstamp) = $year";
        $conditionB = "AND YEAR(doc.tstamp) = $year";
        $header = "$year";
    }

    if ($officeId === "") {
        $getDocs = $c->prepare("
             SELECT
                doc.id,
                doc.doc_number,
                doc_type.doc_type_name,
                doc.purpose, 
                doc.status,
                user.name AS submitter,
                doc.tstamp
            FROM submitted_documents doc
            LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id
            LEFT JOIN users user ON doc.user_id = user.id
            WHERE $conditionA;
        ");
        $getDocs->execute();
    } else {
        $getSettings = $c->prepare("
            SELECT
                GROUP_CONCAT(DISTINCT setting.doc_type)
            FROM document_transaction_setting setting
            LEFT JOIN document_types type
                ON setting.doc_type = type.id
            WHERE setting.office = ?;
        ");
        $getSettings->execute([$officeId]);
        $settings = $getSettings->fetchColumn();

        $getDocs = $c->prepare("
            SELECT
                doc.id,
                doc.doc_number,
                doc.doc_type,
                MAX(log.step) + 1 AS current_step,
                user.name as submitter,
                doc_type.doc_type_name,
                doc.purpose,
                doc.status,
                doc.tstamp
            FROM submitted_documents doc
            LEFT JOIN document_transaction_logs log ON doc.doc_number = log.doc_number
            LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id 
            LEFT JOIN users user ON doc.user_id = user.id
            WHERE doc.doc_type IN(?)
                $conditionB
            GROUP BY doc.doc_number;
        ");
        $getDocs->execute([$settings]);

        $checkDocumentInLogs = $c->prepare("SELECT id FROM document_transaction_logs WHERE doc_number=? AND office=?;");
        $checkDocumentInOffice = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ? AND step = ? AND office = ?;");
    }

    $docs = $getDocs->fetchAll();
?>
    <div>
        <!-- <div class="row">
            <div class="col-2 text-center">
                <div class="avatar avatar-xl">
                    <img src="assets/img/reports-header-logo.png" alt="..." class="avatar-img rounded-circle">
                </div>
            </div>

            <div class="col-8 d-flex justify-content-center">
                <div class="avatar avatar-xl">
                    <img src="assets/img/logo-menu.png" alt="" class="avatar-img" style="max-width: 15%;">
                </div>
            </div>
            <div class="col-2"></div>
        </div> -->
        <h6 class="fw-semibold text-center">Document Entries Report for <?= $officeN == "All" ? "All Offices" : $officeN ?> as of <?= $header ?></h6>
    </div>
    <div class="table-responsive mt-4" style="max-height: 999px;">
        <table id="tbl-doc-entries-report" class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Document No.</th>
                    <th>Type</th>
                    <th>Submitter</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($docs) === 0) { ?>
                    <tr>
                        <td valign="top" colspan="7" class="dataTables_empty">
                            <div class="text-center p-3"><i class="fa fa-exclamation-circle fa-2x text-danger mb-2"></i><br><span class="text-muted">No record(s) found.</span></div>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php if ($officeId === "") { ?>
                        <?php foreach ($docs as $index => $doc) { ?>
                            <tr>
                                <td><?= ($index + 1) ?></td>
                                <td><?= $doc['doc_number'] ?></td>
                                <td><?= $doc['doc_type_name'] ?></td>
                                <td><?= $doc['submitter'] ?></td>
                                <td><?= $doc['purpose'] ?></td>
                                <td><?= $doc['status'] ?></td>
                                <td><?= date("M. d, Y", strtotime($doc['tstamp'])) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($docs as $index => $doc) {
                            $checkDocumentInLogs->execute([$doc['doc_number'], $officeId]);
                        ?>
                            <?php if ($checkDocumentInLogs->rowCount() > 0) { ?>
                                <tr>
                                    <td><?= ($index + 1) ?></td>
                                    <td><?= $doc['doc_number'] ?></td>
                                    <td><?= $doc['doc_type_name'] ?></td>
                                    <td><?= $doc['submitter'] ?></td>
                                    <td><?= $doc['purpose'] ?></td>
                                    <td><?= $doc['status'] ?></td>
                                    <td><?= date("M. d, Y", strtotime($doc['tstamp'])) ?></td>
                                </tr>
                            <?php } else {
                                $checkDocumentInOffice->execute([$doc['doc_type'], $doc['current_step'], $officeId]);
                            ?>
                                <?php if ($checkDocumentInOffice->rowCount() > 0) { ?>
                                    <tr>
                                        <td><?= ($index + 1) ?></td>
                                        <td><?= $doc['doc_number'] ?></td>
                                        <td><?= $doc['doc_type_name'] ?></td>
                                        <td><?= $doc['submitter'] ?></td>
                                        <td><?= $doc['purpose'] ?></td>
                                        <td><?= $doc['status'] ?></td>
                                        <td><?= date("M. d, Y", strtotime($doc['tstamp'])) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {});
    </script>

<?php
    sleep(1);
} catch (\Throwable $e) {
?>
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Error: The requested page could not be found. Please contact your system administrator for assistance.
    </div>

    <script>
        console.error("<?php echo $e->getMessage(); ?>");
    </script>
<?php } ?>