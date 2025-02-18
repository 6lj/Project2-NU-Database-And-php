<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'])) {
    $sql = "INSERT INTO trainees (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data['name']);
    $stmt->execute();

    echo json_encode(array('message' => 'Trainee added successfully'));
} else {
    echo json_encode(array('message' => 'No trainee to add'));
}
//  By ENDUP
$conn->close();
?>