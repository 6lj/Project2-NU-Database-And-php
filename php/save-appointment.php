<?php

header('Content-Type: application/json');

$config = json_decode(file_get_contents("config.json"), true);

if ($config === null || !isset($config['database'])) {
    http_response_code(500);
    echo json_encode(array("error" => "Failed to load database configuration. Ensure config.json exists and is valid."));
    exit();
}

$dbConfig = $config['database'];

if (!isset($dbConfig['host']) || !isset($dbConfig['user']) || !isset($dbConfig['password']) || !isset($dbConfig['dbname'])) {
    http_response_code(500);
    echo json_encode(array("error" => "Incomplete database configuration in config.json. Missing host, user, password, or dbname."));
    exit();
}

$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Database connection failed: " . $conn->connect_error));
    exit();
}

$conn->set_charset("utf8mb4"); 

$json_data = file_get_contents("php://input");
$appointmentData = json_decode($json_data, true);

if ($appointmentData === null && $json_data !== '') {
     http_response_code(400);
     echo json_encode(array("error" => "Invalid JSON received."));
     $conn->close();
     exit();
}

if (!isset($appointmentData['id']) || !isset($appointmentData['date']) || !isset($appointmentData['time']) || !isset($appointmentData['department']) || !isset($appointmentData['status'])) {
    http_response_code(400);
    echo json_encode(array("error" => "Missing required data. Ensure 'id', 'date', 'time', 'department', and 'status' are provided."));
    $conn->close();
    exit();
}

$checkSql = "SELECT COUNT(*) FROM appointments WHERE date = ? AND time = ?";
$checkStmt = $conn->prepare($checkSql);

if ($checkStmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Prepare check failed: " . $conn->error));
    $conn->close();
    exit();
}

$checkStmt->bind_param("ss", $appointmentData['date'], $appointmentData['time']);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    http_response_code(409); 
    echo json_encode(array(
        "error" => " هذا الموعد  " . htmlspecialchars($appointmentData['date']) . " at " . htmlspecialchars($appointmentData['time']) . " لقد تم حجزه مسبقا"
    ));
    $conn->close();
    exit();
}

$insertSql = "INSERT INTO appointments (id, date, time, department, status) VALUES (?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);

if ($insertStmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Prepare insert failed: " . $conn->error));
    $conn->close();
    exit();
}

$insertStmt->bind_param("sssss", $appointmentData['id'], $appointmentData['date'], $appointmentData['time'], $appointmentData['department'], $appointmentData['status']);

if ($insertStmt->execute()) {

    http_response_code(201); 
    echo json_encode(array(
        "message" => "Appointment saved successfully.",
        "appointment" => array(
            "id" => $appointmentData['id'],
            "date" => $appointmentData['date'],
            "time" => $appointmentData['time'],
            "department" => $appointmentData['department'],
            "status" => $appointmentData['status']
        )
    ));
} else {

    http_response_code(500); 
    echo json_encode(array("error" => "Execute insert failed: " . $insertStmt->error));
}

$insertStmt->close();
$conn->close();

?>