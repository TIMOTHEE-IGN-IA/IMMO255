<?php
// publish.php → VERSION QUI NE PLANTE JAMAIS
session_start();
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Obligatoire
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Connectez-vous']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST requis']);
    exit;
}

// LE PLUS IMPORTANT : nettoyage du prix (25 000 000 → 25000000)
$prix_raw = $_POST['prix'] ?? '0';
$prix_raw = str_replace([' ', ','], ['', '.'], $prix_raw);
$prix = floatval($prix_raw);

$surface = intval(preg_replace('/[^0-9]/', '', $_POST['surface'] ?? '0'));

$titre       = trim($_POST['titre'] ?? '');
$transaction = $_POST['transaction'] ?? '';
$type_bien   = $_POST['type_bien'] ?? '';
$ville       = trim($_POST['ville'] ?? '');
$cp          = trim($_POST['cp'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validation finale
if (empty($titre) || empty($transaction) || empty($type_bien) || $prix <= 0 || $surface <= 0 || empty($ville)) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants ou prix/surface invalides']);
    exit;
}
if (empty($_FILES['photos']['name'][0])) {
    echo json_encode(['success' => false, 'message' => 'Au moins une photo requise']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Création annonce
    $stmt = $pdo->prepare("INSERT INTO annonces (user_id, transaction, type_bien, titre, prix, surface, ville, cp, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $transaction, $type_bien, $titre, $prix, $surface, $ville, $cp, $description]);
    $annonce_id = $pdo->lastInsertId();

    // 2. Photos (stockées en base → zéro problème de droits)
    $uploaded = 0;
    $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
    foreach ($_FILES['photos']['tmp_name'] as $k => $tmp) {
        if ($uploaded >= 10) break;
        if ($_FILES['photos']['error'][$k] !== 0) continue;
        if (!in_array($_FILES['photos']['type'][$k], $allowed)) continue;
        if ($_FILES['photos']['size'][$k] > 10*1024*1024) continue;

        $data = file_get_contents($tmp);
        if ($data !== false) {
            $stmt = $pdo->prepare("INSERT INTO annonce_photos (annonce_id, photo_path, photo_type) VALUES (?, ?, ?)");
            $stmt->execute([$annonce_id, $data, $_FILES['photos']['type'][$k]]);
            $uploaded++;
        }
    }

    if ($uploaded == 0) throw new Exception("Aucune photo valide");

    // 3. Notification
    $pdo->prepare("INSERT INTO notifications (user_id, titre, message, annonce_id, lu, created_at) VALUES (0, ?, ?, ?, 0, NOW())")
        ->execute(["Nouvelle annonce", "$transaction • ".number_format($prix)." FCFA • $ville", $annonce_id]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Annonce publiée !']);
    // Exemple après $pdo->lastInsertId() qui retourne l'ID de l'annonce créée
$nouvelle_annonce_id = $pdo->lastInsertId();
$titre_annonce = $_POST['titre']; // ou ce que tu utilises
$message = "Nouvelle annonce : " . substr($titre_annonce, 0, 50) . "...";

// Insérer une notification pour TOUS les utilisateurs (ou ciblée)
$sql = "INSERT INTO notifications (user_id, annonce_id, titre, message, lu) 
        VALUES (0, :annonce_id, :titre, :message, 0)"; // user_id = 0 pour "tous"

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':annonce_id' => $nouvelle_annonce_id,
    ':titre' => $titre_annonce,
    ':message' => $message
]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur : '.$e->getMessage()]);
}
?>