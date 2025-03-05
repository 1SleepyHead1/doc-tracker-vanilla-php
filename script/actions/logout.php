<?php
try {
    session_start();

    if (session_destroy()) {
        if ($_POST['a'] == 1) {
            session_start();
            $_SESSION['is_logged_in'] = true;
        }
        echo 's';
    } else {
        echo "Error. Please contact system administrator";
    }
} catch (PDOException $e) {
    echo "Error. Please Contact System Administrator. <br/>" . $e->getMessage();
}
