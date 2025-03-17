<?php
session_start(); 
$config = json_decode(file_get_contents("config.json"), true);


$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

$userRole = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : '';

echo $userRole;
?>