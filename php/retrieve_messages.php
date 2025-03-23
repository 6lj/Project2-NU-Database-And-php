<?php
include 'config.php';

$messages = array();

$sql = "SELECT name, message, date FROM help_messages ORDER BY date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = array(
            'name' => htmlspecialchars($row['name']),
            'message' => htmlspecialchars($row['message']),
            'date' => htmlspecialchars($row['date'])
        );
    }
}

header('Content-Type: application/json');
echo json_encode($messages);

$conn->close();
?>