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

$appointmentData = json_decode(file_get_contents("php://input"), true);

if (!isset($appointmentData['id']) || !isset($appointmentData['date']) || !isset($appointmentData['time']) || !isset($appointmentData['department']) || !isset($appointmentData['status'])) {
    http_response_code(400);
    echo json_encode(array("error" => "Missing required data."));
    $conn->close();
    exit();
}

$stmt = $conn->prepare("INSERT INTO appointments (id, date, time, department, status) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Prepare failed: " . $conn->error));
    $conn->close();
    exit();
}

$stmt->bind_param("sssss", $appointmentData['id'], $appointmentData['date'], $appointmentData['time'], $appointmentData['department'], $appointmentData['status']);

if ($stmt->execute()) {
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
    echo json_encode(array("error" => "Execute failed: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>