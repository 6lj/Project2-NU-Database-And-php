<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config = json_decode(file_get_contents("config.json"), true);
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['name'], $data['phone'], $data['appointment'], $data['patientName'])) {
        die(json_encode(['success' => false, 'message' => 'البيانات غير كافية']));
    }

    $name = $data['name'];
    $phone = $data['phone'];
    $appointmentDate = $data['appointment'];
    $patientName = $data['patientName'];

    $trainee_sql = "INSERT INTO trainees (name, phone, appointment, assigned_to, accepted, patient_name) VALUES (?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($trainee_sql);

    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'خطأ في إعداد العبارة: ' . $conn->error]));
    }

    $stmt->bind_param("sssss", $name, $phone, $appointmentDate, $patientName, $patientName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تنفيذ العملية: ' . $stmt->error]);
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT name, phone, appointment, assigned_to AS trainee, patient_name FROM trainees WHERE accepted = 1";
    $result = $conn->query($sql);

    $trainees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trainees[] = $row; 
        }
    }

    header('Content-Type: application/json');
    echo json_encode($trainees);
}
// check by ENDUP
$conn->close();