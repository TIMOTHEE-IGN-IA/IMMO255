<?php
// config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Marque que le popup a déjà été vu pendant cette session
if (!isset($_SESSION['popup_shown'])) {
    $_SESSION['popup_shown'] = false; // false = pas encore affiché
}


define('BASE_URL', 'http://localhost/immobilier-app');

// Connexion base de données
global $pdo;
try {
    $pdo = new PDO("mysql:host=localhost;dbname=immo225;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Connexion impossible : " . $e->getMessage());
}

// Avatar utilisateur
function getUserAvatar() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) {
        return BASE_URL . '/public/img/default-avatar.png';
    }
    $stmt = $pdo->prepare("SELECT photo FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user && !empty($user['photo'])) {
        return 'data:image/jpeg;base64,' . base64_encode($user['photo']);
    }
    return BASE_URL . '/public/img/default-avatar.png';
}

function getUserPrenom() {
    return $_SESSION['prenom'] ?? 'Visiteur';
}
?>