
Eya Khediri
09:55 (il y a 10 minutes)
À moi

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

// Récupération des donneurs
$stmt = $pdo->query("SELECT * FROM donneurs ORDER BY id_donneur DESC");
$donneurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../template/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">📋 Liste des Donneurs</h2>
    <a href="ajout_donneur.php" class="btn btn-primary mb-3">➕ Ajouter un donneur</a>

    <?php if(empty($donneurs)): ?>
        <div class="alert alert-info">Aucun donneur trouvé.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>CIN</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Groupe Sanguin</th>
                        <th>Rhesus</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($donneurs as $d): ?>
                    <tr>
                        <td><?= $d['id_donneur'] ?></td>
                        <td><?= htmlspecialchars($d['cin']) ?></td>
                        <td><?= htmlspecialchars($d['nom']) ?></td>
                        <td><?= htmlspecialchars($d['prenom']) ?></td>
                        <td><?= htmlspecialchars($d['groupe_sanguin']) ?></td>
                        <td><?= htmlspecialchars($d['rhesus']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $d['id_donneur'] ?>" 
                               class="btn btn-sm btn-info">
                               Voir Détails
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../template/footer.php'; ?>