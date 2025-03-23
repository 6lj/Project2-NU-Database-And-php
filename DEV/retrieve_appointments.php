<?php
$config = json_decode(file_get_contents("../php/config.json"), true);

$dbConfig = $config['database'];
$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$sql = "SELECT username, email, phone, appointment_id, date, time, department, status FROM appointmentdevkit";
$result = $conn->query($sql);

$appointments = [];

if ($result->num_rows > 0) {
  
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($appointments);
?>