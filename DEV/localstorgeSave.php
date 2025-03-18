<?php
// Database file
$dbFile = 'idandappoiment.db';

// Create (or open) the SQLite database
try {
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the table if it doesn't exist
    $createQuery = "CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        visitor_id TEXT NOT NULL,
        appointment_data TEXT NOT NULL
    )";
    $db->exec($createQuery);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if POST data is set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $visitorId = $_POST['visitor_id'] ?? '';
    $appointmentData = $_POST['appointment_data'] ?? '';

    // Validate input
    if (!empty($visitorId) && !empty($appointmentData)) {
        // Prepare the insert query
        $insertQuery = "INSERT INTO appointments (visitor_id, appointment_data) VALUES (:visitor_id, :appointment_data)";
        $stmt = $db->prepare($insertQuery);
        
        // Bind parameters
        $stmt->bindParam(':visitor_id', $visitorId);
        $stmt->bindParam(':appointment_data', $appointmentData);
        
        // Execute the query
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

// Close the database connection
$db = null;
?>