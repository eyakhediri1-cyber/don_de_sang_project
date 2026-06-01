<?php
require_once '../config/connexion.php';       // contient $pdo
require_once '../config/authentification.php'; // contient verifierConnexion() et verifierRole()

// Protection de la page
verifierConnexion();
verifierRole(['ADMIN', 'SECRETAIRE']); // seuls Admin et Secrétaire peuvent voir la liste

// --- Récupération des dons ---
try {
    $sql = "
        SELECT d.id_don, d.date_don, d.statut,
               dn.id_donneur, dn.cin, dn.nom, dn.prenom, dn.groupe_sanguin, dn.rhesus,
               c.nom_centre AS centre
        FROM dons d
        JOIN donneurs dn ON d.id_donneur = dn.id_donneur
        JOIN centres_collecte c ON d.id_centre = c.id_centre
        ORDER BY d.id_don DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur lors de la récupération des dons : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des Dons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .text-center.mb-4 {
            background: var(--primary-gradient);
            padding: 2.5rem;
            border-radius: 20px;
            color: white;
            margin-bottom: 2.5rem !important;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .fs-1 {
            font-size: 4rem !important;
            animation: heartbeat 1.5s infinite;
            display: inline-block;
            filter: drop-shadow(0 5px 15px rgba(220, 53, 69, 0.5));
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            10%, 30% { transform: scale(1.1); }
            20% { transform: scale(1); }
        }

        h2 {
            font-weight: 800;
            font-size: 2.8rem;
            margin: 1rem 0 0.5rem 0;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
        }

        .text-center.mb-4 p {
            font-size: 1.2rem;
            opacity: 0.95;
            margin: 0;
            font-weight: 300;
        }

        .table-responsive {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 0;
        }

        .table thead {
            background: var(--primary-gradient);
        }

        .table thead th {
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            padding: 1.2rem;
            border: none;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-bottom: 2px solid #f1f3f5;
        }

        .table tbody tr:hover {
            background: linear-gradient(to right, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .table tbody td {
            padding: 1.2rem;
            vertical-align: middle;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .badge {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .badge.bg-warning {
            background: linear-gradient(135deg, #ffd200, #ff8c00) !important;
            color: #000;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #11998e, #38ef7d) !important;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, #eb3349, #f45c43) !important;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, #434343, #000000) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #764ba2, #667eea);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffd200, #ff8c00);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
            color: #000;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(88, 68, 44, 0.5);
            background: linear-gradient(135deg, #deb37dff, #e1d28fff);
            color: #000;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 1px;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(114, 170, 219, 0.5);
            background: linear-gradient(135deg, #6496c8ff, #6986a3ff);
        }

        .mb-1 {
            margin-bottom: 0.5rem !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, #eb3349, #f45c43);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1.5rem;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(235, 51, 73, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }

            h2 {
                font-size: 2rem;
            }

            .fs-1 {
                font-size: 3rem !important;
            }

            .table-responsive {
                padding: 1rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.8rem;
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.75rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Animation d'entrée */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
<div class="container my-5">

    <div class="text-center mb-4">
        <div class="fs-1">🩸</div>
        <h2>Liste des Dons</h2>
        <p>Gestion et suivi des dons de sang</p>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Don</th>
                    <th>ID Donneur</th>
                    <th>CIN</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Groupe et Rhésus</th>
                    <th>Date Don</th>
                    <th>Centre</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dons)): ?>
                    <?php foreach($dons as $don): ?>
                        <tr>
                            <td><?= $don["id_don"] ?></td>
                            <td><?= $don["id_donneur"] ?></td>
                            <td><?= htmlspecialchars($don["cin"]) ?></td>
                            <td><?= htmlspecialchars($don["nom"]) ?></td>
                            <td><?= htmlspecialchars($don["prenom"]) ?></td>
                            <td><?= htmlspecialchars($don["groupe_sanguin"] . $don["rhesus"]) ?></td>
                            <td><?= $don["date_don"] ?></td>
                            <td><?= htmlspecialchars($don["centre"]) ?></td>
                            <td>
                                <span class="badge
                                    <?php
                                        if($don['statut'] == 'EN STOCK') echo 'bg-warning';
                                        elseif($don['statut'] == 'VALIDE') echo 'bg-success';
                                        elseif($don['statut'] == 'REJETÉ') echo 'bg-danger';
                                        elseif($don['statut'] == 'UTILISE') echo 'bg-secondary';
                                    ?>">
                                    <?= $don['statut'] ?>
                                </span>
                            </td>
                            <td>
                                <!-- Bouton Tester le don -->
                                <a href="test/ajout.php?id_don=<?= $don['id_don'] ?>" class="btn btn-sm btn-primary mb-1">
                                    🧪 Tester
                                </a>
                                <!-- Bouton modifier le statut -->
                                <a href="update_statut.php?id=<?= $don['id_don'] ?>" class="btn btn-sm btn-warning">
                                    Modifier statut
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Aucun don trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="../index.php" class="btn btn-secondary">← Retour à l'accueil</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>