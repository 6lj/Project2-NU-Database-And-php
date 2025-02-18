<?php
session_start();

$config = json_decode(file_get_contents("config.json"), true);

// Extract the database settings
$db_config = $config["database"];
$host = $db_config["host"];
$user = $db_config["user"];
$password = $db_config["password"];
$dbname = $db_config["dbname"];

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$username = $_SESSION['username'];

// Fetch user information
$sql = "SELECT id, email, username, phone, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Handle form submission (Profile Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phone'];
    $oldPassword = $_POST['old-password'];
    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-new-password'];

    // Validate old password
    if (!password_verify($oldPassword, $user['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid old password']);
        exit;
    }

    // Validate new password
    if ($newPassword != $confirmPassword) {
        http_response_code(400);
        echo json_encode(['error' => 'New passwords do not match']);
        exit;
    }

    // Update data
    $updateSql = "UPDATE users SET username=?, email=?, phone=?";
    $params = [$newUsername, $newEmail, $newPhone];

    // If new password is provided, update it
    if (!empty($newPassword)) {
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql .= ", password=?";
        $params[] = $hashedNewPassword;
    }

    $updateSql .= " WHERE username=?";
    $params[] = $username;

    $stmt = $conn->prepare($updateSql);

    // Dynamic binding
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Update session username if changed
        $_SESSION['username'] = $newUsername;

        // Refresh user data
        $sql = "SELECT id, email, username, phone FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $newUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($user);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found after update']);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Update failed: ' . $conn->error]);
        exit;
    }
}

// Handle appointment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $appointmentDate = $_POST['date'];
    $appointmentTime = $_POST['time'];
    $department = $_POST['department'];
    $userId = $user['id'];

    // Insert appointment data
    $insertSql = "INSERT INTO appointments (user_id, appointment_date, appointment_time, department) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("isss", $userId, $appointmentDate, $appointmentTime, $department);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Appointment saved successfully']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Appointment save failed: ' . $conn->error]);
        exit;
    }
}

//  By ENDUP
header('Content-Type: application/json');
echo json_encode($user);

$conn->close();
?>