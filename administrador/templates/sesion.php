<?php
    session_start();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $limitInactividad=900; 

    if (isset($_SESSION['ULT_ACTIVIDAD']) && (time() - $_SESSION['ULT_ACTIVIDAD'] > $limitInactividad)) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
    $_SESSION['ULT_ACTIVIDAD']=time();
?>
