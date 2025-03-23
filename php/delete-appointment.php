<?php

$config = json_decode(file_get_contents('config.json'), true);
if (!$config || !isset($config['database'])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not load configuration or missing database settings.']);
    exit;
}

$dbConfig = $config['database'];

$servername = $dbConfig['host'];
$username = $dbConfig['user'];
$password = $dbConfig['password'];
$dbname = $dbConfig['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'معرّف الحجز غير صالح.']);
    $conn->close();
    exit;
}

$appointmentId = $conn->real_escape_string(trim($data['id']));

$sql = "DELETE FROM appointments WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'SQL preparation error: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $appointmentId);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'تم حذف الحجز بنجاح.']); 
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'خطأ في حذف الحجز من قاعدة البيانات: ' . $stmt->error]); 
}

$stmt->close();
$conn->close();
?>