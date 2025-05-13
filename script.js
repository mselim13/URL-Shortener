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

                        // 🎯 Son 10 link tablosunu güncelle
                        const table = document.getElementById("recentTable");
                        if (table) {
                            const newRow = table.insertRow(1); // Başlık satırından sonra ekle
                            newRow.innerHTML = `
                                <td><a href="${data.shortUrl}" target="_blank">${data.code}</a></td>
                                <td>${data.longUrl}</td>
                                <td>${data.created_at}</td>
                            `;

                            // 🔟 10'dan fazla satır varsa sonuncuyu sil
                            while (table.rows.length > 11) { // 1 başlık + 10 veri satırı
                                table.deleteRow(-1);
                            }
                        }
                    } else {
                        shortUrlSpan.innerText = "Bir hata oluştu: " + (data.error || "Bilinmeyen hata");
                        shortUrlContainer.style.display = "block";
                    }
                })
                .catch(error => console.error("Hata oluştu:", error));
        });
    } else {
        console.error("Form elemanı bulunamadı! ID yanlış olabilir.");
    }
});
