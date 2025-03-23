<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];


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