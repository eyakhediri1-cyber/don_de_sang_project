<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

$id = $_GET["id"] ?? null;
if (!$id) die("ID invalide !");

// Vérifier si le donneur existe
$stmt = $pdo->prepare("SELECT * FROM donneurs WHERE id_donneur = ?");
$stmt->execute([$id]);
$donneur = $stmt->fetch();

if (!$donneur) {
    die("Donneur introuvable !");
}

// Suppression définitive
$delete = $pdo->prepare("DELETE FROM donneurs WHERE id_donneur = ?");
$delete->execute([$id]);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Suppression Donneur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

    <div class="alert alert-danger">
        <strong>Donneur supprimé :</strong>
        <?= htmlspecialchars($donneur["nom"] . " " . $donneur["prenom"]) ?>
    </div>

    <a href="liste_donneur.php" class="btn btn-primary">Retour à la liste</a>

</div>
</body>
</html>
