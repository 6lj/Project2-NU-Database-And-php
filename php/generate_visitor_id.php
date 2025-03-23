<?php
session_start(); 


if (!isset($_SESSION['visitorId'])) {
    
    $_SESSION['visitorId'] = 'visitor-' . uniqid(); 
}


echo json_encode(['visitorId' => $_SESSION['visitorId']]);
?>