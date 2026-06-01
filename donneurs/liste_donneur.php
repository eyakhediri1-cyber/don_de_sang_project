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

// Statistiques
$total_donneurs = count($donneurs);
$groupes_count = [];
foreach($donneurs as $d) {
    $groupe = $d['groupe_sanguin'];
    if(!isset($groupes_count[$groupe])) {
        $groupes_count[$groupe] = 0;
    }
    $groupes_count[$groupe]++;
}
?>

<?php include '../template/header.php'; ?>

<style>
    .donors-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 2.5rem 0;
        margin: -2rem -15px 2rem -15px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
    }

    .donors-header h2 {
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }

    .donors-header .breadcrumb {
        background: rgba(255,255,255,0.1);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        margin: 0.5rem 0 0 0;
    }

    .donors-header .breadcrumb-item {
        color: rgba(255,255,255,0.8);
    }

    .donors-header .breadcrumb-item.active {
        color: white;
    }

    .stats-row {
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        height: 100%;
        transition: all 0.3s ease;
        border-left: 4px solid #dc3545;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }

    .stat-card .stat-icon {
        font-size: 2.5rem;
        color: rgba(220, 53, 69, 0.2);
    }

    .stat-card h4 {
        color: #dc3545;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .btn-add-donor {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        transition: all 0.3s ease;
    }

    .btn-add-donor:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
        color: white;
    }

    .search-filter-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 0.8rem 1.2rem 0.8rem 3rem;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        outline: none;
    }

    .search-box i {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #636e72;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1.2rem;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        background: white;
        color: #636e72;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover, .filter-btn.active {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border-color: #dc3545;
    }

    .table-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table-custom {
        margin: 0;
    }

    .table-custom thead {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .table-custom thead th {
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f0f2f5;
    }

    .table-custom tbody tr {
        transition: all 0.3s ease;
    }

    .table-custom tbody tr:hover {
        background: #fff5f5;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
    }

    .donor-id-badge {
        display: inline-block;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .cin-badge {
        background: #f8f9fa;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #2c3e50;
        border: 1px solid #e9ecef;
    }

    .donor-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .blood-type-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    .rhesus-badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-left: 0.5rem;
    }

    .rhesus-positive {
        background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);
        color: white;
    }

    .rhesus-negative {
        background: linear-gradient(135deg, #ffd43b 0%, #fab005 100%);
        color: #2c3e50;
    }

    .btn-details {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-details:hover {
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(79, 172, 254, 0.4);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 5rem;
        color: #dfe6e9;
        margin-bottom: 1.5rem;
    }

    .empty-state h4 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #636e72;
        margin-bottom: 2rem;
    }

    .pagination-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-card,
    .stat-card,
    .search-filter-card {
        animation: fadeInUp 0.5s ease;
    }

    @media (max-width: 768px) {
        .donors-header h2 {
            font-size: 1.5rem;
        }

        .btn-add-donor {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }

        .table-custom {
            font-size: 0.9rem;
        }

        .blood-type-badge {
            width: 50px;
            height: 50px;
            font-size: 1rem;
        }

        .stat-card .number {
            font-size: 2rem;
        }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="donors-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="fas fa-hand-holding-heart"></i> Gestion des Donneurs</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php" style="color: rgba(255,255,255,0.8);">Accueil</a></li>
                        <li class="breadcrumb-item active">Donneurs</li>
                    </ol>
                </nav>
            </div>
            <a href="ajout_donneur.php" class="btn btn-add-donor">
                <i class="fas fa-user-plus"></i> Ajouter un donneur
            </a>
        </div>
    </div>
</div>

<div class="container mt-4">

    <!-- Statistiques -->
    <div class="row stats-row g-3">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="fas fa-users"></i> Total Donneurs</h4>
                        <div class="number"><?= $total_donneurs ?></div>
                    </div>
                    <i class="fas fa-hand-holding-heart stat-icon"></i>
                </div>
            </div>
        </div>

        <?php
        $groupes = ['A', 'B', 'AB', 'O'];
        $couleurs = ['#e74c3c', '#3498db', '#9b59b6', '#e67e22'];
        foreach($groupes as $index => $g):
            $count = isset($groupes_count[$g]) ? $groupes_count[$g] : 0;
        ?>
        <div class="col-md-2">
            <div class="stat-card">
                <h4>Groupe <?= $g ?></h4>
                <div class="number" style="color: <?= $couleurs[$index] ?>;"><?= $count ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if(empty($donneurs)): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h4>Aucun donneur enregistré</h4>
                <p>Commencez par ajouter votre premier donneur au système</p>
                <a href="ajout_donneur.php" class="btn btn-add-donor">
                    <i class="fas fa-user-plus"></i> Ajouter un donneur
                </a>
            </div>
        </div>

    <?php else: ?>

    <!-- Recherche et filtres -->
    <div class="search-filter-card">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="form-control" 
                        placeholder="Rechercher par CIN, nom, prénom..."
                        onkeyup="filterTable()"
                    >
                </div>
            </div>
            <div class="col-md-6">
                <div class="filter-group">
                    <button class="filter-btn active" onclick="filterByBloodType('ALL')">
                        <i class="fas fa-globe"></i> Tous
                    </button>
                    <button class="filter-btn" onclick="filterByBloodType('A')">A</button>
                    <button class="filter-btn" onclick="filterByBloodType('B')">B</button>
                    <button class="filter-btn" onclick="filterByBloodType('AB')">AB</button>
                    <button class="filter-btn" onclick="filterByBloodType('O')">O</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-custom" id="donorsTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-id-card"></i> CIN</th>
                        <th><i class="fas fa-user"></i> Nom Complet</th>
                        <th><i class="fas fa-tint"></i> Groupe Sanguin</th>
                        <th><i class="fas fa-plus-circle"></i> Rhésus</th>
                        <th><i class="fas fa-info-circle"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($donneurs as $d): ?>
                    <tr data-blood-type="<?= htmlspecialchars($d['groupe_sanguin']) ?>">
                        <td>
                            <span class="donor-id-badge">
                                #<?= str_pad($d['id_donneur'], 3, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <span class="cin-badge">
                                <?= htmlspecialchars($d['cin']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="donor-name">
                                <i class="fas fa-user-circle" style="color: #dc3545; margin-right: 0.5rem;"></i>
                                <?= htmlspecialchars($d['nom']) ?> <?= htmlspecialchars($d['prenom']) ?>
                            </div>
                        </td>
                        <td>
                            <div class="blood-type-badge">
                                <?= htmlspecialchars($d['groupe_sanguin']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="rhesus-badge <?= $d['rhesus'] == '+' ? 'rhesus-positive' : 'rhesus-negative' ?>">
                                <?= $d['rhesus'] == '+' ? '<i class="fas fa-plus"></i> Positif' : '<i class="fas fa-minus"></i> Négatif' ?>
                            </span>
                        </td>
                        <td>
                            <a href="detail.php?id=<?= $d['id_donneur'] ?>" 
                               class="btn btn-details"
                               title="Voir les détails">
                               <i class="fas fa-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Info pagination -->
        <div class="pagination-info">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Affichage de <strong id="visibleCount"><?= $total_donneurs ?></strong> donneur(s)
            </small>
            <small class="text-muted">
                Total : <strong><?= $total_donneurs ?></strong> donneurs enregistrés
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
let currentFilter = 'ALL';

function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('donorsTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const bloodType = row.getAttribute('data-blood-type');
        const cells = row.getElementsByTagName('td');
        let textMatch = false;

        // Vérifier si le texte correspond
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                textMatch = true;
                break;
            }
        }

        // Vérifier le filtre de groupe sanguin
        const bloodMatch = currentFilter === 'ALL' || bloodType === currentFilter;

        // Afficher si les deux conditions sont remplies
        if (textMatch && bloodMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }

    document.getElementById('visibleCount').textContent = visibleCount;
}

function filterByBloodType(type) {
    currentFilter = type;

    // Mettre à jour les boutons actifs
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Appliquer le filtre
    filterTable();
}
</script>

<?php include '../template/footer.php'; ?>