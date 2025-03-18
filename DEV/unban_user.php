<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];

    // Check if the visitorId is in the banned users session
    if (isset($_SESSION['banned_users'])) {
        $key = array_search($visitorId, $_SESSION['banned_users']);
        if ($key !== false) {
            unset($_SESSION['banned_users'][$key]); // Remove the banned user
            echo "User unbanned.";
        } else {
            echo "User not found in banned list.";
        }
    } else {
        echo "No banned users.";
    }
} else {
    echo "Invalid request.";
}
?>