<?php
// routes.php – VERSION FINALE QUI MARCHE À 100%

require_once 'config/database.php'; // ta connexion PDO


class Router
{
    public static function run()
    {
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');

        // Accueil
        if ($url === '' || $url === 'accueil') {
            $title = "Accueil - Immo225 Côte d'Ivoire";
            ob_start();
            include VIEWS_PATH . '/home/index.php';
            $content = ob_get_clean();
            include VIEWS_PATH . '/layouts/main.php';
            return;
        }

        // Liste des biens
        if ($url === 'biens') {
            $controller = new BienController();
            $controller->index();
            return;
        }

        // Détail d'un bien : /bien/12
        if (preg_match('#^bien/(\d+)$#', $url, $matches)) {
            $controller = new BienController();
            $controller->detail((int)$matches[1]);
            return;
        }

        // Ajouter une annonce
        if ($url === 'ajouter-annonce') {
            $controller = new BienController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->store();
            } else {
                $controller->create();
            }
            return;
        }

        // Mes biens (connecté uniquement)
        if ($url === 'mes-biens') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
            $controller = new BienController();
            $controller->mesBiens();
            return;
        }

        // 404
        http_response_code(404);
        $title = "Page non trouvée";
        $content = "<div class='container py-5 text-center'>
                        <h1 class='display-1 fw-bold text-danger'>404</h1>
                        <p class='lead'>Page introuvable</p>
                        <a href='" . BASE_URL . "' class='btn btn-primary btn-lg mt-4'>Retour accueil</a>
                    </div>";
        include VIEWS_PATH . '/layouts/main.php';
    }
}