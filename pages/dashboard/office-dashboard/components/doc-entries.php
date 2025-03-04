<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    // $userId = $_POST['userId'];
    $officeId = $_POST['officeId'];

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

    $getDocuments = $c->prepare("
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
        WHERE doc.status NOT IN('Rejected','For Release') 
            AND doc.doc_type IN(?)
        GROUP BY doc.doc_number;
    ");
    $getDocuments->execute([$settings]);
    $docs = $getDocuments->fetchAll();

    $checkDocument = $c->prepare("SELECT id FROM document_transaction_setting WHERE doc_type = ? AND step = ? AND office = ?;");
?>

    <table id="tbl-doc-entries" class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Document No.</th>
                <th>Type</th>
                <th>Submitter</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docs as $doc) {
                $checkDocument->execute([$doc['doc_type'], $doc['current_step'], $officeId]);
                if ($checkDocument->rowCount() > 0) {
            ?>
                    <tr>
                        <td><?= $doc['doc_number'] ?></td>
                        <td><?= $doc['doc_type_name'] ?></td>
                        <td><?= $doc['submitter'] ?></td>
                        <td><?= $doc['purpose'] ?></td>
                        <td><?= $doc['status'] ?></td>
                        <td><?= date("M. d, Y h:i A", strtotime($doc['tstamp'])) ?></td>
                        <td>
                            <div class="btn-group cursor-pointer">
                                <button class="btn btn-icon btn-clean me-0" type="button" id="dropdown-menu-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" style="font-size: 1.1em;">
                                    <li><a class="dropdown-item" onclick="showDocStats('<?= $doc['doc_number'] ?>')">View Document</a></li>
                                    <li><a class="dropdown-item" onclick="showQRCode(<?= $doc['id'] ?>)">Show QR Code</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php  } ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $("#tbl-doc-entries").DataTable({
                columnDefs: [{
                    targets: [5], // Date column index
                    type: "date",
                    render: function(data, type, row) {
                        if (type === "sort") {
                            // Convert date string to sortable format (YYYY-MM-DD)
                            let dateParts = data.split(".");
                            let monthDay = dateParts[0].split(","); // Split month and day
                            let month = {
                                Jan: "01",
                                Feb: "02",
                                Mar: "03",
                                Apr: "04",
                                May: "05",
                                Jun: "06",
                                Jul: "07",
                                Aug: "08",
                                Sep: "09",
                                Oct: "10",
                                Nov: "11",
                                Dec: "12"
                            };
                            return monthDay[1] + "-" + month[monthDay[0]] + "-" + dateParts[1];
                        }
                        return data;
                    }
                }],
                columnDefs: [{
                    targets: [6],
                    orderable: false // Disable sorting
                }],
                order: []
            });
        });
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