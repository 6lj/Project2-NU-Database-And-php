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
$username = $_POST['username'];
$password = $_POST['password'];

// Check if username and password are empty
if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => ""]);
    exit;
}

// Prepare SQL statement for login
$sql = "SELECT role FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login successful
    session_start();
    $_SESSION['username'] = $username;

    // Fetch user's role
    $user = $result->fetch_assoc();
    $_SESSION['role'] = $user['role'];

    // Redirect based on role
    if ($_SESSION['role'] === "مشرف") {
        header('Location: index.html/supervisor');
    } else {
        header('Location: index.html');
    }
    exit;
} else {
    // Redirect on invalid login
    header('Location: login.html?error=invalid');
    exit;
}

$stmt->close();
$conn->close();
?>