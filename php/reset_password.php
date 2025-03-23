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

$newPassword = $data['newPassword'];

$sql = "UPDATE users SET password = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $newPassword);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحديث كلمة المرور.']);
}

$stmt->close();
$conn->close();
?>