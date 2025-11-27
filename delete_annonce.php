<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

// Vérifie que l'annonce appartient bien à l'utilisateur
$stmt = $pdo->prepare("SELECT id FROM annonces WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Annonce non trouvée']);
    exit;
}

// Supprime les photos associées
$pdo->prepare("DELETE FROM annonce_photos WHERE annonce_id = ?")->execute([$id]);

// Supprime l'annonce
$pdo->prepare("DELETE FROM annonces WHERE id = ?")->execute([$id]);

echo json_encode(['success' => true]);
?>