<?php
include 'config.php';

$sql = "SELECT * FROM trainees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $assignments = array();
    while ($row = $result->fetch_assoc()) {
        $assignments[] = array(
            'name' => $row['name'],
            'phone' => $row['phone'],
            'appointment' => $row['appointment'],
            'trainee' => $row['trainee']
        );
    }

    echo json_encode($assignments);
} else {
    echo json_encode(array());
}
// Close the statement and connection , By ENDUP
$conn->close();
?>