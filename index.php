<?php
require_once __DIR__ . '/config/connexion.php';
require_once './config/authentification.php';

verifierConnexion();

// Récupération des statistiques
try {
    // Total donneurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM donneurs");
    $total_donneurs = $stmt->fetch()['total'];

    // Total dons en stock
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM dons WHERE statut = 'EN STOCK'");
    $total_dons_stock = $stmt->fetch()['total'];

    // Total centres
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM centres_collecte");
    $total_centres = $stmt->fetch()['total'];

    // Stock par groupe sanguin (CORRIGÉ)
    $stmt = $pdo->query("
        SELECT dn.groupe_sanguin, COUNT(*) AS quantite
        FROM dons d
        JOIN donneurs dn ON dn.id_donneur = d.id_donneur
        WHERE d.statut = 'EN STOCK'
        GROUP BY dn.groupe_sanguin
    ");
    $stock_par_groupe = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Alertes (besoins)
    $stmt = $pdo->query("
        SELECT groupe_sanguin, niveau_alerte
        FROM besoins
        WHERE niveau_alerte IN ('URGENT', 'CRITIQUE')
    ");
    $alertes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Centre de Don</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
  :root {
    /* Couleurs Pastel Principales */
    --pastel-pink: #FFD6E8;
    --pastel-peach: #FFDAB9;
    --pastel-lavender: #E6E6FA;
    --pastel-mint: #C7F0DB;
    --pastel-blue: #D4E4F7;
    --pastel-yellow: #FFF4C2;
    --pastel-coral: #FFB5B5;
    --pastel-sage: #C8E6C9;
   
    /* Tons Secondaires */
    --soft-cream: #FFF8F0;
    --soft-white: #FEFEFE;
    --warm-beige: #F5EDE0;
    --light-gray: #F0F0F5;
   
    /* Accents Doux */
    --accent-rose: #F8BBD0;
    --accent-lilac: #E1BEE7;
    --accent-aqua: #B2EBF2;
    --accent-lemon: #FFF9C4;
   
    /* Textes */
    --text-dark: #5A5A72;
    --text-medium: #8B8BA7;
    --text-light: #B5B5C8;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, var(--soft-cream) 0%, var(--pastel-lavender) 50%, var(--pastel-blue) 100%);
    min-height: 100vh;
    color: var(--text-dark);
}

/* ============================================
   SIDEBAR PASTEL ESTHÉTIQUE
   ============================================ */

.sidebar {
    min-height: 100vh;
    background: linear-gradient(180deg, var(--soft-white) 0%, var(--warm-beige) 100%);
    box-shadow: 3px 0 20px rgba(230, 230, 250, 0.5);
    position: sticky;
    top: 0;
    border-right: 2px solid var(--pastel-lavender);
}

.sidebar-header {
    padding: 2.5rem 1.5rem;
    border-bottom: 3px solid var(--pastel-pink);
    background: linear-gradient(135deg, var(--pastel-lavender) 0%, var(--pastel-blue) 100%);
    position: relative;
    text-align: center;
}

.sidebar-logo {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem auto;
    border-radius: 50%;
    border: 4px solid var(--pastel-pink);
    padding: 5px;
    background: linear-gradient(135deg, var(--soft-white) 0%, var(--pastel-peach) 100%);
    box-shadow: 0 5px 25px rgba(255, 214, 232, 0.6);
}

.sidebar-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.sidebar-header h4 {
    color: var(--text-dark);
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    margin: 0;
    font-size: 1.6rem;
    letter-spacing: 1px;
    text-shadow: 2px 2px 4px rgba(230, 230, 250, 0.5);
}

.sidebar-header small {
    color: var(--text-medium);
    font-size: 0.8rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-weight: 400;
}

.sidebar .nav-link {
    color: var(--text-dark);
    padding: 1rem 1.5rem;
    margin: 0.4rem 1rem;
    border-radius: 20px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    display: flex;
    align-items: center;
    border: 2px solid transparent;
    letter-spacing: 0.5px;
    background: transparent;
}

.sidebar .nav-link i {
    margin-right: 12px;
    width: 22px;
    text-align: center;
    color: var(--accent-rose);
    font-size: 1.1rem;
}

.sidebar .nav-link:hover {
    background: linear-gradient(135deg, var(--pastel-pink) 0%, var(--pastel-peach) 100%);
    border: 2px solid var(--accent-rose);
    transform: translateX(8px);
    color: var(--text-dark);
    box-shadow: 0 5px 20px rgba(255, 214, 232, 0.5);
}

.sidebar .nav-link.active {
    background: linear-gradient(135deg, var(--pastel-lavender) 0%, var(--pastel-blue) 100%);
    color: var(--text-dark);
    font-weight: 600;
    box-shadow: 0 8px 25px rgba(230, 230, 250, 0.6);
    border: 2px solid var(--accent-lilac);
}

.sidebar .nav-link.active i {
    color: var(--text-dark);
}

.nav-link.text-warning {
    margin-top: 2rem !important;
    border-top: 2px solid var(--pastel-coral);
    padding-top: 1.5rem !important;
    color: var(--pastel-coral) !important;
}

.nav-link.text-warning:hover {
    background: linear-gradient(135deg, var(--pastel-coral) 0%, var(--pastel-peach) 100%);
    border-color: var(--pastel-coral);
}

/* ============================================
   MAIN CONTENT
   ============================================ */

.main-content {
    padding: 2.5rem;
    background: transparent;
}

.page-header {
    margin-bottom: 3rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid var(--pastel-pink);
    position: relative;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: url('https://images.unsplash.com/photo-1615461066159-fea0960485d5?w=800&q=80') center/cover;
    opacity: 0.08;
    border-radius: 20px;
}

.page-header h1 {
    font-family: 'Playfair Display', serif;
    color: var(--text-dark);
    font-weight: 900;
    font-size: 3rem;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(230, 230, 250, 0.3);
    letter-spacing: 1px;
}

.page-header p {
    color: var(--text-medium);
    font-size: 1rem;
    letter-spacing: 0.5px;
    font-weight: 400;
}

/* ============================================
   STAT CARDS PASTEL
   ============================================ */

.stat-card {
    border: none;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(230, 230, 250, 0.4);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    border: 3px solid transparent;
}

.stat-card-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.12;
    z-index: 0;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.3) 100%);
    opacity: 0;
    transition: opacity 0.5s ease;
}

