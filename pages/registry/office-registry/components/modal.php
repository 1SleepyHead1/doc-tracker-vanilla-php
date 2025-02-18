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
            user.name AS person_in_charge,
            user.id AS person_in_charge_id
        FROM offices office
        LEFT JOIN users user ON user.id = office.person_in_charge
        WHERE office.id = ?");
    $getDetails->execute([$id]);
    $details = $getDetails->fetch();
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-building me-2"></i>
            <?= $action == 0 ? "Add New Office" : "Update Office" ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="office-registry-form">
            <div class="row g-2">
                <div class="col-md-12">
                    <label for="office-code" class="form-label small">Office Code</label>
                    <input type="text" class="form-control form-control-md" id="office-code" name="office-code" value="<?= $action == 0 ? "" : $details['office_code'] ?>" required>
                </div>
                <div class="col-md-12">
                    <label for="office-name" class="form-label small">Office Name</label>
                    <input type="text" class="form-control form-control-md" id="office-name" name="office-name" value="<?= $action == 0 ? "" : $details['office_name'] ?>" required>
                </div>

                <div class="col-md-12">
                    <label for="person-in-charge" class="form-label small">Person in Charge</label>
                    <input type="text" class="form-control form-control-md" id="person-in-charge" name="person-in-charge" list="office-personnel-list" data-list-type="office personnel" value="<?= $action == 0 ? "" : $details['person_in_charge'] ?>" onchange="validateDataListOptions('person-in-charge','','office-personnel-list');">
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
            $("#office-registry-form").submit(function(e) {
                e.preventDefault();
                <?= $action == 0 ? "insertUpdateOffice(0);" : "insertUpdateOffice(1,$id);" ?>
            });

            <?php if ($action == 1) { ?>
                <?php if (!is_null($details['person_in_charge_id'])) { ?>
                    $("#office-personnel-list").append(`<option data-id="<?= $details['person_in_charge_id'] ?>" value="<?= $details['person_in_charge'] ?>"><?= $details['person_in_charge'] ?></option>`);
                <?php } ?>
            <?php } ?>
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