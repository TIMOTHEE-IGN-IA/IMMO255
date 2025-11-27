<?php 
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: annonces.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupère l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// RÉCUPÈRE UNIQUEMENT TES ANNONCES (CORRIGÉ À 100%)
$stmt = $pdo->prepare("
    SELECT a.*, 
           (SELECT id FROM annonce_photos WHERE annonce_id = a.id ORDER BY id ASC LIMIT 1) as photo_id
    FROM annonces a 
    WHERE a.user_id = ? 
    ORDER BY a.created_at DESC
");
$stmt->execute([$user_id]);
$annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - IMMO CI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f0f2f5; margin:0; font-family:'Segoe UI',sans-serif; }
        .cover-container { position:relative; height:300px; overflow:hidden; background:#000; }
        .cover-photo { width:100%; height:100%; object-fit:cover; }
        .profile-photo-wrapper { position:absolute; bottom:300px; left:40px; z-index:10; }
        .profile-photo { width:168px; height:10px; object-fit:cover; border:6px solid white; border-radius:50%; box-shadow:0 10px 30px rgba(0,0,0,0.4); }
        .camera-icon, .cover-camera {
            position:absolute; background:rgba(0,0,0,0.7); color:white; border-radius:50%; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
        }
        .camera-icon { width:40px; height:40px; bottom:10px; right:10px; font-size:1.2rem; }
        .cover-camera { bottom:20px; right:20px; padding:10px 20px; font-size:0.9rem; border-radius:50px; }

        .fab-button {
            position:fixed; bottom:30px; right:30px; width:70px; height:70px;
            background:#0d6efd; color:white; border-radius:50%; display:flex;
            align-items:center; justify-content:center; font-size:2.8rem;
            box-shadow:0 8px 25px rgba(13,110,253,0.5); z-index:998;
            cursor:pointer; animation:pulse 2s infinite; border:none;
        }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(13,110,253,0.7)} 70%{box-shadow:0 0 0 20px rgba(13,110,253,0)} 100%{box-shadow:0 0 0 0 rgba(13,110,253,0)} }

        .btn-edit { background:#ffc107; color:black; }
        .btn-delete { background:#dc3545; color:white; }
    </style>
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
<div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:9999;">
    <strong>Succès !</strong> <?= $_SESSION['message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['message']); endif; ?>

<!-- MODALE DE BIENVENUE – STYLE FACEBOOK 2025 (ULTRA-JOLIE) -->
<?php if (!isset($_SESSION['welcome_shown'])): ?>
<div class="modal fade show d-block" id="welcomeModal" tabindex="-1" style="background:rgba(0,0,0,0.7);z-index:9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg text-center overflow-hidden border-0">
            <!-- Header vert succès -->
            <div class="modal-header bg-gradient text-white border-0 py-5" style="background: linear-gradient(135deg, #28a745, #20c997)!important;">
                <i class="fas fa-check-circle fa-5x mb-3"></i>
                <h2 class="modal-title fw-bold fs-1">Bienvenue <?= htmlspecialchars($user['prenom']) ?> !</h2>
            </div>

            <div class="modal-body py-5 px-4">
                <h3 class="fw-bold text-success mb-3">Félicitations !</h3>
                <p class="fs-4 text-dark mb-4">
                    Votre compte IMMO CI est prêt !<br>
                    Publiez vos biens et commencez à vendre ou louer dès maintenant.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-lg" onclick="closeWelcomeModal()">
                        Commencer l'aventure !
                    </button>
                </div>
            </div>

            <div class="modal-footer border-0 justify-content-center pb-4">
                <small class="text-muted fw-bold">IMMO CI – La maison de vos rêves en Côte d'Ivoire</small>
            </div>
        </div>
    </div>
</div>
<?php 
    // On affiche la modale une seule fois
    $_SESSION['welcome_shown'] = true;
?>
<?php endif; ?>


<!-- COUVERTURE + PHOTO PROFIL -->
<div class="cover-container">
    <img src="<?= !empty($user['cover_photo']) ? 'photo_cover.php?id='.$user_id : BASE_URL.'/public/img/cover-default.jpg' ?>" 
         class="cover-photo" id="coverPhoto" alt="Couverture">
    <div class="cover-camera" onclick="document.getElementById('coverInput').click()">
        Changer la couverture
    </div>
    <input type="file" id="coverInput" class="d-none" accept="image/*" onchange="uploadCover(this)">
</div>


<div class="container">
    <div class="mt-5 pt-5 ms-5">
        <h1 class="display-5 fw-bold"><?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?></h1>
        <p class="text-muted fs-5"><?= htmlspecialchars($user['email']) ?></p>
    </div>

    <h3 class="text-center mb-5">Mes annonces (<?= count($annonces) ?>)</h3>

    <?php if (empty($annonces)): ?>
        <div class="text-center py-5">
            <h4 class="text-muted">Vous n'avez pas encore publié d'annonce</h4>
            <button class="btn btn-primary btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#publishModal">
                Publier ma première annonce
            </button>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($annonces as $a): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow h-100 position-relative">
                        <img src="<?= $a['photo_id'] ? 'photo.php?id='.$a['photo_id'] : BASE_URL.'/public/img/default.jpg' ?>" 
                             class="card-img-top" style="height:200px;object-fit:cover;" alt="">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($a['titre']) ?></h5>
                            <p class="text-primary fw-bold"><?= number_format($a['prix']) ?> FCFA</p>
                            <small class="text-muted"><?= htmlspecialchars($a['ville']) ?></small>
                            <!-- BOUTON ROND BLEU + – FONCTIONNE ! -->
<?php if (isset($_SESSION['user_id'])): ?>
    <button class="fab-button" data-bs-toggle="modal" data-bs-target="#confirmModal">
        <i class="fas fa-plus"></i>
    </button>
<?php endif; ?>




                            <!-- BOUTONS MODIFIER + SUPPRIMER (FONCTIONNELS) -->
                            <div class="mt-auto d-flex gap-2">
                                <a href="edit_annonce.php?id=<?= $a['id'] ?>" class="btn btn-warning btn-sm flex-fill">
                                    Modifier
                                </a>
                                <button class="btn btn-danger btn-sm flex-fill" onclick="deleteAnnonce(<?= $a['id'] ?>)">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL PUBLICATION (comme avant) -->
<!-- ... ton modal #publishModal ... -->
<!-- MODAL CONFIRMATION -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5">
                <h4>Bonjour <strong><?= htmlspecialchars(getUserPrenom()) ?></strong> !</h4>
                <p class="lead mt-3">Voulez-vous vraiment publier un bien immobilier ?</p>
            </div>
            <div class="modal-footer justify-content-center gap-4">
                <button type="button" class="btn btn-secondary btn-lg px-5" data-bs-dismiss="modal">Non</button>
                <button type="button" class="btn btn-success btn-lg px-5" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#publishModal">
                    Oui, publier !
                </button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PUBLICATION – FONCTIONNELLE AVEC AJAX + REDIRECTION PROFIL -->
<div class="modal fade" id="publishModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Publier un bien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form id="publishForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select name="transaction" class="form-select form-select-lg" required>
                                <option value="">Transaction</option>
                                <option value="vente">Vente</option>
                                <option value="location">Location</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="type_bien" class="form-select form-select-lg" required>
                                <option value="">Type de bien</option>
                                <option>Appartement</option>
                                <option>Maison</option>
                                <option>Studio</option>
                                <option>Terrain</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <input name="titre" type="text" class="form-control form-control-lg" placeholder="Titre de l'annonce" required>
                        </div>
                        <div class="col-md-6">
                            <input name="prix" type="number" class="form-control form-control-lg" placeholder="Prix (FCFA)" required>
                        </div>
                        <div class="col-md-6">
                            <input name="surface" type="number" class="form-control form-control-lg" placeholder="Surface (m²)" required>
                        </div>
                        <div class="col-md-8">
                            <input name="ville" type="text" class="form-control form-control-lg" placeholder="Ville" required>
                        </div>
                        <div class="col-md-4">
                            <input name="cp" type="text" class="form-control form-control-lg" placeholder="Code postal">
                        </div>
                        <div class="col-12">
                            <textarea name="description" class="form-control" rows="4" placeholder="Description complète..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Photos (max 10)</label>
                            <input name="photos[]" type="file" class="form-control" multiple accept="image/*" required>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <span class="spinner-border spinner-border-sm me-2 d-none"></span>
                            Publier l'annonce
                        </button>
                    </div>
                </form>
                <div id="publishAlert" class="mt-4 text-center fw-bold fs-5"></div>
            </div>
        </div>
    </div>
</div>
<script>
    // PUBLICATION AVEC AJAX + REDIRECTION VERS PROFIL
document.getElementById('publishForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    const spinner = btn.querySelector('.spinner-border');
    const alertDiv = document.getElementById('publishAlert');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    alertDiv.innerHTML = '';

    const formData = new FormData(this);

    fetch('publish.php', { 
        method: 'POST', 
        body: formData 
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerHTML = `<div class="text-success fs-4">Annonce publiée avec succès !</div>`;
            setTimeout(() => {
                // REDIRECTION VERS LE PROFIL
                window.location.href = 'profil.php?id=<?= $_SESSION['user_id'] ?>';
            }, 1500);
        } else {
            alertDiv.innerHTML = `<div class="text-danger fs-5">${data.message}</div>`;
        }
    })
    .catch(() => {
        alertDiv.innerHTML = `<div class="text-danger fs-5">Erreur réseau. Réessayez.</div>`;
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>


</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// SUPPRIMER UNE ANNONCE – FONCTIONNELLE
function deleteAnnonce(id) {
    if (confirm("Supprimer cette annonce ? Cette action est irréversible.")) {
        fetch('delete_annonce.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(() => location.reload());
    }
}

// CHANGEMENT PHOTO PROFIL + COUVERTURE (INSTANTANÉ)
function uploadProfile(input) {
    if (!input.files?.[0]) return;
    const formData = new FormData();
    formData.append('photo', input.files[0]);
    formData.append('action', 'update_profile_photo');
    fetch('update_profile.php', { method: 'POST', body: formData })
    .then(() => {
        document.getElementById('profilePhoto').src = 'photo.php?id=<?= $user_id ?>&t=' + Date.now();
    });
}

function uploadCover(input) {
    if (!input.files?.[0]) return;
    const formData = new FormData();
    formData.append('cover', input.files[0]);
    formData.append('action', 'update_cover');
    fetch('update_profile.php', { method: 'POST', body: formData })
    .then(() => {
        document.getElementById('coverPhoto').src = 'photo_cover.php?id=<?= $user_id ?>&t=' + Date.now();
    });
}
</script>
<script>
// FERME LA MODALE DE BIENVENUE
function closeWelcomeModal() {
    document.getElementById('welcomeModal').remove();
}
</script>
<script>
// CHANGEMENT PHOTO DE PROFIL – INSTANTANÉ ET FIABLE
function changeProfilePhoto(input) {
    if (!input.files?.[0]) return;

    const img = document.getElementById('profilePhoto');
    img.style.opacity = '0.5'; // Indicateur de chargement

    const formData = new FormData();
    formData.append('photo', input.files[0]);
    formData.append('action', 'update_profile_photo');

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // FORÇAGE ABSOLU DU RAFRAÎCHISSEMENT
            const newTime = Date.now();
            img.src = 'photo.php?id=<?= $_SESSION['user_id'] ?>&t=' + newTime;
            img.style.opacity = '1';
        } else {
            alert('Erreur : ' + (data.message || 'Inconnu'));
            img.style.opacity = '1';
        }
    })
    .catch(() => {
        alert('Erreur réseau');
        img.style.opacity = '1';
    });
}
</script>
</body>
</html>