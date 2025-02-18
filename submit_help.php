<?php
include 'config.php';

// Initialize response array
$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input.  This is VERY important
    $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '';
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';


    if (empty($date) || empty($name) || empty($message)) {
        $response['message'] = 'الرجاء ملء جميع الحقول.';
    } else {
        // Prepare and bind parameters to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO help_messages (date, name, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $date, $name, $message);  // "sss" means 3 strings

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'تم إرسال الرسالة بنجاح.';
        } else {
            $response['message'] = 'حدث خطأ أثناء إرسال الرسالة: ' . $stmt->error;
            error_log("Database error: " . $stmt->error); // Log errors for debugging
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'الوصول غير مصرح به.';
}

$conn->close();
header('Content-Type: application/json'); // Set header for JSON response
echo json_encode($response); // Return JSON response , by ENDUP
?>