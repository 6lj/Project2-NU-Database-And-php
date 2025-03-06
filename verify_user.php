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

$username = $data['username'];
$email = $data['email'];

// Check if the user exists
$sql = "SELECT * FROM users WHERE username = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Generate verification code (you can use mail service to send it to user)
    $verificationCode = rand(1000, 9999); // مثال على توليد رمز عشوائي
    // (يمكنك إرسال هذا الرمز عبر البريد الإلكتروني)
    
    // قم بتخزين الرمز في قاعدة البيانات، أو في الذاكرة (تحتاج إلى تنفيذ ذلك حسب الحاجة)
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'المعلومات المدخلة غير صحيحة.']);
}

$stmt->close();
$conn->close();
?>