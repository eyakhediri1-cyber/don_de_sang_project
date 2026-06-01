<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';
verifierConnexion();
verifierRole('ADMIN');

if (!isset($_GET['id'])) {
    header("Location: liste.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['nom_utilisateur']);
    $role = $_POST['role'];
    $password = $_POST['mot_de_passe'];

if (empty($username) || empty($role)) {
    $errors[] = "Nom et rôle obligatoires.";
}

    if (empty($errors)) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_utilisateur=?, role=?, mot_de_passe=? WHERE id_utilisateur=?");
            $stmt->execute([$username, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_utilisateur=?, role=? WHERE id_utilisateur=?");
            $stmt->execute([$username, $role, $id]);
        }
        $success = "Utilisateur mis à jour avec succès.";
    }
}
?>

<?php include '../template/header.php'; ?>
<div class="container mt-5">
    <h2>Modifier un utilisateur</h2>
    <?php foreach ($errors as $err) echo "<div class='alert alert-danger'>$err</div>"; ?>
    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Nom d'utilisateur</label>
            <input type="text" name="nom_utilisateur" class="form-control" value="<?= htmlspecialchars($user['nom_utilisateur']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Rôle</label>
            <select name="role" class="form-control" required>
                <option value="ADMIN" <?= $user['role']=='ADMIN'?'selected':'' ?>>ADMIN</option>
                <option value="MEDECIN" <?= $user['role']=='MEDECIN'?'selected':'' ?>>MEDECIN</option>
                <option value="SECRETAIRE" <?= $user['role']=='SECRETAIRE'?'selected':'' ?>>SECRETAIRE</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>
<?php include '../template/footer.php'; ?>
