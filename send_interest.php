<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

if ($_POST['action'] !== 'send_interest') {
    echo json_encode(['success' => false]);
    exit;
}

$annonce_id = (int)$_POST['annonce_id'];
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($nom) || empty($email) || empty($telephone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit;
}

// Récupère le propriétaire
$stmt = $pdo->prepare("SELECT user_id, titre FROM annonces WHERE id = ?");
$stmt->execute([$annonce_id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    echo json_encode(['success' => false, 'message' => 'Annonce introuvable']);
    exit;
}

$proprio_id = $annonce['user_id'];
$titre_annonce = $annonce['titre'];

// ENVOIE L'EMAIL AU PROPRIÉTAIRE
$proprio_email = $pdo->query("SELECT email FROM users WHERE id = $proprio_id")->fetchColumn();
$subject = "Nouvelle demande pour : " . $titre_annonce;
$body = "Nom : $nom\nEmail : $email\nTéléphone : $telephone\n\nMessage :\n$message\n\nVoir l'annonce : " . BASE_URL . "/annonce_detail.php?id=$annonce_id";
mail($proprio_email, $subject, $body, "From: no-reply@immoci.ci");

// CRÉE UNE NOTIFICATION DANS LA CLOCHE DU PROPRIÉTAIRE
$stmt = $pdo->prepare("
    INSERT INTO notifications (user_id, titre, message, annonce_id, created_at) 
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->execute([
    $proprio_id,
    "Nouvelle demande d'intérêt",
    "$nom souhaite visiter votre annonce \"$titre_annonce\"",
    $annonce_id
]);

echo json_encode(['success' => true]);
?>