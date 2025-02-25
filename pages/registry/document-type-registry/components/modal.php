<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $action = $_POST['action'];
    $id = $_POST['id'];

    $getDetails = $c->prepare("SELECT doc_type_code, doc_type_name FROM document_types WHERE id = ?");
    $getDetails->execute([$id]);
    $details = $getDetails->fetch();
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-file-signature me-2"></i>
            <?= $action == 0 ? "Add New Document Type" : "Update Document Type" ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="document-type-registry-form">
            <div class="row g-2">
                <div class="col-md-12">
                    <label for="doc-type-code" class="form-label small">Document Type Code</label>
                    <input type="text" class="form-control form-control-md" id="doc-type-code" name="doc-type-code" value="<?= $action == 0 ? "" : $details['doc_type_code'] ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="doc-type-name" class="form-label small">Document Type Name</label>
                    <input type="text" class="form-control form-control-md" id="doc-type-name" name="doc-type-name" value="<?= $action == 0 ? "" : $details['doc_type_name'] ?>" required>
                </div>
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#document-type-registry-form").submit(function(e) {
                e.preventDefault();
                <?= $action == 0 ? "insertUpdateDocType(0);" : "insertUpdateDocType(1,$id);" ?>
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