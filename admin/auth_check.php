<?php
// Include at the top of every protected admin page.
// Assumes session_start() has already been called by the including file.
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
