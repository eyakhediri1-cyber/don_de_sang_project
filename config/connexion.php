<?php
require_once 'base_donnees.php';//Pour inclure une seule fois

try{
$dsn="mysql:host=".DB_HOST.";dbname=".DB_NAME;  //DSN(Data Source Name)
// Options PDO pour sécurité et compatibilité
$options=[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,            // Yaffichi les erreurs SQL bech ma yoskotch 3lehom/ATTR=attribute/::=opérateur d'accés
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,       // Récupération des résultats sous forme de tableau associatif
        PDO::ATTR_EMULATE_PREPARES=>false,                    // Active les vraies requêtes préparées bech l SGBD il ygeri les requêtes préparées directement,ken true,php howa yesimuli ces requêtes
    ];
    $pdo=new PDO($dsn,DB_USER,DB_PASS,$options);//new pdo=l'ouverture ta3 l connexion

    //echo "Connexion à la base de données établie avec succès !";

} catch (PDOException $e) {
    die("Erreur lors de la connexion à la base de données: ".$e->getMessage());
}
//Rq: PDO=PHP data object 
//PDOException: est une classe d'exception en php
//$e: l'objet qui contient l'erreur
//die: arrête le script et affiche le msg
//$e->getMessage(): menghir hethi mana3rach 3lech la connexion a échouée