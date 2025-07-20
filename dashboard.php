<?php
require_once '../includes/config.php';
protect_page();

if (!is_admin()) {
    header('Location: ../login.php');
    exit();
}

// Récupérer le nombre d'étudiants
$query = $db->query("SELECT COUNT(*) as total FROM etudiants");
$total_etudiants = $query->fetch(PDO::FETCH_ASSOC)['total'];

// Récupérer le nombre de formations
$query = $db->query("SELECT COUNT(*) as total FROM formations");
$total_formations = $query->fetch(PDO::FETCH_ASSOC)['total'];

// Récupérer les dernières notes ajoutées
$query = $db->query("
    SELECT e.nom, e.prenom, m.libelle as matiere, n.note, n.date_creation 
    FROM notes n
    JOIN etudiants e ON n.etudiant_id = e.id
    JOIN matieres m ON n.matiere_id = m.id
    ORDER BY n.date_creation DESC
    LIMIT 5
");
$dernieres_notes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Admin</title>
    <style>
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
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #333;
        }
        .stat-card p {
            font-size: 2rem;
            margin: 0.5rem 0 0;
            color: #4CAF50;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Tableau de bord - Administrateur</h1>
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
        <div class="stats">
            <div class="stat-card">
                <h3>Étudiants</h3>
                <p><?= $total_etudiants ?></p>
            </div>
            <div class="stat-card">
                <h3>Formations</h3>
                <p><?= $total_formations ?></p>
            </div>
        </div>

        <h2>Dernières notes ajoutées</h2>
        <table>
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Matière</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dernieres_notes as $note): ?>
                <tr>
                    <td><?= htmlspecialchars($note['prenom'] . ' ' . $note['nom']) ?></td>
                    <td><?= htmlspecialchars($note['matiere']) ?></td>
                    <td><?= htmlspecialchars($note['note']) ?></td>
                    <td><?= date('d/m/Y', strtotime($note['date_creation'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>