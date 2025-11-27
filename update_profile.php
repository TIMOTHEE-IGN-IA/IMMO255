<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

if ($_POST['action'] === 'update_profile_photo' && isset($_FILES['photo'])) {
    $photo = file_get_contents($_FILES['photo']['tmp_name']);
    $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id = ?");
    $stmt->execute([$photo, $_SESSION['user_id']]);
    
    echo json_encode([
        'success' => true,
        'photo_url' => getUserAvatar($_SESSION['user_id'])
    ]);
    exit;
}

if ($_POST['action'] === 'update_cover' && isset($_FILES['cover'])) {
    $cover = file_get_contents($_FILES['cover']['tmp_name']);
    $stmt = $pdo->prepare("UPDATE users SET cover_photo = ? WHERE id = ?");
    $stmt->execute([$cover, $_SESSION['user_id']]);
    
    echo json_encode([
        'success' => true,
        'cover_url' => BASE_URL.'/photo_cover.php?id='.$_SESSION['user_id']
    ]);
    exit;
}
?>