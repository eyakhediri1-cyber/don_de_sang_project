<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Liste des dons avec LEFT JOIN tests
$tests = $pdo->query("
    SELECT 
        d.id_don, d.date_don, d.statut,
        dn.nom, dn.prenom,
        t.id_test, t.est_conforme
    FROM dons d
    JOIN donneurs dn ON d.id_donneur = dn.id_donneur
    LEFT JOIN tests_don t ON d.id_don = t.id_don
    ORDER BY d.id_don DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des Tests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
<div class="container">

    <h2 class="mb-4">Liste des Tests de Dons</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Don</th>
                <th>Donneur</th>
                <th>Date</th>
                <th>Statut Don</th>
                <th>Test</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($tests as $t): ?>
                <tr>
                    <td><?= $t["id_don"] ?></td>
                    <td><?= htmlspecialchars($t["nom"] . " " . $t["prenom"]) ?></td>
                    <td><?= $t["date_don"] ?></td>

                    <td>
                        <span class="badge 
                            <?php
                                if($t['statut']=='EN STOCK') echo 'bg-warning';
                                elseif($t['statut']=='VALIDE') echo 'bg-success';
                                elseif($t['statut']=='REJETÉ') echo 'bg-danger';
                                elseif($t['statut']=='UTILISÉ') echo 'bg-secondary';
                            ?>">
                            <?= $t['statut'] ?>
                        </span>
                    </td>

                    <td>
                        <?php
                            if ($t["id_test"] == null)
                                echo "<span class='badge bg-secondary'>À tester</span>";
                            else
                                echo $t["est_conforme"]
                                    ? "<span class='badge bg-success'>Conforme</span>"
                                    : "<span class='badge bg-danger'>Rejeté</span>";
                        ?>
                    </td>

                    <td>
                        <a href="ajout.php?id=<?= $t['id_don'] ?>" class="btn btn-sm btn-primary">
                            Tester
                        </a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>
</body>
</html>