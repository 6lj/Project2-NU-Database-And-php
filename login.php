<?php
// Configuration
$config = array(
    "database" => array(
        "host" => "localhost",
        "user" => "root",
        "password" => "",
        "dbname" => "appointment_db"
    )
);

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
    echo "Please fill in all fields";
    exit;
}

// Prepare SQL statement for login
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login successful
    session_start();
    $_SESSION['username'] = $username;
    header('Location: index.html');
    exit;
} else {
   // By ENDUP
    echo "Invalid username or password";
    exit;
}

$stmt->close();
$conn->close();
?>