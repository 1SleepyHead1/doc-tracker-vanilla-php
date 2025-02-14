<?php
session_start();
if (empty($_SESSION['user_id'])) {
    session_destroy();
    header('location: login.php');
} else {
    if ($_SESSION['is_admin'] == 1) {
        header('location: index.php');
    }
}

require_once "conn.php";

$q = $c->prepare("
    SELECT
            CONCAT(IFNULL(b.first_name,''),' ',IFNULL(b.middle_name,''),' ',IFNULL(b.last_name,'')) AS user_name,
            b.email,
            b.first_name
    FROM users a
    LEFT JOIN donors b ON a.donor_id = b.id
    WHERE a.is_admin = 0 AND a.id = ?
");
$q->execute([$_SESSION['user_id']]);
$userInfo = $q->fetch();
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>RDMS</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/logo.png" type="image/x-icon" />

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

    <style>
        .swal-text {
            text-align: center;
        }

        .notif-text {
            font-size: 0.94rem !important;
            line-height: 1 !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark2">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark2">
                    <a class="logo">
                        <!-- <img src="" alt="logo here" class="navbar-brand" height="28" /> -->
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
                        <li id="parent-dashboard" class="nav-item active">
                            <a id="nav-main-dashboard" data-bs-toggle="collapse" href="#dashboard" class="" aria-expanded="true">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse show" id="dashboard">
                                <ul class="nav nav-collapse">
                                    <li class="sub-menu active" data-target="default-menu">
                                        <a id="default-menu" data-parent="dashboard" data-url="views/dashboard/admin-d/admin-d.php" onclick="loadMenu('views/dashboard/admin-d/admin-d.php','default')">
                                            <span class="sub-item">Home Panel</span>
                                        </a>
                                    </li>

                                    <li class="sub-menu" data-target="user-home-menu">
                                        <a id="user-home-menu" data-parent="dashboard" data-url="views/dashboard/user-d/user-d.php" onclick="loadMenu('views/dashboard/user-d/user-d.php','user-home')">
                                            <span class="sub-item">User Home Panel</span>
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
                        <li id="parent-monitoring" class="nav-item">
                            <a data-bs-toggle="collapse" href="#monitoring">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Monitoring</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="monitoring">
                                <ul class="nav nav-collapse">
                                    <li class="sub-menu">
                                        <a id="user-inventory-menu" data-parent="monitoring" data-url="views/monitoring/user-inventory/user-inventory.php" onclick="loadMenu('views/monitoring/user-inventory/user-inventory.php','user-inventory')">
                                            <span class="sub-item">Inventory</span>
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
                    <div class="logo-header" data-background-color="dark">
                        <a href="index-u.html" class="logo">
                            <!-- <img src="" alt="navbar brand" class="navbar-brand" height="20" /> -->
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
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link dropdown-toggle" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <span id="_notif-count" class="notification"></span>
                                </a>
                                <div id="_notifs" class="dropdown-menu dropdown-menu-right navbar-dropdown bg-white border shadow" style="width: 400px !important;" aria-labelledby="notifDropdown">
                                    <div class="dropdown-header px-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0">Notifications</h6>
                                            <a id="btn-read-notifs" onclick="readUserNotif('1')">Mark all as read</a>
                                        </div>
                                    </div>
                                    <hr class="my-0">
                                    <div class="dropdown-body overflow-auto" style="max-height: 400px;">
                                        <table class="table table-borderless table-hover" id="_tbl-notifs">
                                            <col width="5%">
                                            <col width="93%">
                                            <col width="2%">
                                            <tbody>
                                                <!-- show notifications here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="assets/img/user-default.png" alt="..." class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hi,</span>
                                        <span class="fw-bold"><?= $userInfo['user_name'] ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="assets/img/user-default.png" alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4><?= $userInfo['first_name'] ?></h4>
                                                    <p class="text-muted"><?= $userInfo['email'] ?></p>
                                                    <a class="btn btn-xs btn-secondary btn-sm" onclick="showUserProfileModalContent('<?= $_SESSION['user_id'] ?>')">Update Password</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" onclick="logout()">Logout</a>
                                        </li>
                                    </div>
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
                        <h3 id="ph-title" class="fw-bold mb-3"></h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="">
                                <a>
                                    <i id="ph-icon" class=""></i>
                                </a>
                            </li>
                            <li class="separator">
                                <i class="icon-arrow-right"></i>
                            </li>
                            <li class="nav-item">
                                <a id="ph-sub-menu"></a>
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
                <div class="container-fluid d-flex justify-content-center">
                    <div class="">
                        Copyright © 2024. All rights reserved.
                    </div>
                </div>
            </footer>

            <!-- put glboal modals here -->

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

            <!-- Unit Modal -->
            <form id="unit-form">
                <div class="modal fade modal-reset" id="unit-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
    <!-- <script src="assets/js/plugin/chart.js/chart.min.js"></script> -->

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <!-- <script src="assets/js/plugin/chart-circle/circles.min.js"></script> -->

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

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
            getUserNotifs();

            $(`.nav-item`).on("click", function() {
                $(`.nav-item`).removeClass("active");
                $(this).addClass("active");
            });

            $(`.sub-menu`).on("click", function() {
                $(`.sub-menu`).removeClass("active");
                $(this).addClass("active");
            });

            $("#user-profile-form").submit(function(e) {
                e.preventDefault();
            });

            $("#_notifs").on("click", function(e) {
                e.stopPropagation();
            });

            setInterval(() => {
                getUserNotifs();
            }, 18000);
        })
    </script>
</body>

</html>