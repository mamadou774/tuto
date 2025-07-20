<?php
require_once '../includes/config.php';
protect_page();

if (!is_etudiant()) {
    header('Location: ../login.php');
    exit();
}

// Récupérer les notes avec dates
$query = $db->prepare("
    SELECT n.note, m.libelle as matiere, n.date_creation 
    FROM notes n
    JOIN matieres m ON n.matiere_id = m.id
    WHERE n.etudiant_id = ?
    ORDER BY n.date_creation
");
$query->execute([$_SESSION['user_id']]);
$notes = $query->fetchAll(PDO::FETCH_ASSOC);

// Préparer les données pour le graphique
$matieres = [];
$data = [];
foreach ($notes as $note) {
    if (!isset($data[$note['matiere']])) {
        $matieres[] = $note['matiere'];
        $data[$note['matiere']] = [];
    }
    $data[$note['matiere']][] = [
        'date' => date('d/m/Y', strtotime($note['date_creation'])),
        'note' => $note['note']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma progression</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: 20px auto;
        }
        .chart-box {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1>Ma progression</h1>
        
        <?php if (empty($notes)): ?>
            <p>Vous n'avez pas encore de notes.</p>
        <?php else: ?>
            <?php foreach ($matieres as $matiere): ?>
                <div class="chart-box">
                    <h2><?= htmlspecialchars($matiere) ?></h2>
                    <div class="chart-container">
                        <canvas id="chart-<?= md5($matiere) ?>"></canvas>
                    </div>
                </div>
                
                <script>
                    const ctx<?= md5($matiere) ?> = document.getElementById('chart-<?= md5($matiere) ?>').getContext('2d');
                    const chart<?= md5($matiere) ?> = new Chart(ctx<?= md5($matiere) ?>, {
                        type: 'line',
                        data: {
                            labels: [<?= implode(',', array_map(function($item) { return "'".$item['date']."'"; }, $data[$matiere])) ?>],
                            datasets: [{
                                label: 'Notes en <?= $matiere ?>',
                                data: [<?= implode(',', array_map(function($item) { return $item['note']; }, $data[$matiere])) ?>],
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    min: 0,
                                    max: 20
                                }
                            }
                        }
                    });
                </script>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html> 