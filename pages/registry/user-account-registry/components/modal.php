<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $action = $_POST['action'];
    $id = $_POST['id'];
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-key me-2"></i>
            <?= $action == 0 ? "Add New User Account" : "New password" ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="user-account-registry-form">
            <div class="row g-2">

                <?php if ($action == 0) { ?>
                    <div class="col-md-12">
                        <label for="user" class="form-label small">User</label>
                        <input type="text" class="form-control form-control-md" id="user" name="user" list="user-list" data-list-type="user" onchange="validateDataListOptions('user','','user-list');appendGenerated();" required>

                    </div>
                    <div class="col-md-12">
                        <label for="username" class="form-label small">Username</label>
                        <input type="text" class="form-control form-control-md" id="username" name="username" required>
                    </div>
                <?php } ?>

                <div class="col-md-12">
                    <label for="new-password" class="form-label small"><?php $action == 0 ? "" : "New" ?> Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-md" id="new-password" name="new-password" required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('new-password')">
                            <i class="fas fa-eye" id="new-password-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="confirm-password" class="form-label small">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-md" id="confirm-password" name="confirm-password" required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('confirm-password')">
                            <i class="fas fa-eye" id="confirm-password-icon"></i>
                        </button>
                    </div>
                    <div id="confirm-password-error" class="small text-danger mt-1"></div>
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
            $("#user-account-registry-form").submit(function(e) {
                e.preventDefault();
                <?= $action == 0 ? "insertUpdateUserAccount(0);" : "insertUpdateUserAccount(1,$id);" ?>
            });


            $("#confirm-password").on("input", function() {
                const password = $("#new-password").val();
                const confirmPassword = $(this).val();

                // Remove any existing error messages
                $("#confirm-password-error").html("");

                if (confirmPassword !== password) {
                    $("#confirm-password-error").html("Passwords do not match");
                }
            });

            $("#new-password").on("input", function() {
                const password = $(this).val();
                const confirmPassword = $("#confirm-password").val();

                // Remove any existing error messages

                $("#confirm-password-error").html("");

                if (confirmPassword && confirmPassword !== password) {
                    $("#confirm-password-error").html("Passwords do not match");
                }
            });

            $(`input[type="text"], input[type="password"]`).on("input", function(e) {
                const val = $(this).val();

                if (/\s/.test(val)) {
                    showAlert("Please don't include whitespaces.", "danger");
                    $(this).val(val.replace(/\s+/g, ''));
                }
            })
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
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