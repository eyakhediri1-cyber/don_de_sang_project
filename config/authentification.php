<?php
session_start();
function estConnecte() {
    return isset($_SESSION['user_id']);
}
function estAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
}

function estMedecin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'MEDECIN';
}

function estSecretaire() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'SECRETAIRE';
}
function verifierConnexion() {
    if (!estConnecte()) {
        header("Location: ../login.php");
        exit;
    }
}
function verifierRole($roles) {
    if (is_string($roles)) {
        $roles = [$roles]; // transforme en tableau si nécessaire
    }

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        http_response_code(403);
        echo "Accès refusé : vous n'avez pas les droits.";
        exit;
    }
}
?>
