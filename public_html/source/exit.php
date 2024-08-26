<?php
session_start();
require_once __DIR__ . '/../dbconnect.php';

if (isset($_SESSION['user'])) {
    // $_SESSION = array();
    session_destroy();
}
header('Location: /index.php');
exit;
?>