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

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($appointmentId) || empty($message)) {
        $response['message'] = 'رقم الحجز والرسالة مطلوبان.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM appointmentdevkit WHERE appointment_id = ?");
    $stmt->bind_param("s", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'تم التقديم مسبقا او لايوجد تفاصيل';
    } else {
        $appointment = $result->fetch_assoc();

        $logStmt = $conn->prepare("INSERT INTO DeleteRequest (appointment_id, username, email, department, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $logStmt->bind_param(
            "sssss", 
            $appointment['appointment_id'], 
            $appointment['username'], 
            $appointment['email'], 
            $appointment['department'], 
            $message
        );
        
        $logStmt->execute();
        
        $deleteStmt = $conn->prepare("DELETE FROM appointmentdevkit WHERE appointment_id = ?");
        $deleteStmt->bind_param("s", $appointmentId);
        
        if ($deleteStmt->execute()) {
            $response['status'] = 'success';
            $response['appointment_data'] = $appointment;
        } else {
            $response['message'] = 'حدث خطأ أثناء حذف الحجز.';
        }
        
        $logStmt->close();
        $deleteStmt->close();
    }
    
    $stmt->close();
}

$conn->close();

echo json_encode($response);
?>