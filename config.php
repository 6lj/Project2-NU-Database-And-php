<?php
$config = json_decode(file_get_contents('config.json'), true);
$host = $config['database']['host'];
$user = $config['database']['user'];
$password = $config['database']['password'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($host, $user, $password, $dbname);
// By ENDUP
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>