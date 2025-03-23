<?php
include 'config.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '';
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    if (empty($date) || empty($name) || empty($message)) {
        $response['message'] = 'الرجاء ملء جميع الحقول.';
    } else {
        $stmt = $conn->prepare("INSERT INTO help_messages (date, name, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $date, $name, $message);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'تم إرسال الرسالة بنجاح.';
        } else {
            $response['message'] = 'حدث خطأ أثناء إرسال الرسالة: ' . $stmt->error;
            error_log("Database error: " . $stmt->error);
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'الوصول غير مصرح به.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>