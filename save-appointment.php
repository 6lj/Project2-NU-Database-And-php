<?php

// Load the configuration from config.json
$config = json_decode(file_get_contents("config.json"), true);
$dbConfig = $config['database'];
$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

// Get appointment data from the request body
$appointmentData = json_decode(file_get_contents("php://input"), true);

// Validate the data
if (!isset($appointmentData['id']) || !isset($appointmentData['date']) || !isset($appointmentData['time']) || !isset($appointmentData['department']) || !isset($appointmentData['status'])) {
    http_response_code(400);
    echo json_encode(array("error" => "Missing required data."));
    $conn->close();
    exit();
}

// Prepare and bind the SQL statement
$stmt = $conn->prepare("INSERT INTO appointments (id, date, time, department, status) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Prepare failed: " . $conn->error));
    $conn->close();
    exit();
}

$stmt->bind_param("sssss", $appointmentData['id'], $appointmentData['date'], $appointmentData['time'], $appointmentData['department'], $appointmentData['status']);

// Execute the statement
if ($stmt->execute()) {
    // Return the inserted appointment details
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

// Close the statement and connection
$stmt->close();
$conn->close();
?>