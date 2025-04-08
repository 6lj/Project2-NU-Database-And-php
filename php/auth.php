<?php

$config = json_decode(file_get_contents("config.json"), true);
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$sql = "SELECT email, username, phone, role, verification_code FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $users
    ]);
} else {

    echo json_encode([
        "status" => "error",
        "message" => "لا توجد بيانات مستخدمين."
    ]);
}

$conn->close();
?>