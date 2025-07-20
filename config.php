<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_notes');

// Connexion à la base de données
try {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES 'utf8'");
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Démarrer la session
session_start();

// Fonctions utilitaires
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_to_dashboard() {
    if ($_SESSION['user_type'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: etudiant/dashboard.php');
    }
    exit();
}

function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
}

function is_etudiant() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'etudiant';
}

function protect_page() {
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit();
    }
}

function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}
?>