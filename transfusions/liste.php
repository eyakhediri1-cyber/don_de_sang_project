<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Vérification connexion + accès ADMIN (si nécessaire)
// verifierConnexion();
// verifierRole('ADMIN');

try {
    $stmt = $pdo->prepare("
        SELECT 
            t.id_transfusion, 
            t.id_don, 
            t.hopital_recepteur, 
            t.date_transfusion, 
            d.statut
        FROM transfusions t
        JOIN dons d ON t.id_don = d.id_don
        ORDER BY t.id_transfusion DESC
    ");
    $stmt->execute();
    $transfusions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e){
    die(" Erreur lors de la récupération des transfusions : " . $e->getMessage());
}
?>

<?php include '../template/header.php'; ?>

<div class="container mt-5">

    <h1 class="mb-4">📄 Liste des Transfusions</h1>

    <?php if(empty($transfusions)): ?>

        <div class="alert alert-info">
            Aucune transfusion enregistrée pour le moment.
        </div>

    <?php else: ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>ID Don</th>
                <th>Hôpital récepteur</th>
                <th>Date</th>
                <th>Statut du Don</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($transfusions as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['id_transfusion']) ?></td>
                <td><?= htmlspecialchars($t['id_don']) ?></td>
                <td><?= htmlspecialchars($t['hopital_recepteur']) ?></td>
                <td><?= htmlspecialchars($t['date_transfusion']) ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($t['statut']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
     <div class="mb-3">
        <a href="../index.php" class="btn btn-primary">
            ⬅ Retour à l'accueil
        </a>
    </div>
</div>

<?php include '../template/footer.php'; ?>
