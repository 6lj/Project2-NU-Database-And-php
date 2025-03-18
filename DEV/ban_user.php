<?php
session_start(); // Start session to track banned visitors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];

    // Add the visitorId to the banned users session
    if (!isset($_SESSION['banned_users'])) {
        $_SESSION['banned_users'] = [];
    }

    if (!in_array($visitorId, $_SESSION['banned_users'])) {
        $_SESSION['banned_users'][] = $visitorId;
        echo "User banned.";
    } else {
        echo "User is already banned.";
    }
} else {
    echo "Invalid request.";
}
?>