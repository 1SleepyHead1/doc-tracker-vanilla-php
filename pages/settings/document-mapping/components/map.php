<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../../../conn.php";
require_once "../../../../script/globals.php";

try {
    $id = $_POST['id'];

    $getSettings = $c->prepare("
        SELECT
            office.office_code,
            office.office_name,
            user.name,
            setting.step
        FROM document_transaction_setting setting
        LEFT JOIN document_types doc ON setting.doc_type = doc.id
        LEFT JOIN offices office ON setting.office = office.id
        LEFT JOIN users user ON office.person_in_charge = user.id
        WHERE setting.doc_type = ?
        ORDER BY setting.step;
    ");
    $getSettings->execute([$id]);
    $settings = $getSettings->fetchAll();
?>
    <?php if (count($settings) == 0) { ?>
        <div id="no-mapping" class="alert alert-warning text-start">
            <strong>
                <i class="fas fa-exclamation-triangle"></i>
                No settings available for this document type.
            </strong>
            <br>
            <a class="btn btn-sm btn-primary mt-3" onclick="showModal(0)"><i class="fas fa-plus"></i> Add New Setting</a>
        </div>
    <?php } else { ?>
        <div class="d-flex justify-content-center mb-3 gap-2">
            <button class="btn btn-md btn-primary" title="Update" onclick="showModal(1, <?= $id ?>)">
                <i class="fas fa-edit"></i> Update
            </button>
            <button class="btn btn-md btn-danger" title="Delete" onclick="deleteSetting(<?= $id ?>)">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
        <ul class="timeline expanded-timeline">
            <?php foreach ($settings as $index => $setting) {

            ?>
                <li class="<?= $index % 2 == 0 ? "" : "timeline-inverted"  ?>">
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h4 class="timeline-title">
                                <!-- <i class="fas fa-map-pin"></i> -->
                                <?= $setting['office_code'] ?> - <?= $setting['office_name'] ?>
                            </h4>
                        </div>
                        <div class="timeline-body">
                            <i class="fas fa-user me-1"></i>
                            <?= is_null($setting['name']) ? "-" : $setting['name'] ?>
                        </div>
                    </div>
                    <div class="timeline-badge"><?= $setting['step'] ?></div>
                </li>

            <?php } ?>
        </ul>
    <?php } ?>




    <!--<ul class="timeline expanded-timeline">
        <li>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h4 class="timeline-title"><i class="fas fa-building"></i> Office A</h4>
                </div>
                <div class="timeline-body">
                    <p>John Doe</p>
                </div>
            </div>
            <div class="timeline-badge primary">1</div>
        </li>
        <li>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h4 class="timeline-title"><i class="fas fa-building"></i> Office B</h4>
                </div>
                <div class="timeline-body">
                    <p>John Doe</p>
                </div>
            </div>
            <div class="timeline-badge primary">2</div>
        </li>
        <li>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h4 class="timeline-title"><i class="fas fa-building"></i> Office C</h4>
                </div>
                <div class="timeline-body">
                    <p>John Doe</p>
                </div>
            </div>
            <div class="timeline-badge primary">3</div>
        </li>
    </ul> -->
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