.stat-card:hover {
    transform: translateY(-10px) scale(1.03);
    box-shadow: 0 15px 45px rgba(230, 230, 250, 0.6);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card .card-body {
    padding: 2.5rem;
    position: relative;
    z-index: 1;
}

.stat-card h5 {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--text-dark);
}

.stat-card h2 {
    font-family: 'Playfair Display', serif;
    font-size: 3.5rem;
    font-weight: 900;
    margin: 0;
    color: var(--text-dark);
}

.stat-card .icon-bg {
    position: absolute;
    right: -20px;
    bottom: -20px;
    font-size: 8rem;
    opacity: 0.15;
    color: var(--text-dark);
}

/* Couleurs spécifiques pour chaque carte */
.bg-primary {
    background: linear-gradient(135deg, var(--pastel-lavender) 0%, var(--pastel-blue) 100%) !important;
    border-color: var(--accent-lilac) !important;
}

.bg-success {
    background: linear-gradient(135deg, var(--pastel-mint) 0%, var(--pastel-sage) 100%) !important;
    border-color: var(--pastel-sage) !important;
}

.bg-info {
    background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--accent-aqua) 100%) !important;
    border-color: var(--pastel-blue) !important;
}

/* ============================================
   CONTENT CARDS
   ============================================ */

.content-card {
    border: none;
    border-radius: 25px;
    box-shadow: 0 8px 30px rgba(230, 230, 250, 0.4);
    overflow: hidden;
    margin-bottom: 2rem;
    background: var(--soft-white);
    border: 3px solid var(--pastel-lavender);
    backdrop-filter: blur(10px);
}

.content-card .card-header {
    background: linear-gradient(135deg, var(--pastel-pink) 0%, var(--pastel-peach) 100%);
    border: none;
    padding: 1.5rem 2rem;
    border-bottom: 3px solid var(--accent-rose);
}

