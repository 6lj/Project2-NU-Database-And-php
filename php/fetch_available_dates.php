<?php
header('Content-Type: application/json');

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

$sql = "SELECT date FROM appointments WHERE status != 'محجوز'";
$result = $conn->query($sql);

$dates = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
    }
    
    if (count($dates) > 0) {
        echo json_encode(array_unique($dates));
    } else {
        http_response_code(204);
        echo json_encode(array("message" => "No available dates found."));
    }
} else {
    http_response_code(500);
    echo json_encode(array("error" => "SQL query failed: " . $conn->error));
}

$conn->close();
?>