<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $getOffices = $c->prepare("
        SELECT 
            office.id,
            office.office_code,
            office.office_name,
            office.tstamp,
            user.name AS person_in_charge
        FROM offices office
        LEFT JOIN users user ON user.id = office.person_in_charge
        ORDER BY tstamp DESC;
    ");
    $getOffices->execute();
    $offices = $getOffices->fetchAll();

    $getOfficePersonnels = $c->prepare(" 
        SELECT 
            id,
            name
        FROM users
        WHERE is_office_personnel = 1
        AND id NOT IN (SELECT person_in_charge FROM offices WHERE person_in_charge IS NOT NULL)
        ORDER BY name;
    ");
    $getOfficePersonnels->execute();
    $officePersonnels = $getOfficePersonnels->fetchAll();
?>
    <datalist id="office-personnel-list">
        <?php foreach ($officePersonnels as $officePersonnel) { ?>
            <option data-id="<?= $officePersonnel['id'] ?>" value="<?= $officePersonnel['name'] ?>"></option>
        <?php } ?>
    </datalist>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fas fa-building me-2"></i>Office Registry</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" onclick="showModal(0)">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add Office
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 999px;">
                    <table id="tbl-offices" class="display table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Person in Charge</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offices as $office) { ?>
                                <tr id="tr-office-<?= $office['id'] ?>">
                                    <td><?= $office['office_name'] ?></td>
                                    <td><?= $office['office_code'] ?></td>
                                    <td><?= is_null($office['person_in_charge']) ? '-' : $office['person_in_charge'] ?></td>
                                    <td><?= date("M. d, Y h:i A", strtotime($office['tstamp'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit Office" onclick="showModal(1,<?= $office['id'] ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete Office" onclick="deleteOffice(<?= $office['id'] ?>)">
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

    <!-- Office Modal -->
    <div class="modal fade" id="office-registry-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/registry/office-registry/js/office-registry.js"></script>
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