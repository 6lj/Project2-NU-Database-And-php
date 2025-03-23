<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];

    
    if (isset($_SESSION['banned_users'])) {
        $key = array_search($visitorId, $_SESSION['banned_users']);
        if ($key !== false) {
            unset($_SESSION['banned_users'][$key]); 
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