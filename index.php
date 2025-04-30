<?php
require 'db.php';

$stmt = $conn->prepare("SELECT * FROM urls ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>URL Kısaltıcı</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>URL Kısaltıcı</h1>
    <form id="shortenForm">
        <label for="longUrl">Uzun URL:</label>
        <input type="url" id="longUrl" required placeholder="Uzun URL'yi buraya yazın">
        <button type="submit">Kısalt</button>
    </form>

    <div id="shortUrlContainer">
        <p>Kısa URL: <span id="shortUrl"></span></p>
    </div>

    <h2>Geçmiş Kısa URL'ler</h2>
    <table>
        <tr>
            <th>Kod</th>
            <th>Uzun URL</th>
            <th>Oluşturulma</th>
        </tr>
        <?php foreach ($urls as $url): ?>
        <tr>
            <td><a href="redirect.php?code=<?php echo htmlspecialchars($url['code']); ?>" target="_blank"><?php echo htmlspecialchars($url['code']); ?></a></td>
            <td style="word-break: break-word;"><?php echo htmlspecialchars($url['long_url']); ?></td>
            <td><?php echo $url['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script src="script.js"></script>
</body>
</html>