.content-card .card-header h5 {
    color: var(--text-dark);
    margin: 0;
    font-weight: 700;
    font-size: 1.2rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.content-card .card-body {
    padding: 2rem;
    background: var(--soft-white);
}

/* ============================================
   TABLE PASTEL
   ============================================ */

.table-custom {
    margin: 0;
    color: var(--text-dark);
}

.table-custom thead {
    background: linear-gradient(135deg, var(--pastel-lavender) 0%, var(--pastel-blue) 100%);
    border-bottom: 3px solid var(--accent-lilac);
}

.table-custom thead th {
    border: none;
    color: var(--text-dark);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 2px;
    padding: 1.2rem;
}

.table-custom tbody td {
    padding: 1.3rem;
    vertical-align: middle;
    border-color: var(--pastel-lavender);
    color: var(--text-dark);
}

.table-custom tbody tr {
    transition: all 0.3s ease;
    background: var(--soft-white);
}

.table-custom tbody tr:hover {
    background: linear-gradient(135deg, var(--pastel-pink) 0%, var(--pastel-peach) 100%);
    transform: scale(1.01);
}

/* ============================================
   BLOOD BADGE PASTEL
   ============================================ */

.blood-badge {
    display: inline-block;
    padding: 0.6rem 1.5rem;
    background: linear-gradient(135deg, var(--pastel-coral) 0%, var(--accent-rose) 100%);
    color: var(--text-dark);
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    font-family: 'Playfair Display', serif;
    border: 3px solid var(--text-dark);
    box-shadow: 0 5px 20px rgba(255, 181, 181, 0.5);
    letter-spacing: 1px;
}

/* ============================================
   ALERTS PASTEL
   ============================================ */

.alert-custom {
    border: none;
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    box-shadow: 0 5px 20px rgba(230, 230, 250, 0.3);
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
}

.alert-bg-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.08;
    z-index: 0;
}

.alert-custom > * {
    position: relative;
    z-index: 1;
}

.alert-custom i {
    margin-right: 1.5rem;
    font-size: 2rem;
}

.alert-danger-custom {
    background: linear-gradient(135deg, var(--pastel-coral) 0%, var(--accent-rose) 100%);
    color: var(--text-dark);
    border-color: var(--pastel-coral);
}

.alert-success-custom {
    background: linear-gradient(135deg, var(--pastel-mint) 0%, var(--pastel-sage) 100%);
    color: var(--text-dark);
    border-color: var(--pastel-sage);
}

/* ============================================
   PROGRESS BARS PASTEL
   ============================================ */

.stock-progress {
    height: 14px;
    border-radius: 20px;
    background: var(--light-gray);
    overflow: hidden;
    margin-top: 0.8rem;
    border: 2px solid var(--pastel-lavender);
}

.stock-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--pastel-pink) 0%, var(--accent-rose) 50%, var(--pastel-peach) 100%);
    border-radius: 20px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 0 15px rgba(255, 214, 232, 0.6);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}

/* ============================================
   ANIMATIONS
   ============================================ */

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

.stat-card,
.content-card {
    animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }

/* ============================================
   RESPONSIVE
   ============================================ */

@media (max-width: 768px) {
    .sidebar {
        position: relative;
        min-height: auto;
    }
   
    .main-content {
        padding: 1.5rem;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .stat-card h2 {
        font-size: 2.5rem;
    }
}

/* ============================================
   SCROLLBAR PASTEL
   ============================================ */

::-webkit-scrollbar {
    width: 12px;
}

::-webkit-scrollbar-track {
    background: var(--warm-beige);
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, var(--pastel-pink) 0%, var(--pastel-lavender) 100%);
    border-radius: 10px;
    border: 2px solid var(--warm-beige);
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, var(--accent-rose) 0%, var(--accent-lilac) 100%);
}
    </style>
</head>
<body>

