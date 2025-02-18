<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $action = $_POST['action'];
    $id = $_POST['id'];

    $getDetails = $c->prepare("
        SELECT
            office.office_code,
            office.office_name,
            setting.step
        FROM document_transaction_setting setting
        LEFT JOIN document_types doc ON setting.doc_type = doc.id
        LEFT JOIN offices office ON setting.office = office.id
        WHERE setting.doc_type = ?
        ORDER BY setting.step;");
    $getDetails->execute([$id]);
    $details = $getDetails->fetchAll();
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-list-ul me-2"></i>
            <?= $action == 0 ? "Add New Setting" : "Update Setting" ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="doc-trasnsaction-setting-form">
            <table id="tbl-doc-transaction-setting-entry" class="table table-bordered">
                <colgroup>
                    <col style="width: 10%;">
                    <col style="width: 80%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Office</th>
                        <th><a type="button" id="btn-add-row" class="w-100 fw-bold text-center cursor-pointer"><u>Add</u></a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($action == 0) { ?>
                        <tr class="text-center fs-bold">
                            <td>1</td>
                            <td>
                                <input type="text" id="office-list-input-1" class="form-control form-control-md" list="office-list" data-list-type="office" onchange="validateDataListOptions('office-list-input-1','','office-list');" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($details as $setting) { ?>
                            <tr>
                                <td><?= $setting['step'] ?></td>
                                <td>
                                    <input type="text" id="office-list-input-<?= $setting['step'] ?>" class="form-control form-control-md" list="office-list" data-list-type="office" value="<?= $setting['office_code'] . " [" . $setting['office_name'] . "]" ?>" onchange="validateDataListOptions('office-list-input-<?= $setting['step'] ?>','','office-list');" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#doc-trasnsaction-setting-form").submit(function(e) {
                e.preventDefault();
                <?= $action == 0 ? "insertUpdateDocTransactionSetting(0);" : "insertUpdateDocTransactionSetting(1,$id);" ?>
            });

            $("#btn-add-row").click(function() {
                const tbody = $("#tbl-doc-transaction-setting-entry tbody");
                const rowCount = tbody.find("tr").length;
                let currentOrder = <?php echo isset($currentOrder) ? $currentOrder : "1" ?>;
                tbody.append(`
                            <tr>
                                <td class="text-center fs-bold">${currentOrder+rowCount}</td>
                                <td>
                                    <input type="text" id="office-list-input-${currentOrder+rowCount}" class="form-control form-control-md" list="office-list" data-list-type="office" onchange="validateDataListOptions('office-list-input-${currentOrder+rowCount}','','office-list');" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `);

                currentOrder++;
            });
        });

        function removeRow(e) {
            const element = $(e);
            const trCount = $(`#tbl-doc-transaction-setting-entry tbody tr`).length;
            const currentOrder = <?php echo isset($currentOrder) ? $currentOrder : "1" ?>;

            if (trCount > 1) {
                element.closest("tr").remove();

                $('#tbl-doc-transaction-setting-entry tbody tr').each(function(index, tr) {
                    $(tr).find('td').first().html(currentOrder + index);
                });
            }
        }
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