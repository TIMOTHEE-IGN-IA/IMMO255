<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: annonces.php');
    exit;
}

$id = (int)$_GET['id'];

// Récupère l'annonce + propriétaire
$stmt = $pdo->prepare("
    SELECT a.*, u.prenom, u.nom, u.email as proprio_email, u.telephone,
           (SELECT id FROM annonce_photos WHERE annonce_id = a.id ORDER BY id ASC LIMIT 1) as photo_id
    FROM annonces a
    LEFT JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    die("Annonce non trouvée");
}

// Récupère toutes les photos
$stmt = $pdo->prepare("SELECT id FROM annonce_photos WHERE annonce_id = ? ORDER BY id");
$stmt->execute([$id]);
$photos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($annonce['titre']) ?> - IMMO CI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; }
        .cover-photo { height:500px; object-fit:cover; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
        .profile-pic { width:120px; height:120px; object-fit:cover; border:5px solid white; border-radius:50%; box-shadow:0 8px 25px rgba(0,0,0,0.3); }
        .btn-interest { 
            background: linear-gradient(135deg, #ff6b6b, #ff8e53); 
            color:white; 
            font-weight:bold; 
            border:none;
            transition:0.3s;
        }
        .btn-interest:hover { 
            transform:scale(1.05); 
            box-shadow:0 10px 30px rgba(255,107,107,0.4);
        }
        .card { border-radius:20px; overflow:hidden; }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row g-4">
        <!-- Photo principale -->
        <div class="col-lg-8">
            <img src="<?= $annonce['photo_id'] ? 'photo.php?id='.$annonce['photo_id'] : BASE_URL.'/public/img/default.jpg' ?>"
                 class="img-fluid cover-photo w-100" alt="<?= htmlspecialchars($annonce['titre']) ?>">
        </div>

        <!-- Infos + propriétaire -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body text-center p-5">
                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($annonce['titre']) ?></h2>
                    <p class="fs-2 text-primary fw-bold mb-2"><?= number_format($annonce['prix']) ?> FCFA</p>
                    <p class="fs-5 text-muted mb-4">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        <?= htmlspecialchars($annonce['ville']) ?>
                    </p>

                    <hr class="my-4">

                    <!-- Propriétaire -->
                    <div class="d-flex align-items-center justify-content-center mb-4">
                        <img src="photo.php?id=<?= $annonce['user_id'] ?>"
                             class="profile-pic me-3" alt="Propriétaire">
                        <div class="text-start">
                            <h5 class="mb-0 fw-bold"><?= htmlspecialchars($annonce['prenom'].' '.$annonce['nom']) ?></h5>
                            <small class="text-muted">Propriétaire</small>
                        </div>
                    </div>

                    <p class="text-dark mb-5"><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>

                    <!-- BOUTON JE SUIS INTÉRESSÉ -->
                    <button class="btn btn-interest btn-lg w-100 shadow-lg py-3" data-bs-toggle="modal" data-bs-target="#interestModal">
                        <i class="fas fa-heart me-2"></i> Je suis intéressé !
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INTÉRÊT – STYLE FACEBOOK -->
<div class="modal fade" id="interestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">
                    Contacter le propriétaire
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form id="interestForm">
                    <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                    <div class="mb-3">
                        <input type="text" name="nom" class="form-control form-control-lg" placeholder="Votre nom complet" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Votre email" required>
                    </div>
                    <div class="mb-3">
                        <input type="tel" name="telephone" class="form-control form-control-lg" placeholder="Votre téléphone" required>
                    </div>
                    <div class="mb-4">
                        <textarea name="message" class="form-control" rows="4" placeholder="Votre message au propriétaire..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill shadow">
                        <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                    </button>
                    <div id="interestAlert" class="mt-4 text-center fw-bold fs-5"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ENVOI DU MESSAGE AU PROPRIÉTAIRE
document.getElementById('interestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    const alertDiv = document.getElementById('interestAlert');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Envoi en cours...';
    alertDiv.innerHTML = '';

    const formData = new FormData(this);
    formData.append('action', 'send_interest');

    fetch('send_interest.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerHTML = '<div class="text-success fs-4"><i class="fas fa-check-circle me-2"></i>Message envoyé avec succès !</div>';
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('interestModal')).hide();
            }, 2000);
        } else {
            alertDiv.innerHTML = `<div class="text-danger fs-5">${data.message}</div>`;
        }
    })
    .catch(() => {
        alertDiv.innerHTML = '<div class="text-danger fs-5">Erreur réseau. Réessayez.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Envoyer le message';
    });
});
</script>
</body>
</html>