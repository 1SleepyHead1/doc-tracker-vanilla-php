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
            <i class="fas fa-user-plus me-2"></i>
            Add New User
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <form id="user-registry-form">
            <div class="row g-2">
                <!-- Personal Information -->
                <div class="col-md-12 mb-2">
                    <h6 class="fw-bold">Personal Information</h6>
                </div>
                <div class="col-md-3">
                    <label for="first-name" class="form-label small">First Name</label>
                    <input type="text" class="form-control form-control-sm" id="first-name" name="first-name" required>
                </div>
                <div class="col-md-3">
                    <label for="middle-name" class="form-label small">Middle Name</label>
                    <input type="text" class="form-control form-control-sm" id="middle-name" name="middle-name">
                </div>
                <div class="col-md-3">
                    <label for="last-name" class="form-label small">Last Name</label>
                    <input type="text" class="form-control form-control-sm" id="last-name" name="last-name" required>
                </div>
                <div class="col-md-3">
                    <label for="ext-name" class="form-label small">Extension</label>
                    <input type="text" class="form-control form-control-sm" id="ext-name" name="ext-name">
                </div>
                <div class="col-md-3">
                    <label for="user-type" class="form-label small">Type</label>
                    <select class="form-select form-select-sm" id="user-type" name="user-type" required>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                        <option value="staff">Staff</option>
                        <option value="outside-client">Outside Client</option>
                    </select>
                </div>

                <!-- Contact & Address Information -->
                <div class="col-md-12 mt-3 mb-2">
                    <h6 class="fw-bold">Contact & Address Information</h6>
                </div>
                <div class="col-md-3">
                    <label for="email" class="form-label small">Email</label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" required>
                </div>
                <div class="col-md-3">
                    <label for="contact-no" class="form-label small">Contact No.</label>
                    <input type="tel" class="form-control form-control-sm" id="contact-no" name="contact-no" required>
                </div>
                <div class="col-md-3">
                    <label for="province" class="form-label small">Province</label>
                    <select class="form-select form-select-sm" id="province" name="province" onchange="loadCities()" required>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="city" class="form-label small">City/Municipality</label>
                    <select class="form-select form-select-sm" id="city" name="city" onchange="loadBarangays()" required>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="barangay" class="form-label small">Barangay</label>
                    <select class="form-select form-select-sm" id="barangay" name="barangay" required>
                    </select>
                </div>
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
    </div>

    <script>
        var donorModalLoaded = false;
        $(function() {
            const element = $('#province');
            const url = 'assets/json/ph-json/province.json';

            $.getJSON(url, function(data) {
                var result = data.filter(function(value) {
                    return value.region_code == 06;
                });

                result.sort(function(a, b) {
                    return a.province_name.localeCompare(b.province_name);
                });

                $.each(result, function(key, entry) {
                    element.append(`<option data-code="${entry.province_code}" value="${entry.province_name}">${entry.province_name}</option>`);
                })

                <?php if ($action == 1) { ?>
                    if (!donorModalLoaded) {
                        element.val("<?php echo $d['province'] ?>");
                    }
                <?php } ?>

                loadCities();
            });
        });

        function loadCities() {
            const element = $('#city');
            const province_code = $("#province option:selected").data("code");
            const url = 'assets/json/ph-json/city.json';

            $.getJSON(url, function(data) {
                var result = data.filter(function(value) {
                    return value.province_code == province_code;
                });

                result.sort(function(a, b) {
                    return a.city_name.localeCompare(b.city_name);
                });

                $("#city").html("");

                $.each(result, function(key, entry) {
                    element.append(`<option data-code="${entry.city_code}" value="${entry.city_name}">${entry.city_name}</option>`);
                })

                <?php if ($action == 1) { ?>
                    if (!donorModalLoaded) {
                        element.val("<?php echo $d['city'] ?>");
                    }
                <?php } ?>

                loadBarangays();
            });
        }

        function loadBarangays() {
            const element = $('#barangay');
            const city_code = $("#city option:selected").data("code");
            const url = 'assets/json/ph-json/barangay.json';

            $.getJSON(url, function(data) {
                var result = data.filter(function(value) {
                    return value.city_code == city_code;
                });

                result.sort(function(a, b) {
                    return a.brgy_name.localeCompare(b.brgy_name);
                });

                $("#barangay").html("");

                $.each(result, function(key, entry) {
                    element.append(`<option data-code="${entry.brgy_code}" value="${entry.brgy_name}">${entry.brgy_name}</option>`);
                })

                <?php if ($action == 1) { ?>
                    if (!donorModalLoaded) {
                        element.val("<?php echo $d['barangay'] ?>");
                    }
                <?php } ?>
            });
        }

        document.getElementById('contact-no').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $("#user-registry-form").submit(function(e) {
            e.preventDefault();
            <?php echo $action == 0 ? "insertUpdateUser(0);" : "updateUser(0,$id);" ?>
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