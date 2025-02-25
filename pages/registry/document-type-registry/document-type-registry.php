<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $getDocTypes = $c->prepare("
        SELECT 
            id,
            doc_type_code,
            doc_type_name,
            tstamp
        FROM document_types
        ORDER BY tstamp DESC;
    ");
    $getDocTypes->execute();
    $docTypes = $getDocTypes->fetchAll();
?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fas fa-file-signature me-2"></i>Document Type Registry</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" onclick="showModal(0)">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add Document Type
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 999px;">
                    <table id="tbl-doc-types" class="display table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docTypes as $docType) { ?>
                                <tr id="tr-doc-type-<?= $docType['id'] ?>">
                                    <td><?= $docType['doc_type_name'] ?></td>
                                    <td><?= $docType['doc_type_code'] ?></td>
                                    <td><?= date("M. d, Y h:i A", strtotime($docType['tstamp'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit Document Type" onclick="showModal(1,<?= $docType['id'] ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete Document Type" onclick="deleteDocType(<?= $docType['id'] ?>)">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Type Modal -->
    <div class="modal fade" id="document-type-registry-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/registry/document-type-registry/js/document-type-registry.js"></script>
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