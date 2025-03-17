<?php
// Load database configuration from config.json
$config = json_decode(file_get_contents("config.json"), true);
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointment_id'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Verify that required fields are provided
    if (empty($appointmentId) || empty($message)) {
        $response['message'] = 'رقم الحجز والرسالة مطلوبان.';
        echo json_encode($response);
        exit;
    }

    // Check appointment existence and status in the `appointmentdevkit` table
    $stmt = $conn->prepare("SELECT * FROM appointmentdevkit WHERE appointment_id = ?");
    $stmt->bind_param("s", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'تم التقديم مسبقا او لايوجد تفاصيل';
    } else {
        // Fetching appointment details
        $appointment = $result->fetch_assoc();

        // Log the delete request
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
        
        // Proceed with deletion
        $deleteStmt = $conn->prepare("DELETE FROM appointmentdevkit WHERE appointment_id = ?");
        $deleteStmt->bind_param("s", $appointmentId);
        
        if ($deleteStmt->execute()) {
            $response['status'] = 'success';
            $response['appointment_data'] = $appointment; // Return deleted appointment for confirmation
        } else {
            $response['message'] = 'حدث خطأ أثناء حذف الحجز.';
        }
        
        // Close statement for logging and deletion
        $logStmt->close();
        $deleteStmt->close();
    }
    
    // Close the result statement
    $stmt->close();
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>