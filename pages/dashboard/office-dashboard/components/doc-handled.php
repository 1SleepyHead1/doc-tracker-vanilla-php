<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    // $dateFrom = $_POST['dateFrom'];
    // $dateTo = $_POST['dateTo'];
    $officeId = $_POST['officeId'];
    $docStatus = $_POST['docStatus'];

    $docStatus = $docStatus === "" ? "" : "AND log.status = '$docStatus'";

    $getDocuments = $c->prepare("
       SELECT
            doc.id,
            doc.doc_number,
            type.doc_type_name AS doc_type,
            user.name AS submitter,
            doc.purpose,
            log.status AS action_taken,
            log.tstamp AS date_of_action
        FROM document_transaction_logs log
        LEFT JOIN submitted_documents doc ON log.doc_number = doc.doc_number
        LEFT JOIN document_types type ON doc.doc_type = type.id
        LEFT JOIN users user ON doc.user_id = user.id
        WHERE log.office = ? $docStatus;
    ");
    $getDocuments->execute([$officeId]);
    $documents = $getDocuments->fetchAll();
?>

    <table id="tbl-doc-handled" class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Document No.</th>
                <th>Type</th>
                <th>Submitter</th>
                <th>Purpose</th>
                <th>Action Taken</th>
                <th>Date of Action</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $doc) { ?>
                <tr>
                    <td><?= $doc['doc_number'] ?></td>
                    <td><?= $doc['doc_type'] ?></td>
                    <td><?= $doc['submitter'] ?></td>
                    <td><?= $doc['purpose'] ?></td>
                    <td><?= $doc['action_taken'] ?></td>
                    <td><?= date("M. d, Y h:i A", strtotime($doc['date_of_action'])) ?></td>
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
            <?php  } ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $("#tbl-doc-handled").DataTable({
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