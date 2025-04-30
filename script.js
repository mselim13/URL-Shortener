    document.addEventListener("DOMContentLoaded", function() {
        // Formu bulmaya çalışıyoruz
        const form = document.getElementById("shortenForm");

        // Form bulundu mu? Kontrol edelim
        if (form) {
            form.addEventListener("submit", function(e) {
                e.preventDefault();  // Sayfanın yenilenmesini engelle

                const longUrl = document.getElementById("longUrl").value;  // Kullanıcının girdiği URL

                // fetch ile POST isteği gönderiyoruz
                fetch("http://localhost/url-shortener/shorten.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "longUrl=" + encodeURIComponent(longUrl)  // URL'yi gönderiyoruz
                })
                .then(response => response.json())  // JSON olarak yanıt alıyoruz
                .then(data => {
                    const shortUrlSpan = document.getElementById("shortUrl");
                    const shortUrlContainer = document.getElementById("shortUrlContainer");

                    if (data.shortUrl) {
                        shortUrlSpan.innerHTML = `<a href="${data.shortUrl}" target="_blank">${data.shortUrl}</a>`;
                        shortUrlContainer.style.display = "block";  // Kısa URL'yi göster
                    } else {
                        shortUrlSpan.innerText = "Bir hata oluştu: " + (data.error || "Bilinmeyen hata");
                        shortUrlContainer.style.display = "block";  // Hata mesajını da gösterelim
                    }
                })
                .catch(error => console.error("Hata oluştu:", error));  // Hata durumunu kontrol et  
            });
        } else {
            // Form bulunamadıysa, burada hata mesajını göreceksin.
            console.error("Form elemanı bulunamadı! ID yanlış olabilir.");
        }

        
    });
    
