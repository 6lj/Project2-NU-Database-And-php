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

$data = json_decode(file_get_contents('php://input'), true);

$response = ['status' => 'error', 'message' => 'حدث خطأ غير متوقع.'];

if (!empty($data)) {
    $insertedCount = 0; 
    $totalCount = count($data['appointments']); 

    
    $stmt = $conn->prepare("INSERT IGNORE INTO appointmentdevkit (username, email, phone, appointment_id, date, time, department, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($data['appointments'] as $appointment) {
        $stmt->bind_param("ssssssss", 
            $data['userinfo']['username'],
            $data['userinfo']['email'],
            $data['userinfo']['phone'],
            $appointment['id'], 
            $appointment['date'], 
            $appointment['time'], 
            $appointment['department'], 
            $appointment['status']
        );
        $stmt->execute();
        

        if ($stmt->affected_rows > 0) {
            $insertedCount++;
        }
    }
    
 
    if ($insertedCount > 0) {
        $response['status'] = 'success';
        $response['message'] = "تم حفظ $insertedCount من البيانات بنجاح.";
    } else {
        $response['message'] = 'لا توجد بيانات جديدة لحفظها.';
    }
}


$stmt->close();
$conn->close();


header('Content-Type: application/json');
echo json_encode($response);
?>