<?php
// public/index.php – UNIQUE POINT D'ENTRÉE

require '../config.php';

// Nettoyer l'URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/immobilier-app/public', '', $uri); // si tu es en sous-dossier

// Routes simples
if ($uri === '/' || $uri === '/index.php' || empty($uri)) {
    require '../views/home/index.php';
    exit;
}

if ($uri === '/annonces' || $uri === '/annonces.php') {
    require '../views/annonces/index.php';
    exit;
}

if ($uri === '/logout') {
    session_destroy();
    header("Location: " . BASE_URL . "/public");
    exit;
}

// 404
http_response_code(404);
echo "<h1 style='text-align:center;margin:100px;'>404 - Page non trouvée</h1>";
echo "<p style='text-align:center;'><a href='" . BASE_URL . "/public'>Retour à l'accueil</a></p>";
?>