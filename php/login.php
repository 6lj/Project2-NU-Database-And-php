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

$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => ""]);
    exit;
}

$sql = "SELECT role FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    session_start();
    $_SESSION['username'] = $username;

    $user = $result->fetch_assoc();
    $_SESSION['role'] = $user['role'];

    if ($_SESSION['role'] === "مشرف") {
        header('Location: ../Syetem.html/supervisor');
    } else {
        header('Location: ../Syetem.html');
    }
    exit;
} else {
    header('Location: ../login.html?error=invalid');
    exit;
}

$stmt->close();
$conn->close();
?>