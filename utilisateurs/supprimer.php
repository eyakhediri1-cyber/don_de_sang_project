<?php
require_once '../config/connexion.php';
require_once '../config/auth.php';
verifierConnexion();
verifierRole('ADMIN');

if (!isset($_GET['id'])) {
    header("Location: liste.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id]);

header("Location: liste.php");
exit;
?>
