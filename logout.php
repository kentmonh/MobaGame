<?php
    ob_start();

    session_start();
    session_destroy();
    // Redirect to the index page
    header('Location: index.php');
?>