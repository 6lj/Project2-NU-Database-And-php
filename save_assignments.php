<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (count($data) > 0) {
    foreach ($data as $assignment) {
        $sql = "INSERT INTO trainees (name, phone, appointment, trainee) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $assignment['name'], $assignment['phone'], $assignment['appointment'], $assignment['trainee']);
        $stmt->execute();
    }

    echo json_encode(array('message' => 'Assignments saved successfully'));
} else {
    echo json_encode(array('message' => 'No assignments to save'));
}
// By ENDUP
$conn->close();
?>