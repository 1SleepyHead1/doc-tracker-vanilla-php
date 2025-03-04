<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../conn.php";
require_once "../../script/globals.php";

try {
    $id = sanitize($_POST['id']);
?>
    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-key me-2"></i>
            Update Password
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="update-user-profile-form">
            <div class="row g-2">
                <div class="col-md-12">
                    <label for="new-password" class="form-label small">Current Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-md" id="current-password" name="current-password" required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('current-password')">
                            <i class="fas fa-eye" id="current-password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-12">
                    <label for="new-password" class="form-label small">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-md" id="u-new-password" name="new-password" minlength="6" maxlength="15" required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('u-new-password')">
                            <i class="fas fa-eye" id="u-new-password-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="confirm-password" class="form-label small">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-md" id="u-confirm-password" minlength="6" maxlength="15" name="confirm-password" required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePassword('u-confirm-password')">
                            <i class="fas fa-eye" id="u-confirm-password-icon"></i>
                        </button>
                    </div>
                    <div id="u-confirm-password-error" class="small text-danger mt-1"></div>
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
            $("#update-user-profile-form").submit(function(e) {
                e.preventDefault();
            });


            $("#u-confirm-password").on("input", function() {
                const password = $("#u-new-password").val();
                const confirmPassword = $(this).val();

                // Remove any existing error messages
                $("#u-confirm-password-error").html("");

                if (confirmPassword !== password) {
                    $("#u-confirm-password-error").html("Passwords do not match");
                }
            });

            $("#u-new-password").on("input", function() {
                const password = $(this).val();
                const confirmPassword = $("#u-confirm-password").val();

                // Remove any existing error messages

                $("#u-confirm-password-error").html("");

                if (confirmPassword && confirmPassword !== password) {
                    $("#u-confirm-password-error").html("Passwords do not match");
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