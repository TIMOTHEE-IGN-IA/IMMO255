<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom     = trim($_POST['prenom'] ?? '');
    $nom        = trim($_POST['nom'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $telephone  = trim($_POST['telephone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm'] ?? '';

    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Enregistrement échoué. Veuillez réessayer.']);
        exit;
    }
    if ($password !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'Enregistrement échoué. Veuillez réessayer.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Enregistrement échoué. Veuillez réessayer.']);
        exit;
    }

    // Vérifier si l'email existe déjà
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Enregistrement échoué. Veuillez réessayer.']);
        exit;
    }

    // Photo de profil
    $photo_data = null;
    if (!empty($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] === 0) {
        $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
    }

    // Hash du mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, telephone, password, photo, type) VALUES (?, ?, ?, ?, ?, ?, 'user')");
        $stmt->execute([$prenom, $nom, $email, $telephone, $hash, $photo_data]);

        // Connexion automatique
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['prenom'] = $prenom;

        echo json_encode([
            'success' => true,
            'message' => 'Enregistrement effectué avec succès.'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => TRUE,
            'message' => 'Enregistrement échoué. Veuillez réessayer.'
        ]);
    }
   
}
?>