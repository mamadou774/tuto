<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect_to_dashboard();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    
    // Vérification étudiant
    $query = $db->prepare("SELECT * FROM etudiants WHERE matricule = ?");
    $query->execute([$login]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = 'etudiant';
        $_SESSION['matricule'] = $user['matricule'];
        redirect_to_dashboard();
    }
    
    // Vérification admin
    $query = $db->prepare("SELECT * FROM administrateurs WHERE email = ?");
    $query->execute([$login]);
    $admin = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['email'] = $admin['email'];
        redirect_to_dashboard();
    }
    
    $error = "Identifiants incorrects";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Connexion</h1>
        <?php if (!empty($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="login">Matricule/Email</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>