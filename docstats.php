<?php
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['dn'])) {

?>
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        You do not have proper access for this page.
    </div>

    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert i {
            font-size: 1.1em;
            vertical-align: middle;
        }

        .me-2 {
            margin-right: 0.5rem;
        }
    </style>

<?php
    exit;
}
?>

<?php
session_start();
require_once "conn.php";
require_once "script/globals.php";

try {
    $docNo = sanitize($_GET['dn']);

    $getDocument = $c->prepare("
        SELECT
            doc.id,
            doc.doc_type AS doc_type_id,
            doc_type.doc_type_name,
            doc.purpose, 
            doc.status,
            user.name AS submitter,
            user.email,
            user.contact_no,
            MAX(setting.step) AS max_step,
            doc.tstamp
        FROM submitted_documents doc
        LEFT JOIN document_types doc_type ON doc.doc_type = doc_type.id
        LEFT JOIN users user ON doc.user_id = user.id
        LEFT JOIN document_transaction_setting setting ON doc.doc_type = setting.doc_type
        WHERE doc.doc_number = ?
        ORDER BY doc.tstamp DESC;
   ");
    $getDocument->execute([$docNo]);
    $document  = $getDocument->fetch();

    $maxStep = $document['max_step'];
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Document Tracker</title>
        <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
        <link rel="icon" href="assets/img/logo-no-text.png" type="image/x-icon" />
        <script src="assets/js/plugin/webfont/webfont.min.js"></script>
        <script>
            WebFont.load({
                google: {
                    families: ["Public Sans:300,400,500,600,700"]
                },
                custom: {
                    families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                    urls: ["assets/css/fonts.min.css"]
                },
                active: function() {
                    sessionStorage.fonts = true;
                }
            });
        </script>

        <!-- CSS Files -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/css/plugins.min.css" />
        <link rel="stylesheet" href="assets/css/style.min.css" />
    </head>

    <body>
        <div class="d-flex justify-content-center align-items-center min-vh-100">
            <div class="container mt-4">

                <?php if ($getDocument->rowCount() == 0) { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error: The requested page could not be found. Please contact your system administrator for assistance.
                    </div>
                <?php exit;
                } else {
                    $getSettings = $c->prepare("
                        SELECT
                            setting.step,
                            office.office_code,
                            office.office_name,
                            person.name AS person_in_charge
                        FROM document_transaction_setting setting
                        LEFT JOIN offices office ON setting.office = office.id
                        LEFT JOIN users person ON office.person_in_charge = person.id
                        WHERE doc_type = ?
                        ORDER BY setting.step;
                    ");
                    $getSettings->execute([$document['doc_type_id']]);
                    $settings = $getSettings->fetchAll();

                    $getLogs = $c->prepare("
                        SELECT
                            doc_log.status,
                            doc_log.remarks,
                            doc_log.tstamp,
                            office.office_code,
                            office.office_name,
                            person.name AS person_in_charge
                        FROM document_transaction_logs doc_log
                        LEFT JOIN offices office ON doc_log.office = office.id
                        LEFT JOIN users person ON doc_log.updated_by = person.id 
                        WHERE doc_log.doc_number = ?
                            AND doc_log.step = ?
                    ");

                    $getCurrentStep = $c->prepare("SELECT MAX(step)+1 FROM document_transaction_logs WHERE doc_number = ?");
                    $getCurrentStep->execute([$docNo]);
                    $currentStep = $getCurrentStep->fetchColumn();
                ?>

                    <div id="header" class="mb-4" style="display: none;">
                        <?php if (!isset($_SESSION['is_office_personnel'])) { ?>
                            <?php if ($currentStep <= $maxStep) { ?>
                                <?php if ($document['status'] != "Rejected" || $document['status'] != "For Release") { ?>
                                    <a id="btn-login" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#office-personnel-login-modal"><i class="fas fa-user-tie me-2"></i>Log in as Office Personnel</a>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($currentStep <= $maxStep) { ?>
                                <?php if ($document['status'] != "Rejected" || $document['status'] != "For Release") { ?>
                                    <!-- <button id="btn-logout" class="btn btn-primary mr-4 mb-2" onclick="voidUser()"><i class="fas fa-sign-out-alt me-2"></i>Logout</button> -->
                                    <button class="btn btn-success mb-2 btn-actions" id="btn-proceed" onclick="confirmAction(0)"><i class="fas fa-check me-2"></i>Approve</button>
                                    <button class="btn btn-danger mb-2 btn-actions" id="btn-reject" onclick="confirmAction(1)"><i class="fas fa-times me-2"></i>Reject</button>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <div id="content" doc-no="<?= $docNo ?>" doc-type="<?= $document['doc_type_id'] ?>" current-step="<?= $currentStep ?>" max-step="<?= $maxStep ?>" u="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "" ?>" style="display: none;">
                        <div class="row justify-content-center">
                            <!-- Document Details Column -->
                            <div class="col-md-4 position-sticky">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="fw-bold mb-3"><i class="fas fa-file-alt me-2"></i>Document Details</h3>
                                        <h5 class="card-title mb-3 text-center">Document: <?php echo $docNo; ?></h5>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-file-signature me-2"></i>Document Type</label>
                                            <p class="mb-1"><?= $document['doc_type_name'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-info-circle me-2"></i>Purpose</label>
                                            <p class="mb-1"><?= $document['purpose'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-user me-2"></i>Submitter</label>
                                            <p class="mb-1"><?= $document['submitter'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-phone me-2"></i>Contact Number</label>
                                            <p class="mb-1"><?= $document['contact_no'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-envelope me-2"></i>Email</label>
                                            <p class="mb-1"><?= $document['email'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-flag me-2"></i>Status</label>
                                            <p class="mb-1"><?= $document['status'] ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold"><i class="fas fa-calendar-alt me-2"></i>Date Submitted</label>
                                            <p class="mb-1"><?= date("M. d, Y h:i A", strtotime($document['tstamp'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Column -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="fw-bold mb-3"><i class="fas fa-list me-2"></i>Document Logs</h3>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <ul class="timeline">
                                                    <li>
                                                        <div class="timeline-badge success">
                                                            <i class="fas fa-file-alt"></i>
                                                        </div>
                                                        <div class="timeline-panel">
                                                            <div class="timeline-heading">
                                                                <h4 class="timeline-title">Document submitted</h4>
                                                                <p>
                                                                    <small class="text-muted">
                                                                        <i class="far fa-calendar-check me-1"></i>
                                                                        <span class="text-primary fw-medium"><?= date("M. d, Y h:i A", strtotime($document['tstamp'])) ?></span>
                                                                    </small>
                                                                </p>
                                                            </div>
                                                            <div class="timeline-body">
                                                                <span class="badge bg-info">New</span>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <?php foreach ($settings as $index => $setting) {
                                                        $getLogs->execute([$docNo, $setting['step']]);
                                                        $log = $getLogs->fetch();
                                                        $stepColor = "";

                                                        if ($log) {
                                                            if ($log['status'] == "Rejected") {
                                                                $stepColor = "danger";
                                                            } else {
                                                                if ($currentStep == $setting['step']) {
                                                                    $stepColor = "warning";
                                                                } else {
                                                                    $stepColor = "success";
                                                                }
                                                            }
                                                        } else {
                                                            if ($document['status'] != "Rejected") {
                                                                if ($currentStep == $setting['step']) {
                                                                    $stepColor = "warning";
                                                                }
                                                            }
                                                        }

                                                    ?>
                                                        <li id="main-step-<?= $setting['step'] ?>" class="<?= $index % 2 == 0 ? "timeline-inverted" : ""  ?>">
                                                            <div name="step-display" class="timeline-badge <?= $stepColor ?>">
                                                                <?= $setting['step'] ?>
                                                            </div>
                                                            <div class="timeline-panel">
                                                                <div class="timeline-heading">
                                                                    <h4 class="timeline-title"><?= $setting['office_code'] . " - " . $setting['office_name'] ?></h4>
                                                                    <p>
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-user me-1"></i><?= $setting['person_in_charge'] ?>
                                                                            <br>
                                                                            <i name="calendar-display" class="<?= $log ? "far fa-calendar-check" : "far fa-calendar-minus" ?> me-1"></i>
                                                                            <span name="tstamp-display" class="text-primary fw-medium"><?= $log ? date("M. d, Y h:i A", strtotime($log['tstamp'])) : "-" ?></span>
                                                                        </small>
                                                                    </p>
                                                                </div>
                                                                <div class="timeline-body">
                                                                    <p>
                                                                        <?php if ($log) { ?>
                                                                            <span name="status-display" class="badge bg-<?= ($log['status'] == "Forwarded") ? "info" : (($log['status'] == "For Release") ? "success" : "danger") ?>">
                                                                                <?= $log['status'] ?>
                                                                            </span>
                                                                        <?php } else { ?>
                                                                            <span name="status-display" class="">
                                                                            </span>
                                                                        <?php } ?>
                                                                    </p>
                                                                    <p class="mt-2">
                                                                        <i class="fas fa-comment-alt me-1"></i>
                                                                        <span name="remarks-display"><?= $log ? $log['remarks'] : "-" ?></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Loading State -->
                <div id="loading" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading document details...</p>
                </div>

                <!-- Office personnel login Modal -->
                <form id="office-personnel-login-form">
                    <div class="modal fade" id="office-personnel-login-modal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content" id="_modal-details">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">
                                        <span class="fw-mediumbold"><i class="fas fa-sign-in-alt me-1"></i>Log in to your account</span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col col-sm-12">
                                            <div class="form-group">
                                                <label><i class="fas fa-user me-1"></i>Username</label>
                                                <input name="username" type="text" class="form-control" required="">
                                            </div>
                                        </div>

                                        <div class="col col-sm-12">
                                            <div class="form-group">
                                                <label><i class="fas fa-lock me-1"></i>Password</label>
                                                <input name="password" type="password" class="form-control" required="">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input me-2 show-p" type="checkbox" value="" id="show-pass-1" data-target="password">
                                                    <label class="form-check-label" for="show-pass-1">
                                                        <i class="fas fa-eye me-1"></i>Show password
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" id="btn-submit" class="btn btn-sm btn-primary">
                                        Login
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Remarks Modal -->
                <div class="modal fade" id="remarks-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" id="_modal-details">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">
                                    <span class="fw-mediumbold"><i class="fas fa-comment-dots me-1"></i>Remarks</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col col-sm-12">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control form-control-md" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" id="btn-confirm" class="btn btn-sm btn-primary">
                                    Proceed
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!--   Core JS Files   -->
                <script src="assets/js/core/jquery-3.7.1.min.js"></script>
                <script src="assets/js/core/popper.min.js"></script>
                <script src="assets/js/core/bootstrap.min.js"></script>
                <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
                <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>
                <script src="assets/js/main.js"></script>

                <script>
                    "use strict";

                    window.addEventListener('load', function() {
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('header').style.display = 'block';
                        document.getElementById('content').style.display = 'block';
                    });

                    $(document).ready(function() {
                        $(".show-p").click(function() {
                            const e = $(this).data("target");

                            if ($(this).is(":checked")) {
                                $(`input[name="${e}"]`).attr("type", "text");
                            } else {
                                $(`input[name="${e}"]`).attr("type", "password");
                            }
                        });

                        $("#office-personnel-login-form").submit(function(e) {
                            e.preventDefault();

                            $.post(`script/docstats/verify-login.php`, {
                                username: $(`input[name="username"]`).val(),
                                password: $(`input[name="password"]`).val()
                            }, function(data) {
                                const response = JSON.parse(data);
                                if (response.status) {
                                    const header = $("#header");
                                    const form = $("#office-personnel-login-form");
                                    const btnLogin = $("#btn-login");
                                    $("#content").attr("u", response.u);
                                    form[0].reset();

                                    if (btnLogin) {
                                        btnLogin.remove();
                                    }

                                    header.append(`
                                        <button id="btn-logout" class="btn btn-primary mr-4 mb-2" onclick="voidUser()"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                        <button class="btn btn-success mb-2 btn-actions" id="btn-proceed" onclick="confirmAction(0)"><i class="fas fa-check me-2"></i>Approve</button>
                                        <button class="btn btn-danger mb-2 btn-actions"id="btn-reject" onclick="confirmAction(1)"><i class="fas fa-times me-2"></i>Reject</button>
                                    `);

                                    toggleModal("office-personnel-login-modal", 1);
                                } else {
                                    showAlert(response.message, "danger");
                                }
                            }).fail(function(jqXHR, textStatus, errorThrown) {
                                const errorMessages = {
                                    500: "Internal Server Error (500) occurred.",
                                    404: "Resource not found (404) error.",
                                    403: "Forbidden (403) error.",
                                    401: "Unauthorized (401) error.",
                                    400: "Bad Request (400) error."
                                };
                                console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
                            });
                        });

                        $("#remarks-modal").on("hidden.bs.modal", function(e) {
                            $("#btn-confirm").attr("onclick", "");
                            $("#remarks").val("");
                        });
                    });

                    function voidUser() {
                        const header = $("#header");
                        $("#content").attr("u", "");
                        header.html(``);
                        header.append(`<a id="btn-login" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#office-personnel-login-modal"><i class="fas fa-user-tie me-2"></i>Log in as Office Personnel</a>`);
                    }

                    function confirmAction(a) {
                        swal({
                            title: "Notice!",
                            text: a === 0 ? "Approve document?" : "Reject Document?",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    visible: true,
                                    className: "btn btn-danger"
                                },
                                confirm: {
                                    text: "Proceed",
                                    className: "btn btn-success"
                                }
                            }
                        }).then((confirmed) => {
                            if (confirmed) {
                                const btn = $("#btn-confirm");
                                a === 0 ? btn.attr("onclick", "proceedDoc(0)") : btn.attr("onclick", "proceedDoc(1)");
                                toggleModal("remarks-modal");
                            }
                        });
                    }

                    function proceedDoc(a) {
                        const api = a === 0 ? "approve.php" : "reject.php";
                        const currentStep = $("#content").attr("current-step");

                        $.post(
                            `script/docstats/${api}`, {
                                u: $("#content").attr("u"),
                                docNo: $("#content").attr("doc-no"),
                                docType: $("#content").attr("doc-type"),
                                remarks: $("#remarks").val(),
                                currentStep: currentStep
                            },
                            function(data) {
                                const response = JSON.parse(data);
                                if (response.status) {
                                    const nextStep = parseInt(currentStep) + 1;
                                    const currentElement = $(`#main-step-${currentStep}`);
                                    const nextElement = $(`#main-step-${nextStep}`);

                                    $("#content").attr("current-step", nextStep);
                                    currentElement.find(`div[name="step-display"]`).removeClass("warning").addClass("success");
                                    currentElement.find(`i[name="calendar-display"]`).removeClass("fa-calendar-minus").addClass("fa-calendar-check");
                                    currentElement.find(`span[name="tstamp-display"]`).html(formatDateTime(response.tstamp));
                                    currentElement.find(`span[name="status-display"]`).addClass(`badge bg-${response.status === "Forwarded" ?  "info" : response.status === "For Release" ? "success" : "danger"}`).html(response.status);
                                    currentElement.find(`span[name="remarks-display"]`).html(response.remarks);

                                    nextElement.find(`div[name="step-display"]`).addClass("warning");

                                    if (response.status === "Rejected" || response.status === "For Release") {
                                        $("#header").html("")
                                    }
                                    showAlert("Document has been approved.");
                                    toggleModal("remarks-modal", 1);
                                } else {
                                    showAlert(response.message, "danger");
                                }
                            }
                        ).fail(function(jqXHR, textStatus, errorThrown) {
                            const errorMessages = {
                                500: "Internal Server Error (500) occurred.",
                                404: "Resource not found (404) error.",
                                403: "Forbidden (403) error.",
                                401: "Unauthorized (401) error.",
                                400: "Bad Request (400) error."
                            };
                            console.error(errorMessages[jqXHR.status] || `Unexpected Error: ${textStatus}, ${errorThrown}`);
                            resolve(false);
                        });
                    }
                </script>
            </div>
        </div>
    </body>

    </html>

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