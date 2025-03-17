<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];

    // Check if the visitorId is in the banned users session
    if (isset($_SESSION['banned_users']) && in_array($visitorId, $_SESSION['banned_users'])) {
        echo 'true'; // Visitor is banned
    } else {
        echo 'false'; // Visitor is not banned
    }
} else {
    echo 'false'; // Invalid request
}
?>