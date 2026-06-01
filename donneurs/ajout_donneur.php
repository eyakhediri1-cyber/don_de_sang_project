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

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = trim($_POST['cin'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $groupe_sanguin = $_POST['groupe_sanguin'] ?? '';
    $rhesus = $_POST['rhesus'] ?? '';

    // Validation simple
    if (!$cin || !$nom || !$prenom || !$date_naissance || !$ville || !$groupe_sanguin || !$rhesus) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    // Insertion si pas d'erreur
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO donneurs (cin, nom, prenom, date_naissance, ville, groupe_sanguin, rhesus) VALUES (?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$cin, $nom, $prenom, $date_naissance, $ville, $groupe_sanguin, $rhesus]);
            $success = "Donneur ajouté avec succès.";
            // Réinitialiser le formulaire
            $_POST = [];
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // violation de clé unique (cin)
                $errors[] = "Un donneur avec ce CIN existe déjà.";
            } else {
                $errors[] = "Erreur lors de l'ajout : " . $e->getMessage();
            }
        }
    }
}
?>

<?php include '../template/header.php'; ?>

<div class="container mt-5">
    <h2>Ajouter un donneur</h2>

    <?php foreach($errors as $err) echo "<div class='alert alert-danger'>$err</div>"; ?>
    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label>CIN</label>
            <input type="text" name="cin" class="form-control" value="<?= htmlspecialchars($_POST['cin'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Prénom</label>
            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Ville</label>
            <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label>Groupe Sanguin</label>
            <select name="groupe_sanguin" class="form-control" required>
                <option value="">--Sélectionner--</option>
                <option value="A" <?= (($_POST['groupe_sanguin'] ?? '') == 'A') ? 'selected' : '' ?>>A</option>
                <option value="B" <?= (($_POST['groupe_sanguin'] ?? '') == 'B') ? 'selected' : '' ?>>B</option>
                <option value="AB" <?= (($_POST['groupe_sanguin'] ?? '') == 'AB') ? 'selected' : '' ?>>AB</option>
                <option value="O" <?= (($_POST['groupe_sanguin'] ?? '') == 'O') ? 'selected' : '' ?>>O</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Rhesus</label>
            <select name="rhesus" class="form-control" required>
                <option value="">--Sélectionner--</option>
                <option value="+" <?= (($_POST['rhesus'] ?? '') == '+') ? 'selected' : '' ?>>+</option>
                <option value="-" <?= (($_POST['rhesus'] ?? '') == '-') ? 'selected' : '' ?>>-</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
    <div class="text-center mt-4">
                <a href="liste_donneur.php" class="link-secondary"><i class="bi bi-arrow-left-circle"></i> Retour a la liste</a>
            </div>
</div>

<?php include '../template/footer.php'; ?>
