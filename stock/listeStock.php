<?php

require_once '../config/connexion.php';
require_once '../config/authentification.php';
include '../template/header.php'; 

try {
    $stmt = $pdo->query("
        SELECT d.id_don, dn.cin, dn.groupe_sanguin, d.statut
        FROM dons d
        JOIN donneurs dn ON d.id_donneur = dn.id_donneur
        ORDER BY d.id_don DESC
    ");
    $donnees = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='alert alert-danger container mt-4'>Erreur lors du chargement du stock : " . $e->getMessage() . "</div>");
}
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">📦 Stock de Sang Disponible</h2>
        <a href="../index.php" class="btn btn-outline-primary">
            ⬅ Retour à l'accueil
        </a>
    </div>

    <?php if(empty($donnees)): ?>
        <div class="alert alert-warning text-center">Aucun stock disponible pour le moment.</div>

    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-sm">
                <thead class="table-danger text-dark">
                    <tr>
                        <th>ID Don</th>
                        <th>CIN Donneur</th>
                        <th>Groupe Sanguin</th>
                        <th>Statut</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($donnees as $d): ?>
                    <tr>
                        <td class="fw-semibold"><?= $d['id_don'] ?></td>
                        <td><?= htmlspecialchars($d['cin']) ?></td>
                        <td><span class="fw-bold"><?= htmlspecialchars($d['groupe_sanguin']) ?></span></td>
                        <td>
                            <span class="badge 
                                bg-<?= $d['statut'] === 'VALIDE' ? 'success' : 'secondary' ?> p-2">
                                <?= $d['statut'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    <?php endif; ?>

</div>

<?php include '../template/footer.php'; ?>
