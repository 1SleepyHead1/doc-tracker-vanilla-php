<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $uploadDir = "../../../../assets/uploads/qr-codes/";
    $id = $_POST['id'];


    $getDocNo = $c->prepare("SELECT doc_number FROM submitted_documents WHERE id = ?");
    $getDocNo->execute([$id]);
    $docNo = $getDocNo->fetchColumn();

    $qrCode = imgToBlob($uploadDir . $docNo . ".png");
?>

    <div class="modal-header">
        <!-- <h5 class="modal-title">QR Code</h5> -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div id="_qr-code" class="modal-body text-center">
        <img id="qr-code-image" src="<?= $qrCode ?>" alt="QR Code" class="img-fluid">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" onclick="donwloadQrCode('<?= $docNo ?>')">
            <i class="fas fa-download"></i> Download
        </button>
        <button type="button" class="btn btn-sm btn-success" onclick="previewPrint('_qr-code')">
            <i class="fas fa-print"></i> Print
        </button>
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
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