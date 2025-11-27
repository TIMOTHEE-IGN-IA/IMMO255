<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>IMMO CI - Location & Vente en Côte d'Ivoire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Heebo', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Montserrat', sans-serif; }
        .btn-warning { background: #ffc107; border: none; font-weight: 700; }
        .btn-warning:hover { background: #e0a800; }
        .carousel-item img { object-fit: cover; height: 90vh; width: 100%; }
        .back-to-top {
            position: fixed; bottom: 30px; right: 30px; z-index: 99;
            width: 50px; height: 50px; background: #0d6efd; color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .user-avatar { width: 40px; height: 40px; object-fit: cover; border: 3px solid white; }
        .online-dot { width: 12px; height: 12px; background: #28a745; border: 2px solid white; bottom: -2px; right: -2px; }
    </style>
</head>
<body>
<div class="container-xxl bg-white p-0">

    <!-- Header -->
    <div class="container-fluid bg-dark px-0">
        <div class="row gx-0">
            <div class="col-lg-3 bg-dark d-none d-lg-block">
                <a href="index.html" class="navbar-brand w-100 h-100 m-0 p-0 d-flex align-items-center justify-content-center">
                    <h1 class="m-0 text-primary text-uppercase"><b>IMMO CI</b></h1>
                </a>
            </div>
            <div class="col-lg-9">
                <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-3 p-lg-0">
                    <a href="index.html" class="navbar-brand d-block d-lg-none">
                        <h1 class="m-0 text-primary text-uppercase"><b>IMMO CI</b></h1>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav py-0">
                            <a href="index.html" class="nav-item nav-link active">Accueil</a>
                        </div>
                        <a href="#" class="btn btn-primary rounded-0 py-4 px-md-5 d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#authModal">
                            Voir les annonces <i class="fa fa-arrow-right ms-3"></i>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Carousel + Bouton -->
    <div class="position-relative" style="height: 90vh;">
        <div id="header-carousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel" data-bs-interval="4500">
            <div class="carousel-inner h-100">
                <div class="carousel-item active h-100">
                    <img src="img/carousel-1.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="Abidjan">
                </div>
                <div class="carousel-item h-100">
                    <img src="img/carousel-2.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="Cocody">
                </div>
            </div>
        </div>

        <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100 px-3" style="z-index:10;">
            <div class="container">
                <h3 class="display-2 fw-bold mb-4 animated slideInDown">
                    Votre logement <br>vous attend ici
                </h3>
                <p class="fs-3 mb-5 animated slideInDown">
                    Appartements • Maisons • Studios • Villas<br>
                    Abidjan • Yamoussoukro • San Pedro • Bouaké • Toute la Côte d'Ivoire
                </p>
                <button type="button" class="btn btn-warning btn-lg px-5 py-3 text-dark fw-bold animated slideInLeft" data-bs-toggle="modal" data-bs-target="#authModal">
                   👉 <a href="http://localhost/immobilier-app/annonce">Cliquez ici pour Voir les biens publiés
                </button></a>
            </div>
        </div>
    </div>

    <!-- Section Bienvenue -->
    <div class="container py-5 bg-white">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h1 class="mb-4">Bienvenue sur <span class="text-primary text-uppercase">IMMO CI</span></h1>
            <p class="lead mb-5">La plateforme n°1 pour louer ou vendre votre bien en Côte d'Ivoire</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h4 class="text-white mb-4">IMMO CI</h4>
                    <p>Trouve ou publie ton logement facilement et gratuitement partout en Côte d'Ivoire.</p>
                </div>
                <div class="col-lg-4">
                    <h4 class="text-white mb-4">Liens rapides</h4>
                    <a class="btn btn-link text-light" href="#">À propos</a>
                    <a class="btn btn-link text-light" href="#">Contact</a>
                </div>
                <div class="col-lg-4">
                    <h4 class="text-white mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>contact@immoci.ci</p>
                </div>
            </div>
        </div>
        <div class="container text-center py-4">
            &copy; 2025 IMMO CI - Tous droits réservés
        </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">
        <i class="bi bi-arrow-up"></i>
    </a>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>

</body>
</html>