<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 sidebar p-0">
        <div class="sidebar-header">
            <h4><i class="fas fa-heartbeat"></i> Centre de Don</h4>
            <small>Système de Gestion</small>
        </div>

        <ul class="nav flex-column px-2 py-3">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <i class="fas fa-home"></i> Tableau de Bord
                </a>
            </li>

            <?php if ($_SESSION['role']=='ADMIN'||$_SESSION['role']=='SECRETAIRE'): ?>
            <li class="nav-item">
                <a class="nav-link" href="donneurs/liste_donneur.php">
                    <i class="fas fa-users"></i> Donneurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dons/ajout.php">
                    <i class="fas fa-plus-circle"></i> Ajouter Dons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dons/liste.php">
                    <i class="fas fa-list"></i> Lister Dons
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['role']=='ADMIN'): ?>
            <li class="nav-item">
                <a class="nav-link" href="utilisateurs/ajout.php">
                    <i class="fas fa-user-plus"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="utilisateurs/liste.php">
                    <i class="fas fa-users-cog"></i> Liste Utilisateurs
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['role']=='ADMIN' || $_SESSION['role']=='SECRETAIRE' || $_SESSION['role']=='MEDECIN'): ?>
            <li class="nav-item">
                <a class="nav-link" href="transfusions/ajout.php">
                    <i class="fas fa-syringe"></i> Ajouter Transfusion
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="transfusions/liste.php">
                    <i class="fas fa-clipboard-list"></i> Transfusions
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['role']=='ADMIN'||$_SESSION['role']=='MEDECIN'): ?>
            <li class="nav-item">
                <a class="nav-link" href="tests/ajout.php">
                    <i class="fas fa-flask"></i> Tests
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['role']=='ADMIN' || $_SESSION['role']=='MEDECIN'): ?>
            <li class="nav-item">
                <a class="nav-link" href="stock/listeStock.php">
                    <i class="fas fa-boxes"></i> Stock Sanguin
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link text-warning" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </nav>

    <!-- Contenu principal -->
    <main class="col-md-9 col-lg-10 main-content">

        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Tableau de Bord</h1>
        </div>

        <!-- Statistiques clés -->
        <div class="row mb-4 g-3">

            <div class="col-md-4">
                <div class="card bg-primary text-white stat-card">
                    <div class="card-body">
                        <i class="fas fa-users icon-bg"></i>
                        <h5><i class="fas fa-user-friends"></i> Total Donneurs</h5>
                        <h2><?= $total_donneurs ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white stat-card">
                    <div class="card-body">
                        <i class="fas fa-tint icon-bg"></i>
                        <h5><i class="fas fa-box"></i> Dons en Stock</h5>
                        <h2><?= $total_dons_stock ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white stat-card">
                    <div class="card-body">
                        <i class="fas fa-hospital icon-bg"></i>
                        <h5><i class="fas fa-building"></i> Centres de Collecte</h5>
                        <h2><?= $total_centres ?></h2>
                    </div>
                </div>
            </div>

        </div>

        <!-- Stock par groupe sanguin -->
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Stock par Groupe Sanguin</h5>
            </div>

            <div class="card-body">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th><i class="fas fa-tint"></i> Groupe Sanguin</th>
                            <th><i class="fas fa-boxes"></i> Quantité Disponible</th>
                            <th>Stock</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $groupes = ['A','B','O','AB'];
                    $stock = ['A'=>0,'B'=>0,'O'=>0,'AB'=>0];

                    foreach ($stock_par_groupe as $s) {
                        $stock[$s['groupe_sanguin']] = $s['quantite'];
                    }

                    foreach ($groupes as $g):
                        $percentage = $total_dons_stock > 0 ? ($stock[$g] / $total_dons_stock * 100) : 0;
                    ?>
                        <tr>
                            <td>
                                <span class="blood-badge"><?= $g ?></span>
                            </td>
                            <td>
                                <strong style="font-size: 1.1rem; color: #dc3545;"><?= $stock[$g] ?></strong> 
                                <span class="text-muted">unités</span>
                            </td>
                            <td style="width: 40%;">
                                <div class="stock-progress">
                                    <div class="stock-progress-bar" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>

        <!-- Alertes -->
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Alertes et Notifications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($alertes)): ?>
                    <div class="alert-custom alert-success-custom">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Système Normal</strong><br>
                            <small>Aucune alerte critique détectée</small>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($alertes as $a): ?>
                        <div class="alert-custom alert-danger-custom">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Alerte - Groupe <?= $a['groupe_sanguin'] ?></strong><br>
                                <small>Niveau : <?= $a['niveau_alerte'] ?> - Action requise immédiatement</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </main>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>