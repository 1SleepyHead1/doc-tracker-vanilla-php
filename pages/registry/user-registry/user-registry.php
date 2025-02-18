<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $userTypes = [
        "student" => "Student",
        "faculty" => "Faculty",
        "staff" => "Staff",
        "outside-client" => "Outside Client"
    ];

    $getOfficePersonnels = $c->prepare("
        SELECT
            id,
            name,
            address,
            email,
            contact_no,
            tstamp
        FROM users
        WHERE is_office_personnel = 1
        ORDER BY tstamp DESC;
    ");
    $getOfficePersonnels->execute();
    $officePersonnels = $getOfficePersonnels->fetchAll();

    $getNonOfficePersonnels = $c->prepare("
        SELECT
            id,
            user_type,
            name,
            address,
            email,
            contact_no,
            tstamp
        FROM users
        WHERE is_office_personnel = 0
        ORDER BY tstamp DESC;
    ");
    $getNonOfficePersonnels->execute();
    $nonOfficePersonnels = $getNonOfficePersonnels->fetchAll();

?>
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title"><i class="fas fa-users me-2"></i>User Registry</h4>
                <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" onclick="showModal(0)">
                    <i class="fas fa-plus-circle me-2"></i>
                    Add User
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active user-category" data-category="office" data-bs-toggle="tab" href="#office-personnel">Office Personnel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link user-category" data-category="non-office" data-bs-toggle="tab" href="#non-office-personnel">Document Submitters</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Office Personnel Tab -->
                <div id="office-personnel" class="tab-pane active">
                    <div class="table-responsive mt-3" style="max-height: 999px;">
                        <table id="tbl-office-personnel" class="display table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <!-- <th>Status</th> -->
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($officePersonnels as $officePersonnel) { ?>
                                    <tr id="tr-user-op-<?= $officePersonnel['id']; ?>">
                                        <td><?= $officePersonnel['name']; ?></td>
                                        <td><?= $officePersonnel['email']; ?></td>
                                        <td><?= $officePersonnel['contact_no']; ?></td>
                                        <td><?= $officePersonnel['address']; ?></td>
                                        <td><?= date("M. d, Y h:i A", strtotime($officePersonnel['tstamp'])) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-primary btn-sm" title="Edit User" onclick="showModal(1, <?= $officePersonnel['id']; ?>)">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteUser('office', <?= $officePersonnel['id']; ?>)">
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

                <!-- Non-Office Personnel Tab -->
                <div id="non-office-personnel" class="tab-pane fade">
                    <div class="table-responsive mt-3" style="max-height: 999px;">
                        <table id="tbl-non-office-personnel" class="display table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <!-- <th>Status</th> -->
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($nonOfficePersonnels as $nonOfficePersonnel) { ?>
                                    <tr id="tr-user-nop-<?= $nonOfficePersonnel['id']; ?>">
                                        <td><?= $userTypes[$nonOfficePersonnel['user_type']]; ?></td>
                                        <td><?= $nonOfficePersonnel['name']; ?></td>
                                        <td><?= $nonOfficePersonnel['email']; ?></td>
                                        <td><?= $nonOfficePersonnel['contact_no']; ?></td>
                                        <td><?= $nonOfficePersonnel['address']; ?></td>
                                        <!-- <td><span class="badge bg-success">Active</span></td> -->
                                        <td><?= date("M. d, Y h:i A", strtotime($nonOfficePersonnel['tstamp'])) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-primary btn-sm" title="Edit User" onclick="showModal(1, <?= $nonOfficePersonnel['id']; ?>)">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteUser('non-office', <?= $nonOfficePersonnel['id']; ?>)">
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
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="user-registry-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/registry/user-registry/js/user-registry.js"></script>
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