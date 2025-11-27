<?php
require 'config.php';
header('Content-Type: application/json');

$page = max(1, (int)($_POST['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if (!empty($_POST['ville'])) {
    $where[] = "a.ville LIKE ?";
    $params[] = '%' . $_POST['ville'] . '%';
}
if (!empty($_POST['type'])) {
    $where[] = "a.type_bien = ?";
    $params[] = $_POST['type'];
}
if (!empty($_POST['prix_max'])) {
    $where[] = "a.prix <= ?";
    $params[] = $_POST['prix_max'];
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT a.*, u.prenom as proprietaire,
           (SELECT id FROM annonce_photos WHERE annonce_id = a.id ORDER BY id ASC LIMIT 1) as photo_id
    FROM annonces a
    LEFT JOIN users u ON a.user_id = u.id
    $whereClause
    ORDER BY a.created_at DESC
    LIMIT ? OFFSET ?
");
$params[] = $perPage;
$params[] = $offset;
$stmt->execute($params);
$annonces = $stmt->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM annonces a $whereClause");
$total->execute(array_slice($params, 0, -2));
$totalAnnonces = $total->fetchColumn();
$totalPages = ceil($totalAnnonces / $perPage);

echo json_encode([
    'annonces' => $annonces,
    'totalPages' => $totalPages
]);
?>