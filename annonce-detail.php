<?php
require 'config.php';
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.prenom, u.telephone FROM annonces a LEFT JOIN users u ON a.user_id = u.id WHERE a.id = ?");
$stmt->execute([$id]);
$annonce = $stmt->fetch();

$stmt = $pdo->prepare("SELECT photo_path FROM annonce_photos WHERE annonce_id = ?");
$stmt->execute([$id]);
$photos = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($annonce['titre']) ?> - ImmoLux</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <div class="row">
    <div class="col-lg-8">
      <div id="carouselPhotos" class="carousel slide">
        <div class="carousel-inner">
          <?php foreach ($photos as $i => $photo): ?>
            <div class="carousel-item <?= $i===0 ? 'active' : '' ?>">
              <img src="<?= $photo ?>" class="d-block w-100" style="max-height:500px; object-fit:cover;">
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" data-bs-target="#carouselPhotos" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" data-bs-target="#carouselPhotos" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
      </div>
    </div>
    <div class="col-lg-4">
      <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
      <h3 class="text-primary"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</h3>
      <p><strong>Type :</strong> <?= $annonce['type_bien'] ?> - <?= ucfirst($annonce['transaction']) ?></p>
      <p><strong>Surface :</strong> <?= $annonce['surface'] ?> m²</p>
      <p><strong>Localisation :</strong> <?= htmlspecialchars($annonce['ville']) ?> (<?= $annonce['cp'] ?>)</p>
      <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
      <hr>
      <p><strong>Contact :</strong> <?= $annonce['prenom'] ?? 'Anonyme' ?> - <?= $annonce['telephone'] ?? 'Non communiqué' ?></p>
      <a href="contact.html" class="btn btn-primary btn-lg">Contacter le vendeur</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>