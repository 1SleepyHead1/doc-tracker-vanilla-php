<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

session_start();
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $userId = $_SESSION['user_id'];
    $officeId = $_SESSION['office_id'];
    $statuses = ['new', 'forwarded', 'for_release', 'rejected'];
    $submittedCount = [];

?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fa fa-exclamation-circle fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Documents in Need of Action</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-paper-plane fa-3x text-warning"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Forwarded Documents</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
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
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
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
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>
        </div>

        <div id="_docs" class="row mb-4" of="<?= $officeId ?>" u="<?= $userId ?>">
            <div class="col-12">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4><i class="fas fa-file-alt"></i> Documents</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="docTabs" role="tablist">
                            <!-- <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="entries-tab" data-bs-toggle="tab" href="#entries" role="tab" aria-controls="entries" aria-selected="true">Documents in need of Action</a>
                            </li> -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="handled-tab" data-bs-toggle="tab" href="#handled" role="tab" aria-controls="handled" aria-selected="false">Documents Handled</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="submissions-tab" data-bs-toggle="tab" href="#submissions" role="tab" aria-controls="submissions" aria-selected="false">Document Submissions</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- <div class="tab-pane fade show active" id="entries" role="tabpanel" aria-labelledby="entries-tab">
                                <div id="_doc-entries" class="table-responsive" style="max-height: 999px;">
                                </div>
                            </div> -->
                            <div class="tab-pane fade show active" id="handled" role="tabpanel" aria-labelledby="handled-tab">
                                <div class="row mb-4">
                                    <!-- <div class="col-md-2">
                                        <label for="date-from-a" class="form-label">Date From</label>
                                        <input type="date" id="date-from-a" class="form-control form-control-sm" value="<?= date('Y') . "-" . date('m') . "-01" ?>" onchange="">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="date-to-a" class="form-label">Date To</label>
                                        <input type="date" id="date-to-a" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="">
                                    </div> -->
                                    <div class="col-md-2">
                                        <label for="doc-status-a" class="form-label">Action Taken</label>
                                        <select class="form-select form-control form-control-sm" id="doc-status-a" name="doc-status-a" onchange="">
                                            <option value="">All</option>
                                            <option value="Forwarded">Forwarded</option>
                                            <option value="For Release">For Release</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="_doc-handled" class="table-responsive" style="max-height: 999px;">
                                    <!-- show handled documents here -->
                                    <table id="tbl-handled-docs" class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Document No.</th>
                                                <th>Type</th>
                                                <th>Submitter</th>
                                                <th>Purpose</th>
                                                <th>Status</th>
                                                <th>Date Handled</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Handled documents data should be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label for="date-from-b" class="form-label">Date From</label>
                                        <input type="date" id="date-from-b" class="form-control form-control-sm" value="<?= date('Y') . "-" . date('m') . "-01" ?>" onchange="loadDocSubmissions()">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="date-to-b" class="form-label">Date To</label>
                                        <input type="date" id="date-to-b" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="loadDocSubmissions()">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="doc-status-b" class="form-label">Status</label>
                                        <select class="form-select form-control form-control-sm" id="doc-status-b" name="doc-status-b" onchange="loadDocSubmissions()">
                                            <option value="">All</option>
                                            <option value="New">New</option>
                                            <option value="Forwarded">Forwarded</option>
                                            <option value="For Release">For Release</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="_doc-submissions" class="table-responsive" style="max-height: 999px;">
                                    <!-- show submitted documents here -->
                                </div>
                            </div>
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

    <script src="pages/dashboard/office-dashboard/js/office-dashboard.js"> </script>
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