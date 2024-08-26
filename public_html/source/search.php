<?php
session_start();
session_regenerate_id(true);
error_log(print_r($_SESSION, true));
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/actions/helper.php';

$searchText = isset($_GET['search']) ? $_GET['search'] : '';

$searchResults = [];

header('Content-Type: application/json');
echo json_encode($searchResults); 
?>
