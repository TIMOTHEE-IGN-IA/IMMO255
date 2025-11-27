<?php
require '../config.php';
require_login();

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM annonce_photos WHERE annonce_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM annonces WHERE id = ?")->execute([$id]);
    header('Location: index.php');
    exit;
}

// Liste des annonces
$stmt = $pdo->query("SELECT a.*, u.prenom FROM annonces a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");
$annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin - ImmoLux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">Administration (<?= count($annonces) ?> annonces)</h1>
  <a href="../annonces.php" class="btn btn-secondary mb-3">← Retour au site</a>
  <a href="../logout.php" class="btn btn-danger mb-3 float-end">Déconnexion</a>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Titre</th>
          <th>Prix</th>
          <th>Ville</th>
          <th>Propriétaire</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($annonces as $a): ?>
          <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['titre']) ?></td>
            <td><?= number_format($a['prix'], 0, ',', ' ') ?> €</td>
            <td><?= htmlspecialchars($a['ville']) ?></td>
            <td><?= $a['prenom'] ?? 'Anonyme' ?></td>
            <td><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
            <td>
              <a href="../annonce-detail.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-info text-white">Voir</a>
              <a href="index.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette annonce ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>