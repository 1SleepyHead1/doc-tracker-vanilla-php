<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {

    $getDocTypes = $c->prepare("SELECT id,doc_type_code,doc_type_name FROM document_types ORDER BY doc_type_code;");
    $getDocTypes->execute();
    $docTypes = $getDocTypes->fetchAll();

    $getOffices = $c->prepare("SELECT id,office_code,office_name FROM offices ORDER BY office_code;");
    $getOffices->execute();
    $offices = $getOffices->fetchAll();
?>
    <datalist id="office-list">
        <?php foreach ($offices as $office) { ?>
            <option data-id="<?= $office['id'] ?>" value="<?= $office['office_code'] . " [" . $office['office_name'] . "]" ?>"></option>
        <?php } ?>
    </datalist>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-file-signature"></i> Document Types</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="tbl-doc-types" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($docTypes as $docType) { ?>
                                    <tr class="cursor-pointer text-center" data-id="<?= $docType['id'] ?>" onclick="loadDocMapping(<?= $docType['id'] ?>)">
                                        <td>
                                            <?= $docType['doc_type_code'] ?>
                                        </td>
                                        <td>
                                            <?= $docType['doc_type_name'] ?>
                                            <span class="float-end"><i class="fa fa-angle-right"></i></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 overflow-auto" style="max-height: 999px;">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-list-ul"></i> Setting</h4>
                    </div>
                    <div class="card-body" id="_doc-map">
                        <!-- show mapping here -->
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
                        color: white;
                        font-weight: bold;
                        margin-top: 10px;
                    }

                    .expanded-timeline .timeline-panel {
                        width: 90%;
                        margin: 0 auto;
                    }
                </style>
            </div>
        </div>
    </div>

    <!-- Document Mapping Modal -->
    <div class="modal fade" id="document-mapping-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/settings/document-mapping/js/document-mapping.js"></script>
    <script>
        $(document).ready(function() {
            $("#tbl-doc-types tbody tr:first").click();
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