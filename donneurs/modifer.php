<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Protection : connecté + ADMIN ou SECRETAIRE
verifierConnexion();
if (!estAdmin() && !estSecretaire()) {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé.";
    exit;
}

// Récupération de l'ID du donneur
$id = $_GET["id"] ?? null;
if (!$id) die("ID invalide !");

// Charger les données du donneur
$stmt = $pdo->prepare("SELECT * FROM donneurs WHERE id_donneur = ?");
$stmt->execute([$id]);
$donneur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$donneur) die("Donneur introuvable !");

$errors = [];
$success = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom_donneur"]);
    $prenom = trim($_POST["prenom"]);
    $cin = trim($_POST["cin"]);
    $groupe = $_POST["groupe_sanguin"];
    $rhesus = $_POST["rhesus"];
    $ville = trim($_POST["ville"]);

    // Validation simple
    if (!$nom || !$prenom || !$cin || !$groupe || !$rhesus) {
        $errors[] = "Tous les champs sauf Ville sont obligatoires.";
    }

    // Mise à jour si pas d'erreurs
    if (empty($errors)) {
        try {
            $update = $pdo->prepare("
                UPDATE donneurs 
                SET nom = ?, prenom = ?, cin = ?, groupe_sanguin = ?, rhesus = ?, ville = ?
                WHERE id_donneur = ?
            ");
            $update->execute([$nom, $prenom, $cin, $groupe, $rhesus, $ville, $id]);
            $success = "Donneur mis à jour avec succès.";

            // Recharger les données
            $stmt->execute([$id]);
            $donneur = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Inclure le header
include '../template/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Modifier Donneur #<?= htmlspecialchars($id) ?></h2>

    <!-- Messages d'erreur -->
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>

    <!-- Message de succès -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card p-4 shadow">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom_donneur" class="form-control" 
                           value="<?= htmlspecialchars($donneur["nom"]) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" 
                           value="<?= htmlspecialchars($donneur["prenom"]) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">CIN</label>
                <input type="text" name="cin" class="form-control"
                       value="<?= htmlspecialchars($donneur["cin"]) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Groupe Sanguin</label>
                    <select name="groupe_sanguin" class="form-select" required>
                        <?php
                        $groupes = ["A", "B", "AB", "O"];
                        foreach ($groupes as $g) {
                            $selected = ($donneur["groupe_sanguin"] == $g) ? "selected" : "";
                            echo "<option value='$g' $selected>$g</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Rhésus</label>
                    <select name="rhesus" class="form-select" required>
                        <option value="+" <?= ($donneur["rhesus"] == "+") ? "selected" : "" ?>>+</option>
                        <option value="-" <?= ($donneur["rhesus"] == "-") ? "selected" : "" ?>>-</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Ville</label>
                    <input type="text" name="ville" class="form-control" 
                           value="<?= htmlspecialchars($donneur["ville"]) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enregistrer les Modifications</button>
        </form>
    </div>
</div>

<?php include '../template/footer.php'; ?>
