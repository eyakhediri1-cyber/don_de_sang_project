<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Vérifier admin
if (!estAdmin()) {
    header("Location: ../login.php");
    exit;
}

$msg = "";
$msg_type = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_don = intval($_POST['id_don']);
    $hopital_recepteur = trim($_POST['hopital_recepteur']);
    $date_transfusion = $_POST['date_transfusion'];

    // Validation
    if (empty($hopital_recepteur)) {
        $msg = "⚠ Le nom de l'hôpital est obligatoire.";
        $msg_type = "danger";

    } else {

        // Vérifier si le don est encore valide
        $check = $pdo->prepare("SELECT statut FROM dons WHERE id_don = ?");
        $check->execute([$id_don]);
        $statut = $check->fetchColumn();

        if ($statut !== "EN STOCK") { // On vérifie le statut correct
            $msg = "⚠ Ce don est déjà utilisé ou non valide.";
            $msg_type = "warning";

        } else {

            // --- Ajouter la transfusion ---
            $stmt = $pdo->prepare("
                INSERT INTO transfusions (id_don, hopital_recepteur, date_transfusion)
                VALUES (:id_don, :hopital_recepteur, :date_transfusion)
            ");

            $stmt->execute([
                ":id_don"          => $id_don,
                ":hopital_recepteur"=> $hopital_recepteur,
                ":date_transfusion"=> $date_transfusion
            ]);

            // --- Mise à jour du statut du don ---
            $pdo->prepare("UPDATE dons SET statut = 'UTILISE' WHERE id_don=?")
                ->execute([$id_don]);

            $msg = "✔ Transfusion enregistrée ✓ Don marqué comme UTILISÉ.";
            $msg_type = "success";
        }
    }
}

// ================== RÉCUPÉRATION DES DONS EN STOCK ==================
$dons = $pdo->query("
    SELECT d.id_don, dn.nom, dn.prenom, dn.groupe_sanguin, dn.rhesus
    FROM dons d
    JOIN donneurs dn ON d.id_donneur = dn.id_donneur
    WHERE d.statut='EN STOCK'
    ORDER BY d.id_don DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../template/header.php'; ?>

<div class="container mt-5 p-4 bg-dark text-light rounded-4 shadow">
    <h2 class="text-center mb-4">🩸 Ajouter une Transfusion</h2>

    <?php if($msg): ?>
        <div class="alert alert-<?= $msg_type ?> text-center fw-bold"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">

        <div class="col-md-12">
            <label class="form-label">Sélection du Don (EN STOCK) *</label>
            <select name="id_don" class="form-select bg-secondary text-light" required>
                <option value="">Choisir un don...</option>
                <?php foreach($dons as $d): ?>
                    <option value="<?= $d['id_don'] ?>">
                        Don #<?= $d['id_don'] ?> — <?= htmlspecialchars($d['nom'].' '.$d['prenom']) ?> (<?= $d['groupe_sanguin'].$d['rhesus'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Hôpital Récepteur *</label>
            <input type="text" name="hopital_recepteur" class="form-control bg-secondary text-light" placeholder="Ex: Charles Nicolle" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Date de Transfusion *</label>
            <input type="date" name="date_transfusion" value="<?= date('Y-m-d') ?>" class="form-control bg-secondary text-light" required>
        </div>

        <div class="col-12 text-center mt-3">
            <button class="btn btn-danger px-4 py-2" type="submit">Enregistrer</button>
        </div>
    </form>
    <div class="mb-3">
        <a href="../index.php" class="btn btn-primary">
            ⬅ Retour à l'accueil
        </a>
    </div>
</div>

<style>
.bg-secondary{ background:rgba(255,255,255,0.12)!important; }
.form-control:focus,.form-select:focus{
    border-color:#c9a96e!important;
    box-shadow:0 0 8px rgba(201,169,110,0.6)!important;
}
   .form-select option {
    color: #000000;       /* Texte noir pour les options */
    background-color: #ffffff; /* Fond blanc des options */
}

/* Garde le focus actuel du select */
.form-control:focus, .form-select:focus {
    border-color: #c9a96e !important;
    box-shadow: 0 0 8px rgba(201,169,110,0.6) !important;
} 
</style>

<?php include '../template/footer.php'; ?>
