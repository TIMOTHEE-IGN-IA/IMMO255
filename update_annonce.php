<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: annonces.php');
    exit;
}

// Vérifie que l'ID est présent
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    die("Erreur : ID manquant");
}

// Vérifie que l'annonce appartient bien à l'utilisateur
$stmt = $pdo->prepare("SELECT id FROM annonces WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    die("Erreur : Vous n'avez pas le droit de modifier cette annonce");
}

// Récupère les données
$titre = trim($_POST['titre'] ?? '');
$prix = (int)($_POST['prix'] ?? 0);
$ville = trim($_POST['ville'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($titre) || $prix <= 0 || empty($ville) || empty($description)) {
    die("Tous les champs sont obligatoires");
}

// Mise à jour de l'annonce
$stmt = $pdo->prepare("UPDATE annonces SET titre = ?, prix = ?, ville = ?, description = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$titre, $prix, $ville, $description, $id, $_SESSION['user_id']]);

// Redirection vers le profil avec message de succès
$_SESSION['message'] = "Annonce modifiée avec succès !";
header('Location: profil.php');
exit;
?>