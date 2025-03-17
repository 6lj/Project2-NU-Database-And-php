<?php
// record_visitor.php

try {
    // Create or open the SQLite database
    $db = new PDO('sqlite:track_visitor.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create the visitors table if it does not exist
    $createVisitorsTableQuery = "
        CREATE TABLE IF NOT EXISTS visitors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            visitor_id TEXT NOT NULL,
            ip_address TEXT NOT NULL,
            current_page TEXT NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            logged_in BOOLEAN NOT NULL
        );
    ";
    $db->exec($createVisitorsTableQuery);

    // Create the appointments table if it does not exist
    $createAppointmentsTableQuery = "
        CREATE TABLE IF NOT EXISTS appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            visitor_id TEXT NOT NULL,
            username TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            appointment_id TEXT NOT NULL,
            date TEXT NOT NULL,
            time TEXT NOT NULL,
            department TEXT NOT NULL,
            status TEXT NOT NULL
        );
    ";
    $db->exec($createAppointmentsTableQuery);

    // Get client info
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $isLoggedIn = isset($_POST['loggedIn']) ? (int)$_POST['loggedIn'] : 0; // Cast to integer
    $visitorId = isset($_POST['visitorId']) ? $_POST['visitorId'] : null; // Get the visitor ID
    $currentPage = isset($_POST['currentPage']) ? $_POST['currentPage'] : null; // Get the current page

    if (!$visitorId) {
        echo json_encode(['error' => 'Visitor ID is required.']);
        exit;
    }

    // Insert visitor data into the visitors table
    $stmt = $db->prepare("INSERT INTO visitors (visitor_id, ip_address, current_page, logged_in) VALUES (:visitor_id, :ip_address, :current_page, :logged_in)");
    $stmt->bindParam(':visitor_id', $visitorId);
    $stmt->bindParam(':ip_address', $ipAddress);
    $stmt->bindParam(':current_page', $currentPage);
    $stmt->bindParam(':logged_in', $isLoggedIn);
    $stmt->execute();

    // Check if there's appointment data sent as JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['appointmentId'])) {
        // Insert the appointment data
        $stmt = $db->prepare("INSERT INTO appointments (visitor_id, username, email, phone, appointment_id, date, time, department, status) VALUES (:visitor_id, :username, :email, :phone, :appointment_id, :date, :time, :department, :status)");
        
        $stmt->bindParam(':visitor_id', $data['visitorId']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':appointment_id', $data['appointmentId']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':time', $data['time']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':status', $data['status']);
        
        // Execute insert statement
        $stmt->execute();
    }

    // Fetch appointments from the appointments table in the SQLite database
    $stmt = $db->prepare("SELECT username, email, phone, appointment_id, date, time, department, status FROM appointments WHERE visitor_id = :visitor_id");
    $stmt->bindParam(':visitor_id', $visitorId);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparing response
    $response = [
        'message' => "Visitor recorded.",
        'appointments' => $appointments
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => "Database error: " . $e->getMessage()]);
}