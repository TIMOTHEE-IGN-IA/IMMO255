<?php
require 'config.php';

if ($_POST['annonce_id']) {
    $annonce_id = (int)$_POST['annonce_id'];
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $tel = htmlspecialchars($_POST['telephone'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // 1. Enregistre la demande
    $stmt = $pdo->prepare("INSERT INTO demandes (annonce_id, nom, telephone, message, date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$annonce_id, $nom, $tel, $message]);

    // 2. Récupère le token FCM du propriétaire
    $stmt = $pdo->prepare("SELECT u.fcm_token FROM annonces a JOIN users u ON a.user_id = u.id WHERE a.id = ?");
    $stmt->execute([$annonce_id]);
    $token = $stmt->fetchColumn();

    // 3. Envoie notification push via Firebase
    if ($token && strlen($token) > 50) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            "to" => $token,
            "notification" => [
                "title" => "Nouvelle demande !",
                "body" => "$nom souhaite visiter votre bien",
                "icon" => "https://immoci.com/icon.png",
                "click_action" => "https://immoci.com/annonce_detail.php?id=$annonce_id"
            ],
            "data" => [
                "annonce_id" => $annonce_id,
                "type" => "demande"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=AAAA...TA_CLÉ_SERVEUR_FIREBASE_ICI...',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_exec($ch);
        curl_close($ch);
    }

    echo "OK";
}
?>