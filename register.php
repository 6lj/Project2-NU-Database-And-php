<?php

$config = json_decode(file_get_contents("config.json"), true);

// Extract the database settings
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$email = $data['email'];
$username = $data['username'];
$password = $data['password']; 
$phone = $data['phone'];
$role = $data['role']; // استلام نوع المستخدم

// Check if username already exists using a prepared statement
$check_sql = "SELECT * FROM users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "exists"; // اسم المستخدم موجود بالفعل
} else {
    // Prepare SQL statement for insertion
    $sql = "INSERT INTO users (email, username, password, phone, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("sssss", $email, $username, $password, $phone, $role); 

    if ($stmt->execute()) {
        echo "success"; 
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$check_stmt->close();
$conn->close();
?>