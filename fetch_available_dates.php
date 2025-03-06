<?php
header('Content-Type: application/json');

// Load the configuration from config.json
$config = json_decode(file_get_contents("config.json"), true);

// Extract database credentials
$dbConfig = $config['database'];
$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

// Fetch available dates
$sql = "SELECT date FROM appointments WHERE status != 'محجوز'"; // استبدل 'محجوز' بالحالة التي تعني أن الموعد غير متاح
$result = $conn->query($sql);

// Check for results
$dates = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
    }
    
    // If dates are found, return them
    if (count($dates) > 0) {
        echo json_encode(array_unique($dates)); // إزالة التكرارات إذا كان هناك تواريخ مكررة
    } else {
        // No dates available
        http_response_code(204); // No Content
        echo json_encode(array("message" => "No available dates found."));
    }
} else {
    // Error with SQL query
    http_response_code(500);
    echo json_encode(array("error" => "SQL query failed: " . $conn->error));
}

// Close the connection
$conn->close();
?>