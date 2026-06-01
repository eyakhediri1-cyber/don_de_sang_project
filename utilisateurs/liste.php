<?php
require_once '../config/connexion.php';
require_once '../config/authentification.php';

// Vérification de l'authentification et du rôle
verifierConnexion();
verifierRole('ADMIN');

try {
    $stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY id_utilisateur ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("<div class='alert alert-danger container mt-4'>
            Erreur lors de la récupération des utilisateurs : " . htmlspecialchars($e->getMessage()) . "
        </div>");
}
?>

<?php include '../template/header.php'; ?>

<style>
    .users-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem 0;
        margin: -2rem -15px 2rem -15px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .users-header h2 {
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }

    .users-header .breadcrumb {
        background: rgba(255,255,255,0.1);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        margin: 0.5rem 0 0 0;
    }

    .users-header .breadcrumb-item {
        color: rgba(255,255,255,0.8);
    }

    .users-header .breadcrumb-item.active {
        color: white;
    }

    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        border-left: 4px solid #667eea;
    }

    .stats-card h4 {
        color: #667eea;
        font-weight: 600;
        margin: 0;
    }

    .stats-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .btn-add-user {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }

    .btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        color: white;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .user-id-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
    }

    .role-badge {
        display: inline-block;
        padding: 0.5rem 1.2rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .role-medecin {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .role-secretaire {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .role-default {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #2c3e50;
    }

    .btn-action {
        padding: 0.5rem 1.2rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
        margin: 0 0.25rem;
    }

    .btn-edit {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #2c3e50;
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(253, 203, 110, 0.4);
    }

    .btn-delete {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
    }

    .btn-delete:hover {
        background: linear-gradient(135deg, #ee5a6f 0%, #c92a2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(238, 90, 111, 0.4);
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

    .search-box {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .search-box input {
        padding: 0.8rem 1.2rem 0.8rem 3rem;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .search-box i {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #636e72;
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

    .table-card {
        animation: fadeInUp 0.5s ease;
    }

    @media (max-width: 768px) {
        .users-header h2 {
            font-size: 1.5rem;
        }

        .btn-add-user {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            margin: 0.2rem;
        }

        .table-custom {
            font-size: 0.9rem;
        }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="users-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php" style="color: rgba(255,255,255,0.8);">Accueil</a></li>
                        <li class="breadcrumb-item active">Utilisateurs</li>
                    </ol>
                </nav>
            </div>
            <a href="ajout.php" class="btn btn-add-user">
                <i class="fas fa-user-plus"></i> Ajouter
            </a>
        </div>
    </div>
</div>

<div class="container mt-4">

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><i class="fas fa-users"></i> Total</h4>
                        <div class="number"><?= count($users) ?></div>
                    </div>
                    <i class="fas fa-users" style="font-size: 3rem; color: rgba(102, 126, 234, 0.2);"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if(empty($users)): ?>
        <div class="table-card">
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h4>Aucun utilisateur trouvé</h4>
                <p>Commencez par ajouter votre premier utilisateur au système</p>
                <a href="ajout.php" class="btn btn-add-user">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </a>
            </div>
        </div>

    <?php else: ?>

    <!-- Barre de recherche -->
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
            type="text" 
            id="searchInput" 
            class="form-control" 
            placeholder="Rechercher un utilisateur par nom ou rôle..."
            onkeyup="filterTable()"
        >
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-custom" id="usersTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> Nom d'utilisateur</th>
                        <th><i class="fas fa-user-tag"></i> Rôle</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($users as $u): 
                        $roleClass = 'role-default';
                        if($u['role'] === 'ADMIN') $roleClass = 'role-admin';
                        elseif($u['role'] === 'MEDECIN') $roleClass = 'role-medecin';
                        elseif($u['role'] === 'SECRETAIRE') $roleClass = 'role-secretaire';
                    ?>
                    <tr>
                        <td>
                            <span class="user-id-badge">
                                #<?= str_pad($u['id_utilisateur'], 3, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <span class="user-name">
                                <i class="fas fa-user-circle" style="color: #667eea; margin-right: 0.5rem;"></i>
                                <?= htmlspecialchars($u['nom_utilisateur']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-badge <?= $roleClass ?>">
                                <?php if($u['role'] === 'ADMIN'): ?>
                                    <i class="fas fa-crown"></i>
                                <?php elseif($u['role'] === 'MEDECIN'): ?>
                                    <i class="fas fa-user-md"></i>
                                <?php elseif($u['role'] === 'SECRETAIRE'): ?>
                                    <i class="fas fa-user-tie"></i>
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>

                        <td>
                            <a 
                                href="modifier.php?id=<?= $u['id_utilisateur'] ?>" 
                                class="btn btn-action btn-edit"
                                title="Modifier"
                            >
                                <i class="fas fa-edit"></i> Modifier
                            </a>

                            <a 
                                href="supprimer.php?id=<?= $u['id_utilisateur'] ?>" 
                                class="btn btn-action btn-delete"
                                onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet utilisateur ?\n\nCette action est irréversible.')"
                                title="Supprimer"
                            >
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <!-- Pagination info -->
        <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid #e9ecef;">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Affichage de <strong><?= count($users) ?></strong> utilisateur(s)
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }

        rows[i].style.display = found ? '' : 'none';
    }
}
</script>

<?php include '../template/footer.php'; ?>