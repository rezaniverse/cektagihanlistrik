<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cek Tagihan Listrik</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f1f1f1;
    }

    .container {
      margin: 0 auto;
      background-color: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
    }

    .form-group label {
      font-weight: bold;
      color: #555;
    }

    .form-group input[type="text"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 5px;
      margin-bottom: 20px;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 5px;
      background-color: #007bff;
      color: #fff;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn-primary:hover {
      background-color: #0069d9;
    }
  </style>
</head>

<body>
  <div class="container col-sm-6 mt-2">
    <form id="tagihanForm">
      <div class="form-group">
        <label for="idpel">ID Pelanggan</label>
        <input type="text" class="form-control" id="idpel" placeholder="Masukkan ID Pelanggan">
      </div>
      <button type="button" id="cekTagihanButton" class="btn btn-primary" onclick="cekTagihan()">Cek Tagihan</button>
    </form>
  </div>
  <div id="ResultTagihan" class="container col-sm-6 mt-2 mb-2"></div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script>
    const apikey = "apikey"; //default apikey is apikey
    let response = null;

    function formatRupiah(angka) {
      var formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      });
      return formatter.format(angka);
    }

    function cekTagihan() {
      var idPelanggan = document.getElementById("idpel").value;
      if (idPelanggan === "") {
        alert("ID Pelanggan tidak boleh kosong");
        return;
      }

      var cekTagihanButton = document.getElementById("cekTagihanButton");
      cekTagihanButton.innerHTML = "Loading...";
      cekTagihanButton.disabled = true;

      var apiUrl = "https://api.rzptra.my.id/api/api_taglistrik.php?apikey=" + apikey + "&idpel=" + idPelanggan;
      var proxyUrl = "proxy.php?url=" + encodeURIComponent(apiUrl);

      var xhr = new XMLHttpRequest();
      xhr.open("GET", proxyUrl, true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            response = JSON.parse(xhr.responseText);
            console.log(response);
            var resultContainer = document.getElementById("ResultTagihan");
            resultContainer.innerHTML = "";

            if (response.status === true) {
              var tableHtml = `
                <table class="table table-bordered">
                  <tr>
                    <th>Atas Nama</th>
                    <td>${response.data.atasnama}</td>
                  </tr>
                  <tr>
                    <th>ID Pelanggan</th>
                    <td>${response.data.id_pelanggan}</td>
                  </tr>
                  <tr>
                    <th>Tagihan Bulan</th>
                    <td>${response.data.tagihan_bulan}</td>
                  </tr>
                  <tr>
                    <th>Total Tagihan</th>
                    <td>${formatRupiah(response.data.total_tagihan)}</td>
                  </tr>
                  <tr>
                    <th>Total Terbilang</th>
                    <td>${response.data.tot_terbilang}</td>
                  </tr>
                </table>
              `;
              resultContainer.innerHTML = tableHtml;

              var whatsappButton = document.createElement("button");
              whatsappButton.setAttribute("type", "button");
              whatsappButton.setAttribute("class", "btn btn-primary");
              whatsappButton.setAttribute("onclick", "bagikanWhatsApp()");
              whatsappButton.innerText = "Bagikan ke WhatsApp";
              resultContainer.appendChild(whatsappButton);
            } else {
              resultContainer.innerHTML = response.data.title;
            }

            cekTagihanButton.innerHTML = "Cek Tagihan";
            cekTagihanButton.disabled = false;
          } else {
            var resultContainer = document.getElementById("ResultTagihan");
            resultContainer.innerHTML = "Terjadi kesalahan saat mengambil data.";

            cekTagihanButton.innerHTML = "Cek Tagihan";
            cekTagihanButton.disabled = false;
          }
        }
      };
      xhr.send();
    }

    function bagikanWhatsApp() {
      var idPelanggan = document.getElementById("idpel").value;
      var totalTagihan = response.data.total_tagihan;
      var atasNama = response.data.atasnama;
      var tagBulan = response.data.tagihan_bulan;

      var currentDateTime = new Date();
      var formattedDateTime = currentDateTime.toLocaleString('en-US', { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });

      var tagihanText = "============= " + "RIIZE" + " ==========" + "%0A" +
        "ID Pelanggan: " + idPelanggan + "%0A" +
        "Nama Pelanggan: " + atasNama + "%0A" +
        "Tagihan bulan: " + tagBulan + "%0A" +
        "Total Tagihan: " + formatRupiah(totalTagihan) + "%0A" +
        "===== " + formattedDateTime + " =====";

      var encodedText = tagihanText;
      var whatsappURL = "https://wa.me/?text=" + encodedText;
      window.open(whatsappURL);
    }
    
    document.addEventListener("keydown", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
      }
    });

      document.addEventListener("keydown", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
  }
});

  </script>
</body>
</html>
