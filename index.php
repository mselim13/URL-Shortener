<?php
require 'db.php';

// Son 10 URL’yi çek
$stmt = $conn->prepare("SELECT * FROM urls ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>URL Kısaltıcı</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mselim13/URL-Shortener@main/style.css">
</head>
<body>

<div class="background">
    <img src="./undraw_link-shortener_9ro5.svg" alt="">
</div>

<div class="container">
    <h1>URL Kısaltıcı</h1>
    <form id="shortenForm">
        <label for="longUrl">Uzun URL:</label>
        <input type="url" id="longUrl" required placeholder="Uzun URL'yi buraya yazın">
        <button type="submit"><img src="./logo.svg" alt=""></button>
    </form>

    <div id="shortUrlContainer" style="display: none;">
        <p>Kısa URL: <span id="shortUrl"></span></p>
    </div>

    <h2>Geçmiş Kısa URL'ler</h2>
    <table id="recentTable" border="1" style="margin-top: 20px; width: 100%;">
        <tr>
            <th>Kod</th>
            <th>Uzun URL</th>
            <th>Oluşturulma</th>
        </tr>
        <?php foreach ($urls as $url): ?>
        <tr>
            <td><a href="redirect.php?code=<?= htmlspecialchars($url['code']) ?>" target="_blank"><?= htmlspecialchars($url['code']) ?></a></td>
            <td style="word-break: break-word;"><?= htmlspecialchars($url['long_url']) ?></td>
            <td><?= $url['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("shortenForm");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const longUrl = document.getElementById("longUrl").value;

            fetch("http://localhost/url-shortener/shorten.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "longUrl=" + encodeURIComponent(longUrl)
            })
                .then(response => response.json())
                .then(data => {
                    const shortUrlSpan = document.getElementById("shortUrl");
                    const shortUrlContainer = document.getElementById("shortUrlContainer");

                    if (data.shortUrl) {
                        shortUrlSpan.innerHTML = `<a href="${data.shortUrl}" target="_blank">${data.shortUrl}</a>`;
                        shortUrlContainer.style.display = "block";

                        // Tabloya ekle
                        const table = document.getElementById("recentTable");
                        const newRow = table.insertRow(1);
                        const shortCode = data.shortUrl.split("code=")[1] || "???";
                        const now = new Date().toLocaleString("tr-TR");

                        newRow.innerHTML = `
                            <td><a href="${data.shortUrl}" target="_blank">${shortCode}</a></td>
                            <td style="word-break: break-word;">${longUrl}</td>
                            <td>${now}</td>
                        `;

                        // 10'dan fazla satır varsa sil
                        while (table.rows.length > 11) {
                            table.deleteRow(-1);
                        }
                    } else {
                        shortUrlSpan.innerText = "Bir hata oluştu: " + (data.error || "Bilinmeyen hata");
                        shortUrlContainer.style.display = "block";
                    }
                })
                .catch(error => console.error("Hata oluştu:", error));
        });
    }
});
</script>

</body>
</html>
