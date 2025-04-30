<?php
require 'db.php';

$stmt = $conn->prepare("SELECT * FROM analytics ORDER BY created_at DESC");
$stmt->execute();
$clicks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <meta charset="UTF-8">
    <title>Analytics Raporu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f4f4f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        h1 {
            text-align: center;
            color: #333;
        }

        .graphs{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 600px;
            gap: 150px;
            margin: 20px auto;
            margin-right: 500px;
        }

        #countryChart {
            position: absolute;
            margin-top: 200px;
        }

        #dateChart {
            position: absolute;
            margin-top: 200px;
            margin-left: 200px;
        }

        
    </style>
</head>
<body>
<h1>Tıklama Analizi</h1>

<table>
    <tr>
        <th>Kod</th>
        <th>IP Adresi</th>
        <th>Ülke</th>
        <th>Tarayıcı</th>
        <th>Tarih</th>
    </tr>
    <?php foreach ($clicks as $click): ?>
    <tr>
        <td><?= htmlspecialchars($click['code']) ?></td>
        <td><?= htmlspecialchars($click['ip_address']) ?></td>
        <td><?= htmlspecialchars($click['country']) ?></td>
        <td><?= htmlspecialchars($click['user_agent']) ?></td>
        <td><?= htmlspecialchars($click['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Bu kısım artık tablonun dışındadır -->

<div class="graphs">
    <h2>Ülkeye Göre Tıklamalar</h2>
<canvas id="countryChart" width="100" height="50"></canvas>

<h2>Tarihe Göre Tıklamalar</h2>
<canvas id="dateChart" width="200" height="50"></canvas>
</div>





        
    <?php
// Ülke bazlı sayımlar
$countryData = [];
$dateData = [];

foreach ($clicks as $click) {
    // Ülke sayacı
    $country = $click['country'] ?? 'Bilinmiyor';
    $countryData[$country] = ($countryData[$country] ?? 0) + 1;

    // Tarih (sadece gün) bazlı sayım
    $date = date("Y-m-d", strtotime($click['created_at']));
    $dateData[$date] = ($dateData[$date] ?? 0) + 1;
}
?>

<script>
    // PHP'den gelen verileri JS'ye taşıyoruz
    const countryLabels = <?php echo json_encode(array_keys($countryData)); ?>;
    const countryCounts = <?php echo json_encode(array_values($countryData)); ?>;

    const dateLabels = <?php echo json_encode(array_keys($dateData)); ?>;
    const dateCounts = <?php echo json_encode(array_values($dateData)); ?>;

    // Ülke Bazlı Pasta Grafiği
    new Chart(document.getElementById('countryChart'), {
        type: 'pie',
        data: {
            labels: countryLabels,
            datasets: [{
                data: countryCounts,
                backgroundColor: ['#f9ba31', '#4CAF50', '#3498db', '#e74c3c', '#9b59b6']
            }]
        }
    });

    // Günlük Tıklamalar Çizgi Grafiği
    new Chart(document.getElementById('dateChart'), {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'Tıklama Sayısı',
                data: dateCounts,
                borderColor: '#1a1d20',
                backgroundColor: 'rgba(26, 29, 32, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>


    

</body>
</html>
