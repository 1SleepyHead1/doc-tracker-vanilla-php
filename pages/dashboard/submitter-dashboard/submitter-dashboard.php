<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

session_start();
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $userId = $_SESSION['user_id'];
    $statuses = ['new', 'forwarded', 'for_release', 'rejected'];
    $submittedCount = [];

    $getTotalSubmission = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE user_id=?");
    $getTotalSubmission->execute([$userId]);
    $totalSubmitted = $getTotalSubmission->fetchColumn();

    $getSubmission = $c->prepare("SELECT COUNT(*) FROM submitted_documents WHERE user_id=? AND LOWER(status)=?;");

    foreach ($statuses as $key => $value) {
        $queryValue = str_replace("_", " ", $value);
        $getSubmission->execute([$userId, $queryValue]);
        $submittedCount[$value] = $getSubmission->fetchColumn();
    }
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-file-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Submitted Documents</h5>
                        <h2 class="font-weight-bold text-gray-800"><?= $totalSubmitted ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Pending Documents</h5>
                        <h2 class="font-weight-bold text-gray-800"><?= ($submittedCount['new'] + $submittedCount['forwarded']) ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Rejected Documents</h5>
                        <h2 class="font-weight-bold text-gray-800"><?= $submittedCount['rejected'] ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Released Documents</h5>
                        <h2 class="font-weight-bold text-gray-800"><?= $submittedCount['for_release'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4><i class="fas fa-file-alt"></i> Document Submissions</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <label for="date-from" class="form-label">Date From</label>
                                <input type="date" id="date-from" class="form-control form-control-sm" value="<?= date('Y') . "-" . date('m') . "-01" ?>" onchange="loadDocSubmissions()">
                            </div>
                            <div class="col-md-2">
                                <label for="date-to" class="form-label">Date To</label>
                                <input type="date" id="date-to" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="loadDocSubmissions()">
                            </div>
                            <div class="col-md-2">
                                <label for="doc-status" class="form-label">Status</label>
                                <select class="form-select form-control form-control-sm" id="doc-status" name="doc-status" onchange="loadDocSubmissions()">
                                    <option value="">All</option>
                                    <option value="New">New</option>
                                    <option value="Forwarded">Forwarded</option>
                                    <option value="For Release">For Release</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div id="_doc-submissions" class="table-responsive" u="<?= $userId ?>" style="max-height: 999px;">
                            <!-- show doc submissions here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code View Modal -->
    <div class="modal fade" id="qr-code-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div id="_qr_modal_content" class="modal-content modal-reset">
                <!-- show qr modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/dashboard/submitter-dashboard/js/submitter-dashboard.js"> </script>
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