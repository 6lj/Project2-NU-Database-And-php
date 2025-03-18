<?php
// admin/index.php
include '../config/config.php';
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .tab {
            display: none;
        }
        .tab.active {
            display: block;
        }
        .tabs {
            margin-bottom: 20px;
        }
        .tabs button {
            margin-right: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
    </style>
    <script>
        function showTab(tabName) {
            // Hide all tabs
            var tabs = document.querySelectorAll('.tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            // Show the selected tab
            document.getElementById(tabName).classList.add('active');
        }
    </script>
</head>
<body>
    <h1>لوحة التحكم</h1>
    <div class="tabs">
        <button onclick="showTab('viewAppointments')">عرض المواعيد</button>
        <button onclick="showTab('addUser')">إضافة مستخدم</button>
        <button onclick="showTab('addAppointment')">إضافة موعد</button>
        <button onclick="showTab('deleteUser')">حذف مستخدم</button>
    </div>

    <!-- View Appointments Tab -->
    <div class="tab" id="viewAppointments">
        <h2>عرض المواعيد</h2>
        <?php
        $result = $conn->query("SELECT * FROM appointments");
        if ($result->num_rows > 0) {
            echo "<table><tr><th>ID</th><th>التاريخ</th><th>الوقت</th><th>القسم</th><th>الحالة</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["date"] . "</td><td>" . $row["time"] . "</td><td>" . $row["department"] . "</td><td>" . $row["status"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "لا توجد مواعيد.";
        }
        ?>
    </div>

    <!-- Add User Tab -->
    <div class="tab" id="addUser">
        <h2>إضافة مستخدم</h2>
        <form method="post">
            الاسم: <input type="text" name="username" required><br>
            الهاتف: <input type="text" name="phone" required><br>
            البريد الإلكتروني: <input type="email" name="email" required><br>
            الدور: 
            <select name="role">
                <option value="مريض">مريض</option>
                <option value="متدرب">متدرب</option>
                <option value="مشرف">مشرف</option>
            </select><br>
            كلمة المرور: <input type="password" name="password" required><br>
            <input type="submit" value="إضافة">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
            $username = $conn->real_escape_string($_POST['username']);
            $phone = $conn->real_escape_string($_POST['phone']);
            $email = $conn->real_escape_string($_POST['email']);
            $role = $conn->real_escape_string($_POST['role']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (username, phone, email, role, password) VALUES ('$username', '$phone', '$email', '$role', '$password')";

            if ($conn->query($sql) === TRUE) {
                echo "<p>تمت إضافة المستخدم بنجاح</p>";
            } else {
                echo "<p>خطأ: " . $sql . "<br>" . $conn->error . "</p>";
            }
        }
        ?>
    </div>

    <!-- Add Appointment Tab -->
    <div class="tab" id="addAppointment">
        <h2>إضافة موعد</h2>
        <form method="post">
            رقم تعريف الموعد: <input type="text" name="id" required><br>
            التاريخ: <input type="date" name="date" required><br>
            الوقت: <input type="time" name="time" required><br>
            القسم: <input type="text" name="department" required><br>
            الحالة: <input type="text" name="status" required><br>
            <input type="submit" value="إضافة">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $conn->real_escape_string($_POST['id']);
            $date = $conn->real_escape_string($_POST['date']);
            $time = $conn->real_escape_string($_POST['time']);
            $department = $conn->real_escape_string($_POST['department']);
            $status = $conn->real_escape_string($_POST['status']);

            $sql = "INSERT INTO appointments (id, date, time, department, status) VALUES ('$id', '$date', '$time', '$department', '$status')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p>تمت إضافة الموعد بنجاح</p>";
            } else {
                echo "<p>خطأ: " . $sql . "<br>" . $conn->error . "</p>";
            }
        }
        ?>
    </div>

    <!-- Delete User Tab -->
    <div class="tab" id="deleteUser">
        <h2>حذف مستخدم</h2>
        <form method="post">
            إدخال ID للمستخدم الذي ترغب في حذفه: <input type="text" name="id" required><br>
            <input type="submit" value="حذف">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $conn->real_escape_string($_POST['id']);

            $sql = "DELETE FROM users WHERE id='$id'";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p>تم حذف المستخدم بنجاح</p>";
            } else {
                echo "<p>خطأ: " . $conn->error . "</p>";
            }
        }
        ?>
    </div>

    <script>
        // Show the first tab by default
        showTab('viewAppointments');
    </script>
</body>
</html>