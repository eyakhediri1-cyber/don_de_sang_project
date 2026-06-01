<?php
require_once '../config/connexion.php'; 
require_once '../config/authentification.php';

// Protection : ADMIN ou SECRETAIRE
verifierConnexion();
verifierRole(['ADMIN', 'SECRETAIRE']);

// Récupération des donneurs
$donneurs = $pdo->query("SELECT id_donneur, nom, prenom, groupe_sanguin, rhesus FROM donneurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des centres
$centres = $pdo->query("SELECT id_centre, nom_centre FROM centres_collecte ORDER BY nom_centre")->fetchAll(PDO::FETCH_ASSOC);

$messages = [];

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_donneur = filter_input(INPUT_POST, 'id_donneur', FILTER_VALIDATE_INT);
    $id_centre  = filter_input(INPUT_POST, 'id_centre', FILTER_VALIDATE_INT);
    $date_don   = $_POST['date_don'] ?? '';
    $today      = date('Y-m-d');

    if (!$id_donneur || !$id_centre || !$date_don) {
        $messages[] = "<div class='alert alert-warning'>Veuillez remplir tous les champs correctement.</div>";
    } elseif ($date_don > $today) {
        $messages[] = "<div class='alert alert-warning'>La date du don ne peut pas être dans le futur.</div>";
    } else {
        try {
            // Insertion dans la table dons
            $sql = "INSERT INTO dons (id_donneur, id_centre, date_don, statut)
                    VALUES (:id_donneur, :id_centre, :date_don, 'EN STOCK')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_donneur' => $id_donneur,
                ':id_centre'  => $id_centre,
                ':date_don'   => $date_don
            ]);

            $messages[] = "<div class='alert alert-success'>Don ajouté avec succès et est maintenant en stock.</div>";
        } catch (PDOException $e) {
            $messages[] = "<div class='alert alert-danger'>Erreur lors de l'ajout : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ajouter un Don</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="styledons.css">
</head>
<body>

<div class="container main-container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-7">

            <div class="header-section text-center mb-4">
                <div class="blood-icon fs-1 text-danger"><i class="bi bi-droplet-fill"></i></div>
                <h2>Ajouter un Don</h2>
                <p class="text-muted">Enregistrez un nouveau don de sang et sauvez des vies</p>
            </div>

            <?php foreach ($messages as $m) echo $m; ?>

            <div class="card form-card shadow-sm">
                <div class="card-body form-card-body">
                    <form method="POST" novalidate>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-fill"></i> Donneur</label>
                            <select name="id_donneur" class="form-select" required>
                                <option value="">Sélectionnez un donneur...</option>
                                <?php foreach($donneurs as $d): ?>
                                    <option value="<?= (int)$d['id_donneur'] ?>">
                                        <?= htmlspecialchars($d['nom'] . " " . $d['prenom'] . " (".$d['groupe_sanguin'].$d['rhesus'].")") ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-hospital"></i> Centre de Collecte</label>
                            <select name="id_centre" class="form-select" required>
                                <option value="">Sélectionnez un centre...</option>
                                <?php foreach($centres as $c): ?>
                                    <option value="<?= (int)$c['id_centre'] ?>">
                                        <?= htmlspecialchars($c['nom_centre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-calendar-event"></i> Date du Don</label>
                            <input type="date" name="date_don" class="form-control" required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> Enregistrer le Don
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="liste.php" class="link-secondary"><i class="bi bi-arrow-left-circle"></i> Retour au Stock</a>
            </div>

        </div>
    </div>
</div>


</body>
</html>
