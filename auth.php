<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['nom'] = $user['nom'];

        // Notification de bienvenue
        $_SESSION['notifications'][] = [
            'titre' => 'Bienvenue de retour !',
            'message' => 'Vous êtes maintenant connecté(e).',
            'time' => 'À l\'instant',
            'type' => 'success'
        ];

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
    }
    exit;
}

if ($action === 'register' || $action === 'register_and_login') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères']);
        exit;
    }

    // Vérifie si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit;
    }

    // Traitement de la photo
    $photo = null;
    if (!empty($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] === 0) {
        $photo = file_get_contents($_FILES['photo']['tmp_name']);
    }

    // Hash du mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertion dans la base
    $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, telephone, password, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$prenom, $nom, $email, $telephone, $hash, $photo]);
    $user_id = $pdo->lastInsertId();

    // CONNEXION AUTOMATIQUE APRÈS INSCRIPTION
    $_SESSION['user_id'] = $user_id;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom'] = $nom;

    // Notification de bienvenue
    if (!isset($_SESSION['notifications'])) {
        $_SESSION['notifications'] = [];
    }
    $_SESSION['notifications'][] = [
        'titre' => 'Bienvenue sur IMMO CI !',
        'message' => 'Votre compte a été créé avec succès. Bienvenue dans la communauté !',
        'time' => 'À l\'instant',
        'type' => 'success'
    ];

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action inconnue']);
?>