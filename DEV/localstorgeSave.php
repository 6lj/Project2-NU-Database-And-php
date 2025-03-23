<?php
$dbFile = 'idandappoiment.db';

try {
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $createQuery = "CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        visitor_id TEXT NOT NULL,
        appointment_data TEXT NOT NULL
    )";
    $db->exec($createQuery);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitor_id'] ?? '';
    $appointmentData = $_POST['appointment_data'] ?? '';

    if (!empty($visitorId) && !empty($appointmentData)) {
        $insertQuery = "INSERT INTO appointments (visitor_id, appointment_data) VALUES (:visitor_id, :appointment_data)";
        $stmt = $db->prepare($insertQuery);
        
        $stmt->bindParam(':visitor_id', $visitorId);
        $stmt->bindParam(':appointment_data', $appointmentData);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save data']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Visitor ID and Appointment Data are required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$db = null;
?>