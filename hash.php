<?php
require_once  './config/base_donnees.php';
// hash.php - Générateur de hash de mot de passe
$password = $_GET['password'] ?? '123456'; // Mot de passe par défaut

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '123456';
}

$hash = password_hash($password, PASSWORD_DEFAULT);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Hash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-key me-2"></i>Générateur de Hash MD5
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Mot de passe à hasher :</label>
                                <input type="text" name="password" class="form-control" 
                                       value="<?= htmlspecialchars($password) ?>" 
                                       placeholder="Entrez le mot de passe" required>
                                <div class="form-text">Le mot de passe sera hashé avec l'algorithme MD5</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-bolt me-2"></i>Générer le hash
                            </button>
                        </form>

                        <?php if ($password): ?>
                        <div class="mt-4 p-4 bg-dark text-white rounded">
                            <h5 class="text-warning">
                                <i class="fas fa-code me-2"></i>Résultats :
                            </h5>
                            
                            <div class="mb-3">
                                <strong><i class="fas fa-user me-2"></i>Mot de passe original :</strong>
                                <span class="text-info"><?= htmlspecialchars($password) ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <strong><i class="fas fa-fingerprint me-2"></i>Hash généré :</strong>
                                <div class="mt-1">
                                    <code class="bg-secondary p-2 rounded d-block"><?= $hash ?></code>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong><i class="fas fa-database me-2"></i>Commande SQL prête :</strong>
                                <textarea class="form-control mt-2 bg-secondary text-white" rows="4" readonly style="font-family: monospace;">
INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, nom_complet, role, id_centre) 
VALUES ('nom_utilisateur', '<?= $hash ?>', 'Nom Complet', 'ADMIN', 1);</textarea>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Instructions :</strong> Copiez le hash et utilisez-le dans votre base de données.
                                Le mot de passe original est : <strong><?= htmlspecialchars($password) ?></strong>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <h6><i class="fas fa-flask me-2"></i>Tests rapides :</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="hash.php?password=123456" class="btn btn-outline-primary btn-sm">Test: 123456</a>
                                <a href="hash.php?password=admin" class="btn btn-outline-primary btn-sm">Test: admin</a>
                                <a href="hash.php?password=secret" class="btn btn-outline-primary btn-sm">Test: secret</a>
                                <a href="hash.php?password=password" class="btn btn-outline-primary btn-sm">Test: password</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Fonction pour copier le hash
    function copyHash() {
        const hashText = document.querySelector('code').innerText;
        navigator.clipboard.writeText(hashText).then(() => {
            alert('Hash copié dans le presse-papier !');
        });
    }

    // Auto-sélection du texte SQL
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('click', function() {
                this.select();
            });
        }
    });
    </script>
</body>
</html>