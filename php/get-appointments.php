<?php

$config = json_decode(file_get_contents("config.json"), true);

$dbConfig = $config['database'];
$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

$date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
$department = isset($_GET['department']) ? $conn->real_escape_string($_GET['department']) : '';

if (empty($date) || empty($department)) {
    http_response_code(400);
    echo json_encode(array("error" => "Date and department are required."));
    $conn->close();
    exit();
}

$stmt = $conn->prepare("SELECT time FROM appointments WHERE date = ? AND department = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Prepare failed: " . $conn->error));
    $conn->close();
    exit();
}

$stmt->bind_param("ss", $date, $department);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(array("error" => "Execute failed: " . $stmt->error));
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

$bookedTimes = array();
while ($row = $result->fetch_assoc()) {
    $bookedTimes[] = $row['time'];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');

echo json_encode($bookedTimes);

?>