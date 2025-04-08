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

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$email = $data['email'];
$username = $data['username'];
$password = $data['password']; 
$phone = $data['phone'];
$role = $data['role'];

$check_sql = "SELECT * FROM users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "exists";
} else {

    $verification_code = sprintf("%04d", mt_rand(0, 9999));

    $sql = "INSERT INTO users (email, username, password, phone, role, verification_code) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssss", $email, $username, $password, $phone, $role, $verification_code); 

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