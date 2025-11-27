<?php
session_start();
require 'config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT cover_photo FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($user && !empty($user['cover_photo'])) {
    header("Content-Type: image/jpeg");
    echo $user['cover_photo'];
} else {
    header("Location: ".BASE_URL."/public/img/cover-default.jpg");
}
?>