<?php
require_once '../includes/config.php';
protect_page();

if (!is_admin()) {
    header('Location: ../login.php');
    exit();
}

// Ajout d'un étudiant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $matricule = trim($_POST['matricule']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $formation_id = intval($_POST['formation_id']);
    $password = password_hash('etudiant123', PASSWORD_BCRYPT); // Mot de passe par défaut
    
    try {
        $query = $db->prepare("INSERT INTO etudiants (matricule, nom, prenom, adresse, telephone, password, formation_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$matricule, $nom, $prenom, $adresse, $telephone, $password, $formation_id]);
        $success = "Étudiant ajouté avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout: " . $e->getMessage();
    }
}

// Récupérer la liste des étudiants
$query = $db->query("
    SELECT e.*, f.libelle as formation 
    FROM etudiants e
    JOIN formations f ON e.formation_id = f.id
    ORDER BY e.nom, e.prenom
");
$etudiants = $query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les formations pour le formulaire
$query = $db->query("SELECT * FROM formations ORDER BY libelle");
$formations = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des étudiants</title>
    <style>
        /* Styles similaires à dashboard.php */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            padding: 2rem;
        }
        .menu {
            background-color: #333;
            overflow: hidden;
        }
        .menu a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .menu a:hover {
            background-color: #ddd;
            color: black;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .form-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 0.75rem 1.5rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion des étudiants</h1>
        <a href="../logout.php" style="color: white;">Déconnexion</a>
    </div>

    <div class="menu">
        <a href="dashboard.php">Accueil</a>
        <a href="students.php">Étudiants</a>
        <a href="formations.php">Formations</a>
        <a href="matieres.php">Matières</a>
        <a href="notes.php">Notes</a>
        <a href="administrateurs.php">Administrateurs</a>
    </div>

    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2>Ajouter un étudiant</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="matricule">Matricule</label>
                    <input type="text" id="matricule" name="matricule" required>
                </div>
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse">
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone">
                </div>
                <div class="form-group">
                    <label for="formation_id">Formation</label>
                    <select id="formation_id" name="formation_id" required>
                        <option value="">Sélectionnez une formation</option>
                        <?php foreach ($formations as $formation): ?>
                            <option value="<?= $formation['id'] ?>"><?= htmlspecialchars($formation['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="ajouter">Ajouter</button>
            </form>
        </div>

        <h2>Liste des étudiants</h2>
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Formation</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $etudiant): ?>
                <tr>
                    <td><?= htmlspecialchars($etudiant['matricule']) ?></td>
                    <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                    <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                    <td><?= htmlspecialchars($etudiant['formation']) ?></td>
                    <td><?= htmlspecialchars($etudiant['telephone']) ?></td>
                    <td>
                        <a href="edit_student.php?id=<?= $etudiant['id'] ?>">Modifier</a> | 
                        <a href="view_notes.php?etudiant_id=<?= $etudiant['id'] ?>">Notes</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>