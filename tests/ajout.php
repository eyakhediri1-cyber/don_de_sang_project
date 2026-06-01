<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

verifierConnexion();

// Récupération de l'ID du don depuis GET ou POST
$id = 0;
if (!empty($_GET['id_don'])) {
    $id = intval($_GET['id_don']);
} elseif (!empty($_POST['id_don'])) {
    $id = intval($_POST['id_don']);
}

if ($id <= 0) {
    die("<div class='alert alert-danger text-center mt-5'>ID manquant ou invalide</div>");
}

// Vérifier si le don existe
$stmt = $pdo->prepare("
    SELECT d.*, dn.nom, dn.prenom, dn.groupe_sanguin
    FROM dons d
    JOIN donneurs dn ON d.id_donneur = dn.id_donneur
    WHERE d.id_don = ?
");
$stmt->execute([$id]);
$don = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$don) {
    die("<div class='alert alert-danger text-center mt-5'>Don introuvable</div>");
}

// Vérifier si un test existe deja
$check = $pdo->prepare("SELECT * FROM tests_don WHERE id_don = ?");
$check->execute([$id]);
$test_existant = $check->fetch(PDO::FETCH_ASSOC);

$message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $est_conforme = isset($_POST["est_conforme"]) ? intval($_POST["est_conforme"]) : 0;
    $remarques = trim($_POST["remarques"] ?? "");

    try {
        if ($test_existant) {
            // Mise à jour du test existant
            $update = $pdo->prepare("
                UPDATE tests_don SET est_conforme = ?, remarques = ? WHERE id_don = ?
            ");
            $update->execute([$est_conforme, $remarques, $id]);
        } else {
            // Ajout d'un nouveau test
            $insert = $pdo->prepare("
                INSERT INTO tests_don (id_don, est_conforme, remarques) 
                VALUES (?, ?, ?)
            ");
            $insert->execute([$id, $est_conforme, $remarques]);
        }

        // Mise à jour du statut du don
        $statut_final = ($est_conforme === 1) ? "VALIDE" : "REJETÉ";
        $pdo->prepare("UPDATE dons SET statut = ? WHERE id_don = ?")
            ->execute([$statut_final, $id]);

        $message = "<div class='alert alert-success text-center mt-3'>
                        ✔ Test enregistré et statut mis à jour
                    </div>";

        // Reload test
        $check->execute([$id]);
        $test_existant = $check->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

include '../template/header.php';
?>

<div class="container mt-5">

    <h2 class="mb-4">🧪 Test du Don N° <?= $don['id_don'] ?></h2>

    <?= $message ?>

    <!-- Infos du donneur -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Informations du donneur :</h5>
            <p><strong>Nom :</strong> <?= htmlspecialchars($don['nom']) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($don['prenom']) ?></p>
            <p><strong>Groupe sanguin :</strong> <span class="fw-bold text-danger"><?= htmlspecialchars($don['groupe_sanguin']) ?></span></p>
            <p><strong>Statut actuel :</strong> 
                <span class="badge bg-<?= $don['statut'] === 'VALIDE' ? 'success' : 'secondary' ?>">
                    <?= $don['statut'] ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Formulaire test -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Résultat du test :</h5>

            <form method="POST">
                <!-- Champ hidden pour ID -->
                <input type="hidden" name="id_don" value="<?= $don['id_don'] ?>">

                <div class="mb-3">
                    <label class="form-label">Conformité du sang :</label>
                    <select name="est_conforme" class="form-select" required>
                        <option value="1" <?= ($test_existant && $test_existant['est_conforme'] == 1) ? 'selected' : '' ?>>
                            ✔ Conforme (Sang valide)
                        </option>
                        <option value="0" <?= ($test_existant && $test_existant['est_conforme'] == 0) ? 'selected' : '' ?>>
                            ✖ Non conforme (Sang rejeté)
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarques :</label>
                    <textarea name="remarques" class="form-control" rows="3"><?= $test_existant['remarques'] ?? "" ?></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    💾 Enregistrer le test
                </button>

            </form>
        </div>
    </div>

    <a href="../listeDons.php" class="btn btn-secondary mt-4">⬅ Retour à la liste des dons</a>

</div>

<?php include '../template/footer.php'; ?>
