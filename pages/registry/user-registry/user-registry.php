<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {

    $getOfficePersonnels = $conn->prepare("
        SELECT 
        FROM users
    ");
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
                    <a class="nav-link user-category" data-category="non-office" data-bs-toggle="tab" href="#non-office-personnel">Non-Office Personnel</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Office Personnel Tab -->
                <div id="office-personnel" class="tab-pane active">
                    <div class="table-responsive mt-3">
                        <table id="tbl-office-personnel" class="display table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <!-- <th>Status</th> -->
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Smith</td>
                                    <td>john.smith@email.com</td>
                                    <td>+1234567890</td>
                                    <td>123 Main St, City</td>
                                    <!-- <td><span class="badge bg-success">Active</span></td> -->
                                    <td>Jan. 15, 2024</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit User">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete User">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>John Smith</td>
                                    <td>john.smith@email.com</td>
                                    <td>+1234567890</td>
                                    <td>123 Main St, City</td>
                                    <!-- <td><span class="badge bg-success">Active</span></td> -->
                                    <td>Feb. 15, 2025</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit User">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete User">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>John Smith</td>
                                    <td>john.smith@email.com</td>
                                    <td>+1234567890</td>
                                    <td>123 Main St, City</td>
                                    <!-- <td><span class="badge bg-success">Active</span></td> -->
                                    <td>Apr. 15, 2026</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit User">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete User">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Non-Office Personnel Tab -->
                <div id="non-office-personnel" class="tab-pane fade">
                    <div class="table-responsive mt-3">
                        <table id="tbl-non-office-personnel" class="display table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Contact No.</th>
                                    <th>Address</th>
                                    <!-- <th>Status</th> -->
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jane Doe</td>
                                    <td>jane.doe@email.com</td>
                                    <td>+0987654321</td>
                                    <td>456 Oak Ave, Town</td>
                                    <!-- <td><span class="badge bg-success">Active</span></td> -->
                                    <td>Jan. 10, 2024</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" title="Edit User">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Delete User">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
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
            <div id="_modal-content" class="modal-content">
                <!-- show modal content here -->
            </div>
        </div>
    </div>

    <script src="pages/registry/user-registry/scripts/user-registry.js"></script>
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