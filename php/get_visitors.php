<?php
session_start();

try {
    $db = new PDO('sqlite:track_visitor.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("SELECT visitor_id, ip_address, current_page, logged_in FROM visitors");
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($visitors as &$visitor) {
        $visitor['banned'] = isset($_SESSION['banned_users']) && in_array($visitor['visitor_id'], $_SESSION['banned_users']);
    }

    header('Content-Type: application/json');
    echo json_encode($visitors);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>