<?php
require 'config.php';
if ($_POST['id']) {
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}
?>