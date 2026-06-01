<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Vérification autorisation ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom_utilisateur = trim($_POST['nom_utilisateur'] ?? '');
    $mot_de_passe    = trim($_POST['mot_de_passe']    ?? '');
    $role            = $_POST['role'] ?? '';
    $id_centre       = $_POST['id_centre'] ?? null;

    // Vérification champs obligatoires
    if (empty($nom_utilisateur) || empty($mot_de_passe) || empty($role)) {
        $errors[] = "Veuillez remplir tous les champs obligatoires marqués *.";
    }

    // Vérifier si le nom existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE nom_utilisateur = :nom");
        $stmt->execute(['nom' => $nom_utilisateur]);
        if ($stmt->fetch()) $errors[] = "⚠ Ce nom d'utilisateur est déjà pris.";
    }

    // Si tout est OK → insertion
    if (empty($errors)) {
        try {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur,mot_de_passe,role,id_centre) 
                                     VALUES (:nom,:pass,:role,:centre)");
            $insert->execute([
                'nom'    => $nom_utilisateur,
                'pass'   => $hash,
                'role'   => $role,
                'centre' => $id_centre
            ]);

            $success = "🎉 Utilisateur ajouté avec succès !";
            $_POST = []; // Reset formulaire

        } catch(PDOException $e) {
            $errors[] = "Erreur SQL ❌ " . $e->getMessage();
        }
    }
}

include '../template/header.php';
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4 bg-dark text-light rounded-4" style="max-width:700px;margin:auto;">
        
        <h3 class="text-center mb-4"><i class="fas fa-user-plus text-warning me-2"></i>Créer un utilisateur</h3>

        <!-- Affichage messages -->
        <?php if($errors): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $e) echo "<div>$e</div>"; ?>
            </div>
        <?php elseif($success): ?>
            <div class="alert alert-success text-center"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Nom d'utilisateur *</label>
                <input type="text" name="nom_utilisateur" class="form-control bg-secondary text-light"
                       value="<?= htmlspecialchars($_POST['nom_utilisateur'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Mot de passe *</label>
                <input type="password" name="mot_de_passe" class="form-control bg-secondary text-light" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Rôle *</label>
                <select name="role" class="form-select bg-secondary text-light" required>
                    <option value="">Sélectionner...</option>
                    <option value="ADMIN"     <?=(@$_POST['role']=='ADMIN'?'selected':'')?> >Administrateur</option>
                    <option value="MEDECIN"   <?=(@$_POST['role']=='MEDECIN'?'selected':'')?> >Médecin</option>
                    <option value="SECRETAIRE"<?=(@$_POST['role']=='SECRETAIRE'?'selected':'')?> >Secrétaire</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Centre (facultatif)</label>
                <select name="id_centre" class="form-select bg-secondary text-light">
                    <option value="">Aucun</option>
                    <?php
                    $centres = $pdo->query("SELECT * FROM centres_collecte ORDER BY nom_centre")->fetchAll();
                    foreach($centres as $c){
                        $sel = (@$_POST['id_centre']==$c['id_centre']) ? 'selected' : '';
                        echo "<option value='{$c['id_centre']}' $sel>{$c['nom_centre']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg px-5">Ajouter</button>
                <a href="liste.php" class="btn btn-outline-light ms-2 px-4">Retour</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-control,.form-select{border:none;}
.form-control:focus,.form-select:focus{
    border:2px solid #c9a96e !important;
    box-shadow:0 0 10px rgba(201,169,110,.6) !important;
}
</style>

<?php include '../template/footer.php'; ?>
