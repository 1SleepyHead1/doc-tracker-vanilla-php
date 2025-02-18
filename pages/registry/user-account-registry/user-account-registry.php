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

    $getUserAccounts = $c->prepare("
        SELECT
            acc.id,
            u.name,
            u.is_office_personnel,
            acc.username,
            acc.tstamp
        FROM user_accounts acc
        JOIN users u ON acc.user_id=u.id
        WHERE acc.id<>1
        ORDER BY acc.tstamp DESC;
    ");
    $getUserAccounts->execute();
    $userAccounts = $getUserAccounts->fetchAll();

    $getUsersWithoutAccounts = $c->prepare("
        SELECT
            id,
            is_office_personnel,
            CONCAT(LOWER(REPLACE(SUBSTRING_INDEX(first_name, ' ', 2), ' ', '_')),generate_number(5)) AS first_name,
            CONCAT(LOWER(REPLACE(SUBSTRING_INDEX(last_name, ' ', 2), ' ', '_')),generate_number(5)) AS last_name,
            name
        FROM users
        WHERE id NOT IN (SELECT user_id FROM user_accounts)
        ORDER BY name;
    ");
    $getUsersWithoutAccounts->execute();
    $usersWithoutAccounts = $getUsersWithoutAccounts->fetchAll();
?>
    <datalist id="user-list">
        <?php foreach ($usersWithoutAccounts as $user) { ?>
            <option data-gen-uname="<?= $user['first_name'] ?>" data-gen-pass="<?= $user['last_name'] ?>" data-category="<?= $user['is_office_personnel'] ?>" data-id="<?= $user['id'] ?>" value="<?= $user['name'] ?>"><?= $user['name'] ?></option>
        <?php } ?>
    </datalist>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fas fa-user-lock me-2"></i>User Account Registry</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" onclick="showModal(0)">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add Account
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 999px;">
                    <table id="tbl-user-accounts" class="display table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Category</th>
                                <th>Username</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userAccounts as $userAccount) { ?>
                                <tr id="tr-user-account-<?= $userAccount['id'] ?>">
                                    <td><?= $userAccount['name'] ?></td>
                                    <td><?= $userAccount['is_office_personnel'] == 1 ? "Office Personnel" : "Non-Office Personnel" ?></td>
                                    <td><?= $userAccount['username'] ?></td>
                                    <td><?= date("M. d, Y h:i A", strtotime($userAccount['tstamp'])) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="New Password" onclick="showModal(1,<?= $userAccount['id'] ?>)">
                                                <i class="fas fa-key me-1"></i>New Password
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete Account" onclick="deleteAccount(<?= $userAccount['id'] ?>)">
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

    <!-- Account Modal -->
    <div class="modal fade" id="user-account-registry-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div id="_modal-content" class="modal-content modal-reset">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/registry/user-account-registry/js/user-account-registry.js"></script>
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