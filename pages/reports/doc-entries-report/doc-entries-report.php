<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../conn.php";
require_once "../../../script/globals.php";

try {

    $years = range(2025, date("Y") + 5);
    $months = array(
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    );

    $getOffices = $c->prepare("SELECT id,office_name FROM offices ORDER BY office_name;");
    $getOffices->execute();
    $offices = $getOffices->fetchAll();
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
        <div class="card">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Document Entries Report</div>
                    <div class="card-tools d-flex align-items-center">
                        <button class="btn btn-label-info btn-round btn-sm" title="Generate Report" onclick="generateReport()">
                            <span class="btn-label">
                                <i class="fas fa-file-export"></i>
                            </span>
                            Generate Report
                        </button>
                        <a class="btn btn-label-primary btn-round btn-sm" title="Print" onclick="previewPrint('_doc-entries-report')">
                            <span class="btn-label">
                                <i class="fa fa-print"></i>
                            </span>
                            Print
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-6">
                    <div class="col-2">
                        <label for="r-type" class="form-label">Duration</label>
                        <select name="type" id="r-type" name="r-type" class="form-select" onchange="typeChange()">
                            <option value="daily">Daily</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="col-2 for-daily">
                        <label for="date" class="form-label">Date</label>
                        <input id="date" name="date" type="date" class="form-control form-control-sm r-filter mx-2" value="<?= date('Y-m-d') ?>" />
                    </div>
                    <div class="col-2 for-monthly" hidden>
                        <label for="month" class="form-label">Month</label>
                        <select name="month" id="month" class="form-select r-filter mx-2">
                            <?php for ($x = 1; $x < 13; $x++) {
                                if ($months[$x] == date("F")) {
                            ?>
                                    <option data-default="1" value="<?= $x ?>" selected><?= $months[$x] ?></option>
                                <?php
                                } else { ?>
                                    <option value="<?= $x ?>"><?= $months[$x] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-2 for-monthly for-yearly" hidden>
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select">
                            <?php for ($x = 0; $x < count($years); $x++) {
                                if ($years[$x] == date("Y")) {
                            ?>
                                    <option data-default="1" value="<?= $years[$x] ?>" selected><?= $years[$x] ?></option>
                                <?php } else { ?>
                                    <option value="<?= $years[$x] ?>"><?= $years[$x] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="offices" class="form-label">Office</label>
                        <select class="form-select form-control form-control-sm" id="offices" name="offices">
                            <option value="">All</option>
                            <?php foreach ($offices as $office) { ?>
                                <option value="<?= $office['id'] ?>"><?= $office['office_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div id="_doc-entries-report" class="mt-4">
                    <!-- generate report here -->
                </div>
            </div>
        </div>
    </div>

    <!-- for multi select -->
    <!-- <script src="assets/js/plugin/multi-select/jquery.multi-select.min.js"></script>
    <script src="assets/js/plugin/multi-select/jquery.multi-select.js"></script> -->

    <script src="pages/reports/doc-entries-report/js/doc-entries-report.js"></script>

    <script>
        $(document).ready(function() {
            // $('#offices').multiSelect({
            //     allText: 'All',
            //     noneText: "--Select Offices--",
            //     presets: [{
            //         name: 'All',
            //         options: $('#offices option').map(function() {
            //             return $(this).val();
            //         }).get()
            //     }],
            //     positionMenuWithin: $('.card-body')
            // });
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