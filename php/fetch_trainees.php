<?php
$config = json_decode(file_get_contents("config.json"), true);
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT username, phone FROM users WHERE role = 'متدرب'");
$trainees = [];
while ($row = $result->fetch_assoc()) {
    $trainees[] = $row;
}

$result_patients = $conn->query("SELECT username FROM users WHERE role = 'مريض'");
$patients = [];
while ($row = $result_patients->fetch_assoc()) {
    $patients[] = $row;
}

echo json_encode(['trainees' => $trainees, 'patients' => $patients]);
$conn->close();
?>