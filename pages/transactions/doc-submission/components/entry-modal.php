<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $action = $_POST['action'];
    $id = $_POST['id'];

    $getDocTypes = $c->prepare("
        SELECT 
            id,
            doc_type_code,
            doc_type_name,
            tstamp
        FROM document_types
        ORDER BY doc_type_code;
    ");
    $getDocTypes->execute();
    $docTypes = $getDocTypes->fetchAll();

    $getDetails = $c->prepare("
        SELECT 
            doc.doc_type,
            user.name AS submitter,
            doc.purpose 
        FROM submitted_documents doc
        LEFT JOIN users user ON doc.user_id = user.id
        WHERE doc.id=?
    ");
    $getDetails->execute([$id]);
    $details = $getDetails->fetch();
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-file-alt me-2"></i>
            <?= $action == 0 ? "Add New Document" : "Edit Document" ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="doc-entry-form">
            <div class="row g-2">
                <div class="col-md-12">
                    <label for="doc-type" class="form-label small">Type</label>
                    <select class="form-select form-control" id="doc-type" name="doc-type" required>
                        <?php foreach ($docTypes as $docType) { ?>
                            <option value="<?= $docType['id'] ?>"><?= $docType['doc_type_name'] ?></option>
                        <?php } ?>
                    </select>

                    <?php if ($action == 1) { ?>
                        <script>
                            $("#doc-type").val(<?= $details['doc_type'] ?>);
                        </script>
                    <?php } ?>
                </div>
                <div class="col-md-12">
                    <label for="submitter" class="form-label small">Submitter</label>
                    <input type="text" class="form-control form-control-md" id="submitter" name="submitter" list="submitter-list" data-list-type="submitter" value="<?= $action == 0 ? "" : $details['submitter'] ?>" onchange="validateDataListOptions('submitter','','submitter-list');" required>
                </div>
                <div class="col-md-12">
                    <label for="purpose" class="form-label small">Purpose</label>
                    <textarea name="purpose" id="purpose" class="form-control form-control-md" rows="3" required><?= $action == 0 ? "" : $details['purpose'] ?></textarea>
                </div>
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
    </div>

    <script>
        $("#doc-entry-form").submit(function(e) {
            e.preventDefault();
            <?= $action == 0 ? "insertUpdateDocument(0)" : "insertUpdateDocument(1,$id)" ?>
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