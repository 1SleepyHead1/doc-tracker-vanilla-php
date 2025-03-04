<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}

require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {
    $years = range(2025, date("Y") + 5);

    $getOffices = $c->prepare("SELECT id,office_name FROM offices ORDER BY office_name;");
    $getOffices->execute();
    $offices = $getOffices->fetchAll();

    $getDocTypes = $c->prepare("SELECT id,doc_type_name FROM document_types ORDER BY doc_type_name;");
    $getDocTypes->execute();
    $docTypes = $getDocTypes->fetchAll();
?>
    <style>
        .multi-select-container {
            width: 100%;
        }

        .multi-select-button {
            display: inline-block;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background-color: #fff;
            cursor: pointer;
        }

        .multi-select-container--open .multi-select-menu {
            display: block;
        }

        .multi-select-menu {
            position: relative;
            width: auto !important;
            left: 0;
            top: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: 0.5rem 0;
            font-size: 0.875rem;
            color: #212529;
            text-align: left;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 0.25rem;
        }

        .multi-select-menuitem {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .multi-select-menuitem:hover {
            color: #16181b;
            text-decoration: none;
            background-color: #f8f9fa;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-file-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Document Entries</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Pending Documents</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Rejected Documents</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-3">
                    <div class="card-body text-center">
                        <div class="icon mb-2">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        </div>
                        <h5 class="text-xs font-weight-bold text-uppercase">Released Documents</h5>
                        <h2 class="font-weight-bold text-gray-800 doc-count"></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 mb-4">
            <div class="card shadow h-100 py-3">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title"><i class="fas fa-chart-line me-2"></i>Statistics</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- monthly doc entries -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Monthly Document Entries</div>
                                    <div class="card-tools d-flex align-items-center">
                                        <a class="btn btn-label-primary btn-round btn-sm" title="Print" onclick="previewPrintChart('_monthly-doc-entries')">
                                            <span class="btn-label">
                                                <i class="fa fa-print"></i>
                                            </span>
                                            Print
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-2">
                                        <label for="mde-year" class="form-label">Year</label>
                                        <select name="mde-year" id="mde-year" class="form-select" onchange="loadMonthlyDocEntries()">
                                            <?php for ($x = 0; $x < count($years); $x++) {
                                                if ($years[$x] == date("Y")) {
                                            ?>
                                                    <option value="<?= $years[$x] ?>" selected><?= $years[$x] ?></option>
                                                <?php } else { ?>
                                                    <option value="<?= $years[$x] ?>"><?= $years[$x] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="_monthly-doc-entries"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- doc entries per office -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Document Entries per Office</div>
                                    <div class="card-tools d-flex align-items-center">
                                        <div class="form-group mx-2">

                                        </div>
                                        <a class="btn btn-label-primary btn-round btn-sm" title="Print" onclick="previewPrintChart('_doc-entries-per-office')">
                                            <span class="btn-label">
                                                <i class="fa fa-print"></i>
                                            </span>
                                            Print
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label for="depo-date-from" class="form-label">Date From</label>
                                        <input type="date" id="depo-date-from" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" onchange="loadDocEntriesPerOffice()">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="depo-date-to" class="form-label">Date To</label>
                                        <input type="date" id="depo-date-to" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="loadDocEntriesPerOffice()">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="depo-offices" class="form-label">Office</label>
                                        <select class="form-select form-control form-control-sm" id="depo-offices" name="depo-offices" onchange="loadDocEntriesPerOffice()" multiple>
                                            <?php foreach ($offices as $office) { ?>
                                                <option value="<?= $office['id'] ?>"><?= $office['office_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="_doc-entries-per-office"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- doc enties per doc type -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Document Entries per Type</div>
                                    <div class="card-tools d-flex align-items-center">
                                        <a class="btn btn-label-primary btn-round btn-sm" title="Print" onclick="previewPrintChart('_doc-entries-per-type')">
                                            <span class="btn-label">
                                                <i class="fa fa-print"></i>
                                            </span>
                                            Print
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label for="dept-date-from" class="form-label">Date From</label>
                                        <input type="date" id="dept-date-from" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" onchange="loadDocEntriesPerType()">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="dept-date-to" class="form-label">Date To</label>
                                        <input type="date" id="dept-date-to" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="loadDocEntriesPerType()">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="dept-types" class="form-label">Office</label>
                                        <select class="form-select form-control form-control-sm" id="dept-types" name="dept-types" onchange="loadDocEntriesPerType()" multiple>
                                            <?php foreach ($docTypes as $docType) { ?>
                                                <option value="<?= $docType['id'] ?>"><?= $docType['doc_type_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="_doc-entries-per-type"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- for multi select -->
    <script src="assets/js/plugin/multi-select/jquery.multi-select.min.js"></script>
    <script src="assets/js/plugin/multi-select/jquery.multi-select.js"></script>

    <script src="pages/dashboard/admin-dashboard/js/admin-dashboard.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(() => {
                loadDocCounts();
                // loadMonthlyDocEntries();
                // loadDocEntriesPerType();
            }, 18000);

            $('#depo-offices').multiSelect({
                allText: 'All',
                noneText: "--Select Offices--",
                presets: [{
                    name: 'All',
                    options: $('#depo-offices option').map(function() {
                        return $(this).val();
                    }).get()
                }],
                positionMenuWithin: $('.card-body')
            });

            $('#dept-types').multiSelect({
                allText: 'All',
                noneText: "--Select Document Types--",
                presets: [{
                    name: 'All',
                    options: $('#dept-types option').map(function() {
                        return $(this).val();
                    }).get()
                }],
                positionMenuWithin: $('.card-body')
            });
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