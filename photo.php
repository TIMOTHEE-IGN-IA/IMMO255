<?php
require 'config.php';
$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) die();

$stmt = $pdo->prepare("SELECT photo_path, photo_type FROM annonce_photos WHERE id = ?");
$stmt->execute([$id]);
$photo = $stmt->fetch();

if ($photo && !empty($photo['photo_path'])) {
    header("Content-Type: " . $photo['photo_type']);
    echo $photo['photo_path'];
} else {
    header("Location: " . BASE_URL . "/public/img/default.jpg");
}
?>