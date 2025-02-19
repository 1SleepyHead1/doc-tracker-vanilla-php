<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {

    $getDocuments = $c->prepare("
        SELECT
            doc.id,
            doc.doc_number,
            doc_type.doc_type_name AS doc_type,
            doc.status,
            user.name AS submitter,
            doc.tstamp
        FROM submitted_documents doc
        LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id
        LEFT JOIN users user ON doc.user_id = user.id
        ORDER BY doc.tstamp DESC;
   ");
    $getDocuments->execute();
    $documents  = $getDocuments->fetchAll();

    $getSubmitters = $c->prepare("SELECT  id, name FROM users WHERE id<>1 ORDER BY name;");
    $getSubmitters->execute();
    $submitters = $getSubmitters->fetchAll();
?>
    <datalist id="submitter-list">
        <?php foreach ($submitters as $submitter) { ?>
            <option data-id="<?= $submitter['id'] ?>" value="<?= $submitter['name'] ?>"></option>
        <?php } ?>
    </datalist>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4><i class="fas fa-file-alt"></i> Document Entries</h4>
                            <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" onclick="showModal(0)">
                                <i class="fas fa-plus-circle me-2"></i>
                                Add Document
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive" style="max-height: 999px;">
                        <table id="tbl-documents" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Document No.</th>
                                    <th>Type</th>
                                    <th>Submitter</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc) { ?>
                                    <tr class="cursor-pointer text-center" data-id="<?= $docType['id'] ?>" data-id-no="<?= $docType['doc_no'] ?>" onclick="">
                                        <td><?= $docType['doc_number'] ?></td>
                                        <td><?= $docType['doc_type'] ?></td>
                                        <td><?= $docType['submitter'] ?></td>
                                        <td><span class="badge bg-success">New</span></td>
                                        <td><?= date("M. d, Y h:i A", strtotime($doc['tstamp'])) ?></td>
                                        <td></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-list-ul"></i> Setting</h3>
                    </div>
                    <div class="card-body" id="_doc-map">
                    </div>
                </div>

                <style>
                    .timeline {
                        padding: 10px;
                        margin: 0;
                    }

                    .timeline-panel {
                        padding: 15px;
                        margin-bottom: 20px;
                        background: #f9f9f9;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    }

                    .timeline-badge {
                        width: 50px;
                        height: 50px;
                        line-height: 50px;
                        text-align: center;
                        border-radius: 50%;
                        background-color: #007bff;
                        color: white;
                        font-weight: bold;
                        margin-top: 10px;
                    }

                    .expanded-timeline .timeline-panel {
                        width: 90%;
                        margin: 0 auto;
                    }
                </style>
            </div> -->
        </div>
    </div>

    <!-- Document Mapping Modal -->
    <div class="modal fade" id="doc-submission-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/transactions/doc-submission/js/doc-submission.js"></script>
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