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

$sql = "SELECT date FROM appointments WHERE status != 'محجوز'";
$result = $conn->query($sql);

$dates = array();

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
    }

    if (count($dates) > 0) {

        echo json_encode(array_values(array_unique($dates)));
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