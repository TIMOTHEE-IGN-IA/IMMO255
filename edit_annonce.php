<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: annonces.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$annonce = $stmt->fetch();

if (!$annonce) {
    header('Location: profil.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier - <?= htmlspecialchars($annonce['titre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Modifier l'annonce</h2>
    <form action="update_annonce.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $annonce['id'] ?>">
        <div class="mb-3"><input name="titre" type="text" class="form-control form-control-lg" value="<?= htmlspecialchars($annonce['titre']) ?>" required></div>
        <div class="mb-3"><input name="prix" type="number" class="form-control form-control-lg" value="<?= $annonce['prix'] ?>" required></div>
        <div class="mb-3"><input name="ville" type="text" class="form-control form-control-lg" value="<?= htmlspecialchars($annonce['ville']) ?>" required></div>
        <div class="mb-3"><textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($annonce['description']) ?></textarea></div>
        <button type="submit" class="btn btn-success btn-lg">Enregistrer les modifications</button>
        <a href="profil.php" class="btn btn-secondary btn-lg ms-3">Annuler</a>
    </form>
</div>
</body>
</html>