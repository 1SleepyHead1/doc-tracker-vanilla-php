<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['is_admin'] == 0) {
        header('location: index-u.php');
    } else {
        header('location: index.php');
    }
}

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

    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
    </style>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/style.min.css" />
</head>

<body>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <!-- logo image here -->
                    <!-- <img src="" class="img-fluid" alt="Sample image"> -->
                </div>
                <div class="col-md-8 col-lg-6 col-xl-3 offset-xl-1">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form id="login-form">
                                <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                    <p class="lead fw-normal mb-0 me-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Sign in with your Account
                                    </p>
                                </div>

                                <div class="divider d-flex align-items-center my-4"></div>

                                <!-- username input -->
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <label class="form-label mx-1" for="username"><i class="fas fa-user me-2"></i>Username</label>
                                    <input type="text" id="username" class="login-i form-control form-control-md" placeholder="Username" autocomplete="on" required />
                                </div>

                                <!-- password input -->
                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label mx-1" for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                    <input type="password" id="password" class="login-i form-control form-control-md" placeholder="Password" autocomplete="on" required />
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Checkbox -->
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input me-2" type="checkbox" value="" id="show-pass" />
                                        <label class="form-check-label" for="show-pass" style="cursor: pointer;">
                                            <i class="fas fa-eye me-1"></i>
                                            Show password
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-4 pt-2">
                                    <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-md" style="padding-left: 2.5rem; padding-right: 2.5rem;" onclick="loginAttempt()">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-light text-center text-lg-start fixed-bottom">
            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
                © <script>
                    document.write(new Date().getFullYear())
                </script> All Rights Reserved
            </div>
        </footer>
    </section>

    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/jquery.js"></script>
    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <script src="assets/js/main.js"></script>

    <script>
        $(document).ready(function() {
            $("#show-pass").click(function() {
                if ($(this).is(":checked")) {
                    $("#password").attr("type", "text");
                } else {
                    $("#password").attr("type", "password");
                }
            });

            $("#login-form").submit(function(e) {
                e.preventDefault();
            })
        })
    </script>
</body>

</html>