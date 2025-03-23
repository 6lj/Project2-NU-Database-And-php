<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitorId'];

    if (isset($_SESSION['banned_users']) && in_array($visitorId, $_SESSION['banned_users'])) {
        echo 'true';
    } else {
        echo 'false';
    }
} else {
    echo 'false';
}
?>