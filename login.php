<?php
session_start(); // Pour démarrer la session
require_once  './config/base_donnees.php';
require_once  './config/connexion.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {

    $nom_utilisateur=trim($_POST['nom_utilisateur'] ?? '');//Au cas ou mafamech nom_utilisateur n7otou chaine vide fi blastou w ?? :  yvérifi si l variable existe w mahech null sinon ya3ti valeur pas défaut/trim tne7i les espaces zeydin fi awal w ekher chaîne
    $mot_de_passe=$_POST['mot_de_passe'] ?? '';

    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $errors[]="Tous les champs sont obligatoires.";
    } else {
        try {
            $stmt=$pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur=:nom_utilisateur");//$pdo: l'objet eli sna3tou fel connexion/prepare():protège contre les injections SQL
            $stmt->execute(['nom_utilisateur'=>$nom_utilisateur]);//Exécuter la requête avec la valeur du formulaire
            $user=$stmt->fetch();//Prend le résultat SQL et le transforme en tableau PHP prêt à être utilisé

            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                // Authentification réussie
                $_SESSION['user_id']=$user['id_utilisateur'];//Stocke l’id de l’utilisateur pour l’utiliser partout
                $_SESSION['role']=$user['role'];//Stocke le rôle pour gérer les autorisations
                $_SESSION['nom_utilisateur']=$user['nom_utilisateur'];//stocke le nom d’utilisateur pour l’afficher ou l’utiliser si besoin

                header("Location: index.php");//Dit au navigateur:« Va sur index.php maintenant »
                exit;
                //ces deux lignes le redirigent vers la page principale et
                //arrêtent le script pour que rien d’autre ne se lance
            } else {
                $errors[]="Nom d'utilisateur ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $errors[]="Erreur SQL: ".$e->getMessage();
        }
    }
}
?>

<?php include 'template/header.php'; ?>
<!-- Importe le fichier header.php, qui contient l’entête HTML -->

<div class="container mt-5">
    <h2>Connexion</h2>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <!-- Affiche un bloc rouge Bootstrap -->
            <?php foreach($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
    <!-- action="":login reçcoit et traite les propres données de la page login -->
        <div class="mb-3">
            <label for="nom_utilisateur" class="form-label">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required>
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<?php include 'template/footer.php'; ?>
<!-- Instruction PHP qui importe et exécute le code d’un autre fichier à cet endroit précis -->
