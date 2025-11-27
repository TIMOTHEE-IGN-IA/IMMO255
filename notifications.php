<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: annonces.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupère les notifications
$stmt = $pdo->prepare("
    SELECT n.*, a.id AS annonce_id, a.titre AS annonce_titre
    FROM notifications n
    LEFT JOIN annonces a ON n.annonce_id = a.id
    WHERE n.user_id = :user_id OR n.user_id = 0
    ORDER BY n.created_at DESC
    LIMIT 50
");
$stmt->execute(['user_id' => $user_id]);
$notifications = $stmt->fetchAll();

// Marque comme lues
$pdo->prepare("UPDATE notifications SET lu = 1 WHERE user_id = ? AND lu = 0")->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications - IMMO CI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .notification-item { cursor:pointer; transition:0.3s; border-radius:12px; }
        .notification-item:hover { background:#f0f8ff; transform:scale(1.02); }
        .unread { background:#e8f5e8; border-left:5px solid #28a745; font-weight:bold; }
        .unread::after { content:'•'; color:#28a745; font-size:1.8rem; position:absolute; right:15px; top:15px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4" href="annonces.php">IMMO CI</a>
        <a href="annonces.php" class="btn btn-outline-light">Retour</a>
    </div>
</nav>

<div class="container my-5">
    <h1 class="text-center mb-5 display-5 fw-bold text-primary">
        Vos Notifications
    </h1>

    <?php if (empty($notifications)): ?>
        <div class="text-center py-5">
            <h3 class="text-muted">Aucune notification</h3>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($notifications as $notif): ?>
                <?php if ($notif['annonce_id']): ?>
                    <a href="annonce_detail.php?id=<?= $notif['annonce_id'] ?>"
                       class="list-group-item list-group-item-action notification-item p-4 rounded mb-3 <?= !$notif['lu'] ? 'unread' : '' ?>">
                        <h5 class="fw-bold text-primary"><?= htmlspecialchars($notif['titre']) ?></h5>
                        <p><?= htmlspecialchars($notif['message']) ?></p>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></small>
                    </a>
                <?php else: ?>
                    <div class="list-group-item p-4 bg-light rounded mb-3">
                        <h5 class="text-info"><?= htmlspecialchars($notif['titre']) ?></h5>
                        <p><?= htmlspecialchars($notif['message']) ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>