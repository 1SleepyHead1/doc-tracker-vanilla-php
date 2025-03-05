<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    die('Oops!');
}
require_once "../../conn.php";
require_once "../../script/globals.php";

try {
?>

    <div class="modal-header no-bd">
        <h5 class="modal-title">
            <i class="fas fa-qrcode me-2"></i>
            Scan QR Code
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <div id="video-container">
            <video id="qr-video" style="width: 100%; height: auto;" autoplay></video>
        </div>
        <div class="text-center mt-3">
            <a id="qr-result" class="mt-2 btn-link fs-4 cursor-pointer" target="_blank"></a>
        </div>
    </div>

    <div class="modal-footer mt-3">
        <button id="start-scan" class="btn btn-primary btn-sm" hidden>Scan Again</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>

    <!-- qr scanner -->
    <script src="assets/js/plugin/qr-scanner/qr-scanner.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            const video = document.getElementById('qr-video');
            const videoContainer = document.getElementById("video-container");
            const camQrResult = document.getElementById("qr-result");
            const scanAgainButton = document.getElementById("start-scan");

            function setResult(label, result) {
                scanner.stop();
                label.setAttribute("href", result.data);
                label.textContent = result.data;
                videoContainer.style.display = "none";
                scanAgainButton.hidden = false;
            }

            scanAgainButton.addEventListener("click", () => {
                camQrResult.removeAttribute("href");
                camQrResult.classList.remove("text-primary");
                camQrResult.innerHTML = "";
                videoContainer.style.display = "block";
                scanner.start();
            });

            var scanner = new QrScanner(video, (result) => setResult(camQrResult, result), {
                onDecodeError: (error) => {
                    camQrResult.textContent = error;
                    camQrResult.classList.add("text-primary");
                },
                highlightScanRegion: true,
                highlightCodeOutline: true
            });

            // Access the camera
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then((stream) => {
                    video.srcObject = stream;
                    video.play();
                })
                .catch((err) => {
                    console.error("Error accessing the camera: ", err);
                    alert("Unable to access the camera. Please check your permissions.");
                });

            // for debugging
            window.scanner = scanner;
            scanner.start();
        });
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