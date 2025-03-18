<?php
// admin/view_appointments.php

include '../config.php';

$result = $conn->query("SELECT * FROM appointments");

echo "<h1>المواعيد</h1>";
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>التاريخ</th><th>الوقت</th><th>القسم</th><th>الحالة</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["date"] . "</td><td>" . $row["time"] . "</td><td>" . $row["department"] . "</td><td>" . $row["status"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "لا توجد مواعيد.";
}
$conn->close();
?>