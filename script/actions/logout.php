<?php
try {
    session_start();

    if (session_destroy()) {
        echo 's';
    } else {
        echo "Error. Please contact system administrator";
    }
} catch (PDOException $e) {
    echo "Error. Please Contact System Administrator. <br/>" . $e->getMessage();
}
