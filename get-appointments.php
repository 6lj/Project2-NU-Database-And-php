<?php
// get-appointments.php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Load the configuration from config.json (same as save-appointment.php)
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

// Sanitize and retrieve the date and department from the GET request
$date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
$department = isset($_GET['department']) ? $conn->real_escape_string($_GET['department']) : '';

// Validate the input
if (empty($date) || empty($department)) {
    http_response_code(400); // Bad Request
    echo json_encode(array("error" => "Date and department are required."));
    $conn->close();
    exit();
}

// Prepare and execute the SQL query
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

// Set the content type to JSON
header('Content-Type: application/json');

// Return the booked times as JSON , By ENDUP
echo json_encode($bookedTimes);

?>