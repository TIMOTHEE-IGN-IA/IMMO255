<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM notifications WHERE lu = 0");
    $count = $stmt->fetchColumn();
    
    // Retourne du JSON
    header('Content-Type: application/json');
    echo json_encode(['count' => (int)$count]);
} catch(Exception $e) {
    echo json_encode(['count' => 0]);
}
?>