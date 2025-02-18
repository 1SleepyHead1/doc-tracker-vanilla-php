<?php
session_start();
if (empty($_SESSION['user_id'])) {
	session_destroy();
	header('location: login.php');
} else {
	// if ($_SESSION['is_admin'] == 0) {
	// 	header('location: index-u.php');
	// }
}

require_once "conn.php";

// $q = $c->prepare("
//     SELECT
//         	b.name AS user_name,
// 			b.email,
// 			b.first_name,
// 			a.username
//     FROM user_accounts a
//     LEFT JOIN users b ON a.user_id = b.id
//     WHERE a.is_admin = 1 AND a.id = ?
// ");
$q = $c->prepare("
    SELECT
        	b.name AS user_name,
			b.email,
			b.first_name,
			a.username
    FROM user_accounts a
    LEFT JOIN users b ON a.user_id = b.id
    WHERE a.id = ?
");
$q->execute([$_SESSION['user_id']]);
$userInfo = $q->fetch();
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Document Tracker</title>
	<meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
	<link rel="icon" href="" type="image/x-icon" />
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
	<div class="wrapper">
		<!-- Sidebar -->
		<div class="sidebar" data-background-color="white">
			<div class="sidebar-logo">
				<!-- Logo Header -->
				<div class="logo-header" data-background-color="white">
					<a class="logo">
						<!-- <img src="" alt="logo here" class="navbar-brand" height="32" /> -->
					</a>
					<div class="nav-toggle">
						<button class="btn btn-toggle toggle-sidebar">
							<i class="gg-menu-right"></i>
						</button>
						<button class="btn btn-toggle sidenav-toggler">
							<i class="gg-menu-left"></i>
						</button>
					</div>
					<button class="topbar-toggler more">
						<i class="gg-more-vertical-alt"></i>
					</button>
				</div>
				<!-- End Logo Header -->
			</div>

			<!-- navs -->
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<ul class="nav nav-secondary">
						<!-- dashboard -->
						<li id="parent-dashboard" class="nav-item active">
							<a data-bs-toggle="collapse" href="#dashboard">
								<i class="fas fa-home"></i>
								<p>Dashboard</p>
								<span class="caret"></span>
							</a>
							<div class="collapse show" id="dashboard">
								<ul class="nav nav-collapse">
									<li class="sub-menu cursor-pointer active">
										<a id="default-menu" data-parent="dashboard" data-url="pages/dashboard/admin-d/admin-d.php" onclick="loadMenu('views/dashboard/admin-d/admin-d.php','default')">
											<span class="sub-item">Home Panel</span>
										</a>
									</li>
									<li class="sub-menu cursor-pointer">
										<a id="statistics-menu" data-parent="dashboard" data-url="pages/dashboard/statistics/statistics.php" onclick="loadMenu('views/dashboard/statistics/statistics.php','statistics')">
											<span class="sub-item">Statistics</span>
										</a>
									</li>
								</ul>
							</div>
						</li>

						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
						</li>

						<!-- registry -->
						<li id="parent-registry" class="nav-item">
							<a data-bs-toggle="collapse" href="#registry">
								<i class="fas fa-book"></i>
								<p>Registry</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="registry">
								<ul class="nav nav-collapse">
									<li class="sub-menu cursor-pointer">
										<a id="user-registry-menu" data-parent="registry" data-url="pages/registry/user-registry/user-registry.php" onclick="loadMenu('pages/registry/user-registry/user-registry.php','user-registry')">
											<span class="sub-item">User Registry</span>
										</a>

									</li>
									<li class="sub-menu cursor-pointer">
										<a id="user-account-registry-menu" data-parent="registry" data-url="pages/registry/user-account-registry/user-account-registry.php" onclick="loadMenu('pages/registry/user-account-registry/user-account-registry.php','user-account-registry')">
											<span class="sub-item">User Account Registry</span>
										</a>
									</li>
									<li class="sub-menu cursor-pointer">
										<a id="office-registry-menu" data-parent="registry" data-url="pages/registry/office-registry/office-registry.php" onclick="loadMenu('pages/registry/office-registry/office-registry.php','office-registry')">
											<span class="sub-item">Office Registry</span>
										</a>
									</li>
									<li class="sub-menu cursor-pointer">
										<a id="document-type-registry-menu" data-parent="registry" data-url="pages/registry/document-type-registry/document-type-registry.php" onclick="loadMenu('pages/registry/document-type-registry/document-type-registry.php','document-type-registry')">
											<span class="sub-item">Document Type Registry</span>
										</a>
									</li>
								</ul>
							</div>
						</li>

						<!-- settings -->
						<li id="parent-settings" class="nav-item">
							<a data-bs-toggle="collapse" href="#settings">
								<i class="fas fa-cog"></i>
								<p>Settings</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="settings">
								<ul class="nav nav-collapse">
									<li class="sub-menu cursor-pointer">
										<a id="document-mapping-menu" data-parent="settings" data-url="pages/settings/document-mapping/document-mapping.php" onclick="loadMenu('pages/settings/document-mapping/document-mapping.php','document-mapping')">
											<span class="sub-item">Document Transaction Setting</span>
										</a>
									</li>
									<li class="sub-menu cursor-pointer">
										<a id="sms-notifications-menu" data-parent="settings" data-url="pages/settings/sms-notifications/sms-notifications.php" onclick="loadMenu('pages/settings/sms-notifications/sms-notifications.php','sms-notifications')">
											<span class="sub-item">SMS Notifications Setting</span>
										</a>
									</li>
								</ul>
							</div>
						</li>

						<!-- transactions -->
						<li id="parent-transactions" class="nav-item">
							<a data-bs-toggle="collapse" href="#transactions">
								<i class="fas fa-clipboard-list"></i>
								<p>Transactions</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="transactions">
								<ul class="nav nav-collapse">
									<li class="sub-menu cursor-pointer">
										<a id="inventory-menu" data-parent="transactions" data-url="pages/transactions/inventory/inventory.php" onclick="loadMenu('pages/transactions/inventory/inventory.php','inventory')">
											<span class="sub-item">Inventory</span>
										</a>
									</li>
								</ul>
							</div>
						</li>

						<!-- reports -->
						<li id="parent-reports" class="nav-item">
							<a data-bs-toggle="collapse" href="#reports">
								<i class="fas fa-copy"></i>
								<p>Reports</p>
								<span class="caret"></span>
							</a>
							<div class="collapse" id="reports">
								<ul class="nav nav-collapse">
									<li class="sub-menu cursor-pointer">
										<a id="cash-d-report-menu" data-parent="reports" data-url="pages/reports/cash-d-report/cash-d-report.php" onclick="loadMenu('views/reports/cash-d-report/cash-d-report.php','cash-d-report')">
											<span class="sub-item">Cash Donation Report</span>
										</a>
									</li>

									<li class="sub-menu cursor-pointer">
										<a id="inkind-d-report-menu" data-parent="reports" data-url="pages/reports/inkind-d-report/inkind-d-report.php" onclick="loadMenu('views/reports/inkind-d-report/inkind-d-report.php','inkind-d-report')">
											<span class="sub-item">In-kind Donation Report</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<!-- end nav here -->
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="main-header">
				<div class="main-header-logo">
					<!-- Logo Header -->
					<div class="logo-header" data-background-color="dark2">
						<a class="logo">
							<!-- <img src="assets/img/logo-main.png" alt="logo here" class="navbar-brand" height="32" /> -->
						</a>
						<div class="nav-toggle">
							<button class="btn btn-toggle toggle-sidebar">
								<i class="gg-menu-right"></i>
							</button>
							<button class="btn btn-toggle sidenav-toggler">
								<i class="gg-menu-left"></i>
							</button>
						</div>
						<button class="topbar-toggler more">
							<i class="gg-more-vertical-alt"></i>
						</button>
					</div>
					<!-- End Logo Header -->
				</div>
				<!-- Navbar Header -->
				<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
					<div class="container-fluid">
						<ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
							<li class="topbar-icon dropdown hidden-caret">
								<a class="nav-link dropdown-toggle" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<div class="d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
										<i class="fa fa-bell"></i>
										<span id="_notif-count" class="notification bg-danger">
											99+
										</span>
									</div>
								</a>
								<div id="_notifs" class="dropdown-menu dropdown-menu-end navbar-dropdown bg-white border shadow rounded-3" style="width: 360px">
									<div class="dropdown-header px-3 py-2">
										<div class="d-flex justify-content-between align-items-center">
											<h6 class="mb-0 fw-bold">Notifications</h6>
											<button id="btn-read-notifs" class="btn btn-link text-primary p-0 fw-semibold" style="font-size: 13px" onclick="readAdminNotif('1')">
												<i class="fa fa-check-double me-1"></i>Mark all as read
											</button>
										</div>
									</div>
									<div class="dropdown-body" style="max-height: 400px; overflow-y: auto;">
										<div id="notifications-empty" class="text-center py-4 d-none">
											<div class="text-muted">
												<i class="fa fa-bell-slash fa-2x mb-2"></i>
												<p class="mb-0">No new notifications</p>
											</div>
										</div>
										<div id="_tbl-notifs">
											<!-- Notifications will be dynamically inserted here -->
										</div>
									</div>
									<div class="dropdown-footer border-top p-2 text-center">
										<a href="#" class="text-primary text-decoration-none fw-semibold" style="font-size: 14px">
											See All Notifications
										</a>
									</div>
								</div>
							</li>

							<li class="topbar-user dropdown hidden-caret ms-2">
								<a class="dropdown-toggle d-flex align-items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
									<div class="avatar-sm me-2">
										<img src="assets/img/user-default.png" alt="Profile" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;" />
									</div>
									<div class="d-none d-sm-block">
										<span class="fw-semibold text-dark"><?= $userInfo['username'] ?></span>
									</div>
								</a>
								<ul class="dropdown-menu dropdown-menu-end animated fadeIn shadow border-0 rounded-3" style="width: 300px">
									<li class="p-3">
										<div class="d-flex align-items-center mb-3">
											<div class="avatar-lg me-3">
												<img src="assets/img/user-default.png" alt="Profile" class="rounded-circle border" style="width: 60px; height: 60px; object-fit: cover;" />
											</div>
											<div>
												<h6 class="mb-1"><?= $userInfo['user_name'] ?></h6>
												<p class="text-muted small mb-2"><?= $userInfo['email'] ?></p>
												<button class="btn btn-sm btn-light cursor-pointer" onclick="showUserProfileModalContent('<?= $_SESSION['user_id'] ?>')">
													<i class="fas fa-key me-1"></i>Change Password
												</button>
											</div>
										</div>
										<div class="list-group list-group-flush border-top pt-2">
											<!-- <a class="list-group-item list-group-item-action cursor-pointer" onclick="dbBackup()">
												<i class="fas fa-database me-2"></i>Backup Database
											</a>
											<a id="db-backup" href="sql/backup/rdms_backup.sql cursor-pointer" download="rdms_backup.sql" hidden></a> -->
											<a class="list-group-item list-group-item-action text-danger cursor-pointer" onclick="logout()">
												<i class="fas fa-sign-out-alt me-2"></i>Logout
											</a>
										</div>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</nav>
				<!-- End Navbar -->
			</div>

			<div class="container">
				<div class="page-inner">
					<!-- container header -->
					<div class="page-header">
						<ul class="breadcrumbs d-flex align-items-center mb-3">
							<li class="d-flex align-items-center">
								<h3 id="ph-title" class="fw-bold mb-0 me-3"></h3>
							</li>
							<li class="d-flex align-items-center">
								<a class="d-flex align-items-center">
									<i id="ph-icon" class="me-3"></i>
								</a>
							</li>
							<li class="separator d-flex align-items-center">
								<i class="fas fa-chevron-right mx-2"></i>
							</li>
							<li class="nav-item d-flex align-items-center">
								<a id="ph-sub-menu" class="text-muted"></a>
							</li>
						</ul>
					</div>
					<!-- end -->

					<!-- container content -->
					<div id="_container" class="row">
						<!-- show contents here -->
					</div>
					<!-- end -->
				</div>
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<div class="copyright">
						<div class="text-center">
							© <script>
								document.write(new Date().getFullYear())
							</script> All Rights Reserved
						</div>
					</div>
				</div>
			</footer>

			<!-- put global modals here -->

			<!-- User Profile Modal -->
			<form id="user-profile-form">
				<div class="modal fade modal-reset" id="user-profile-modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content" id="_modal-details">
							<!-- show modal details here -->
						</div>
					</div>
				</div>
			</form>

			<!-- Inkind D Modal -->
			<form id="inkind-d-form">
				<div class="modal fade modal-reset" id="inkind-d-modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-xl" role="document">
						<div class="modal-content" id="_modal-details">
							<!-- show modal details here -->
						</div>
					</div>
				</div>
			</form>

			<!-- External D Modal -->
			<div class="modal fade modal-reset" id="external-d-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-xl" role="document">
					<div class="modal-content" id="_modal-details">
						<!-- show modal details here -->
					</div>
				</div>
			</div>

			<!-- end -->
		</div>

	</div>
	<!--   Core JS Files   -->
	<script src="assets/js/core/jquery-3.7.1.min.js"></script>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>

	<!-- jQuery Scrollbar -->
	<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

	<!-- Chart JS -->
	<script src="assets/js/plugin/chart.js/chart.min.js"></script>

	<!-- jQuery Sparkline -->
	<!-- <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script> -->

	<!-- Chart Circle -->
	<!-- <script src="assets/js/plugin/chart-circle/circles.min.js"></script> -->

	<!-- Datatables -->
	<script src="assets/js/plugin/datatables/datatables.min.js"></script>
	<script src="assets/js/plugin/datatables/absolute-sorting.min.js"></script>

	<!-- Bootstrap Notify -->
	<script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

	<!-- jQuery Vector Maps -->
	<script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
	<script src="assets/js/plugin/jsvectormap/world.js"></script>

	<!-- Sweet Alert -->
	<script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

	<!-- KD JS -->
	<!-- <script src="assets/js/kaiadmin.min.js"></script> -->

	<script src="assets/js/main.js"></script>

	<script>
		$(document).ready(function() {
			// getAdminNotifs();

			$(`.nav-item`).on("click", function() {
				$(`.nav-item`).removeClass("active");
				$(this).addClass("active");
			});

			$(`.sub-menu`).on("click", function() {
				$(`.sub-menu`).removeClass("active");
				$(this).addClass("active");
			});

			$("#_notifs").on("click", function(e) {
				e.stopPropagation();
			});

			// setInterval(() => {
			// 	getAdminNotifs();
			// }, 18000);
		})
	</script>
</body>

</html>