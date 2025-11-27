<?php 
session_start();
require 'config.php';

// Récupère les annonces
$stmt = $pdo->query("
    SELECT a.*, u.prenom,
           (SELECT id FROM annonce_photos WHERE annonce_id = a.id ORDER BY id ASC LIMIT 1) as photo_id
    FROM annonces a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC
");
$annonces = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces - IMMO CI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; margin:0; font-family:'Segoe UI',sans-serif; }
        .user-avatar { width:48px; height:48px; object-fit:cover; border:3px solid white; }
        .online-dot { position:absolute; bottom:0; right:0; width:16px; height:16px; background:#28a745; border:3px solid white; border-radius:50%; }
        .card-img-top { height:250px; object-fit:cover; }

        .hero-video { position:relative; height:35vh; overflow:hidden; background:#000; }
        .hero-video video { position:absolute; top:50%; left:50%; min-width:100%; min-height:100%; width:auto; height:auto; transform:translate(-50%,-50%); object-fit:cover; }
        .hero-overlay { position:absolute; inset:0; background:linear-gradient(to bottom,rgba(0,0,0,0.7),rgba(0,0,0,0.4)); }
        .hero-title { font-size:4.5rem; font-weight:900; text-shadow:0 8px 20px rgba(0,0,0,0.9); }

        .fab-button {
            position:fixed; bottom:30px; right:30px; width:70px; height:70px;
            background:#0d6efd; color:white; border-radius:50%; display:flex;
            align-items:center; justify-content:center; font-size:2.8rem;
            box-shadow:0 8px 25px rgba(13,110,253,0.5); z-index:998;
            cursor:pointer; animation:pulse 2s infinite; border:none;
        }
        @keyframes pulse { 0%{box-shadow:0 0 0 0 rgba(13,110,253,0.7)} 70%{box-shadow:0 0 0 20px rgba(13,110,253,0)} 100%{box-shadow:0 0 0 0 rgba(13,110,253,0)} }

        .dropdown-toggle::after { display:none !important; } /* Cache la flèche Bootstrap */
        .dropdown-menu { border-radius:15px !important; }
    </style>
</head>
<body>


<!-- NAVBAR – PHOTO + MENU AU CLIC (FONCTIONNE !) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
    <div class="container-fluid">
        <!-- TON LOGO À GAUCHE – SUPERBE ET PRO -->
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
            <img src="<?= BASE_URL ?>/public/img/logo.png" 
                 alt="IMMO CI" 
                 height="60" 
                 class="me-3 rounded shadow-lg"
                 style="border: 4px solid white; object-fit: contain;">
            
        </a>

        <!-- PARTIE DROITE -->
        <div class="d-flex align-items-center gap-4">
<!-- ICÔNE CLOCHE + BADGE ROUGE (FONCTIONNE PARFAITEMENT) -->
<a href="notifications.php" class="text-white position-relative me-4">
    <i class="fas fa-bell fa-2x"></i>
    <?php
    $notif_count = 0;
    try {
        // Compte les notifications non lues de l'utilisateur + les globales
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE (user_id = ? OR user_id = 0) AND lu = 0
        ");
        $stmt->execute([$_SESSION['user_id'] ?? 0]);
        $notif_count = (int)$stmt->fetchColumn();
    } catch (Exception $e) {}
    ?>
    <?php if ($notif_count > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate__animated animate__pulse animate__infinite" 
              style="font-size:0.8rem; animation-duration:1.5s;">
            <?= $notif_count > 99 ? '99+' : $notif_count ?>
            <span class="visually-hidden">notifications</span>
        </span>
    <?php endif; ?>
</a>

        <div class="d-flex align-items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- PHOTO RONDE + MENU AU CLIC -->
                <div class="dropdown">
                    <a href="#" class="d-block text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="position-relative">
                            <img src="<?= getUserAvatar() ?>" 
                                 class="rounded-circle border border-4 border-white shadow" 
                                 width="56" height="56" 
                                 alt="Profil" 
                                 style="object-fit:cover;">
                            <span class="position-absolute bottom-0 end-0 translate-middle bg-success rounded-circle" 
                                  style="width:18px;height:18px;border:4px solid white;"></span>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2">
                        <li class="dropdown-header fw-bold text-primary"><?= htmlspecialchars(getUserPrenom()) ?></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="profil.php"><i class="fas fa-user me-3"></i> Mon profil</a></li>
                        <li><a class="dropdown-item py-2" href="mes_annonces.php"><i class="fas fa-home me-3"></i> Mes annonces</a></li>
                        <li><a class="dropdown-item py-2" href="notifications.php"><i class="fas fa-bell me-3"></i> Mes notifications</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-3"></i> Déconnexion</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">Inscription</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- BOUTON ROND BLEU + – FONCTIONNE ! -->
<?php if (isset($_SESSION['user_id'])): ?>
    <button class="fab-button" data-bs-toggle="modal" data-bs-target="#confirmModal">
        <i class="fas fa-plus"></i>
    </button>
<?php endif; ?>

<!-- VIDÉO + TITRE -->
<div class="hero-video position-relative">
    <video autoplay muted loop playsinline class="w-100">
        <source src="<?= BASE_URL ?>/public/video/welcome1.mp4" type="video/mp4">
    </video>
    <video autoplay muted loop playsinline class="w-100">
        <source src="<?= BASE_URL ?>/public/video/welcome2.mp4" type="video/mp4">
    </video>


    <div class="hero-overlay"></div>
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100">
        <h1 class="hero-title display-3 fw-bold">IMMO CI </h1><font size="3" color="orange"> Meilleur plateforme </font><font size="3" color="white">  Immobilière en</font> <font size="3" color="GREEN"> Côte D'ivoire 🌍 </font>
    </div>
</div>

<!-- FILTRE + ANNONCES -->
<div class="container my-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card shadow p-4">
                <form class="row g-3">
                    <div class="col-md-4"><input type="text" class="form-control form-control-lg" placeholder="Ville" id="ville" onkeyup="filtrerAnnonces()"></div>
                    <div class="col-md-4">
                        <select class="form-select form-control-lg" id="type" onchange="filtrerAnnonces()">
                            <option value="">Type de bien</option>
                            <option>Appartement</option><option>Maison</option><option>Studio</option><option>Terrain</option>
                        </select>
                    </div>
                    <div class="col-md-4"><input type="number" class="form-control form-control-lg" placeholder="Prix max" id="prix_max" onkeyup="filtrerAnnonces()"></div>
                </form>
            </div>
        </div>
    </div>

    <div id="annoncesContainer" class="row g-4">
        <?php foreach ($annonces as $a): ?>
            <div class="col-md-6 col-lg-4 annonce-item"
                 data-ville="<?= strtolower($a['ville']) ?>"
                 data-type="<?= strtolower($a['type_bien'] ?? '') ?>"
                 data-prix="<?= $a['prix'] ?>">
                <div class="card shadow h-100 border-0">
                    <img src="<?= $a['photo_id'] ? 'photo.php?id='.$a['photo_id'] : BASE_URL.'/public/img/default.jpg' ?>"
                         class="card-img-top" alt="<?= htmlspecialchars($a['titre']) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($a['titre']) ?></h5>
                        <p class="text-muted"><i class="fas fa-map-marker-alt text-primary"></i> <?= htmlspecialchars($a['ville']) ?></p>
                        <p class="fs-4 fw-bold text-primary"><?= number_format($a['prix']) ?> FCFA</p>
                        <a href="annonce.php?id=<?= $a['id'] ?>" class="btn btn-primary mt-auto">Voir l'annonce</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

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
<!-- MODAL CONNEXION – FONCTIONNELLE -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Connexion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form id="loginForm">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Mot de passe" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <span class="spinner-border spinner-border-sm me-2 d-none"></span>
                        Se connecter
                    </button>
                    <div id="loginAlert" class="mt-3 text-center fw-bold"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INSCRIPTION – CONNEXION AUTOMATIQUE APRÈS INSCRIPTION -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Inscription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <form id="registerForm" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-6"><input name="prenom" type="text" class="form-control form-control-lg" placeholder="Prénom" required></div>
                        <div class="col-6"><input name="nom" type="text" class="form-control form-control-lg" placeholder="Nom" required></div>
                        <div class="col-12"><input name="email" type="email" class="form-control form-control-lg" placeholder="Email" required></div>
                        <div class="col-12"><input name="telephone" type="tel" class="form-control form-control-lg" placeholder="Téléphone"></div>
                        <div class="col-12"><input name="password" type="password" class="form-control form-control-lg" placeholder="Mot de passe" required></div>
                        <div class="col-12"><input name="confirm" type="password" class="form-control form-control-lg" placeholder="Confirmer mot de passe" required></div>
                        <div class="col-12"><input name="photo" type="file" class="form-control form-control-lg" accept="image/*"></div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4">
                        <span class="spinner-border spinner-border-sm me-2 d-none"></span>
                        S'inscrire & Connecter
                    </button>
                    <div id="registerAlert" class="mt-3 text-center fw-bold fs-5"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// CONNEXION
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    const spinner = btn.querySelector('.spinner-border');
    const alert = document.getElementById('loginAlert');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    alert.innerHTML = '';

    const formData = new FormData(this);
    formData.append('action', 'login');

    fetch('auth.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert.innerHTML = '<div class="text-success">Connexion réussie ! Redirection...</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            alert.innerHTML = `<div class="text-danger">${data.message}</div>`;
        }
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});

// INSCRIPTION
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    const spinner = btn.querySelector('.spinner-border');
    const alert = document.getElementById('registerAlert');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    alert.innerHTML = '';

    const formData = new FormData(this);
    formData.append('action', 'register');

    fetch('auth.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert.innerHTML = '<div class="text-success">Inscription réussie ! Connexion...</div>';
            setTimeout(() => location.reload(), 1500);
        } else {
            alert.innerHTML = `<div class="text-danger">${data.message}</div>`;
        }
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>

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


<script>
// FILTRE EN TEMPS RÉEL
function filtrerAnnonces() {
    const ville = document.getElementById('ville').value.toLowerCase().trim();
    const type = document.getElementById('type').value.toLowerCase();
    const prixMax = parseInt(document.getElementById('prix_max').value) || Infinity;

    document.querySelectorAll('.annonce-item').forEach(item => {
        const v = item.dataset.ville;
        const t = item.dataset.type;
        const p = parseInt(item.dataset.prix);

        const match = v.includes(ville) && (!type || t === type) && p <= prixMax;
        item.style.display = match ? 'block' : 'none';
    });
}
document.getElementById('ville').addEventListener('keyup', filtrerAnnonces);
document.getElementById('type').addEventListener('change', filtrerAnnonces);
document.getElementById('prix_max').addEventListener('keyup', filtrerAnnonces);
</script>
<script>
// INSCRIPTION + CONNEXION AUTOMATIQUE (SANS RECHARGER NI CLIQUER SUR CONNEXION)
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = this.querySelector('button');
    const spinner = btn.querySelector('.spinner-border');
    const alertDiv = document.getElementById('registerAlert');

    btn.disabled = true;
    spinner.classList.remove('d-none');
    alertDiv.innerHTML = '';

    const formData = new FormData(this);
    formData.append('action', 'register_and_login'); // On change l'action

    fetch('auth.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertDiv.innerHTML = '<div class="text-success fs-4">Inscription réussie ! Bienvenue 😊</div>';
            // FERME LA MODALE + RECHARGE LA PAGE → L'UTILISATEUR EST CONNECTÉ
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('registerModal')).hide();
                location.reload(); // Recharge → navbar montre photo + menu
            }, 1500);
        } else {
            alertDiv.innerHTML = `<div class="text-danger">${data.message}</div>`;
        }
    })
    .catch(() => {
        alertDiv.innerHTML = '<div class="text-danger">Erreur réseau. Réessayez.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>
</body>
</html>