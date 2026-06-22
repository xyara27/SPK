<?php
require 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

$query = mysqli_query($conn, "SELECT * FROM user_bobot WHERE user_id='$username'");
$bobot = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bh = floatval($_POST['bobot_harga']);
    $br = floatval($_POST['bobot_ram']);
    $bs = floatval($_POST['bobot_storage']);
    $bb = floatval($_POST['bobot_baterai']);
    $bk = floatval($_POST['bobot_kamera']);
    
    $total = $bh + $br + $bs + $bb + $bk;
    
    if ($bobot) {
        mysqli_query($conn, "UPDATE user_bobot SET 
            bobot_harga='$bh', bobot_ram='$br', bobot_storage='$bs', 
            bobot_baterai='$bb', bobot_kamera='$bk' 
            WHERE user_id='$username'");
    } else {
        mysqli_query($conn, "INSERT INTO user_bobot (user_id, bobot_harga, bobot_ram, bobot_storage, bobot_baterai, bobot_kamera) 
            VALUES ('$username','$bh','$br','$bs','$bb','$bk')");
    }
    
    // Notifikasi setelah simpan
    if ($total != 1.00) {
        echo "<script>
            alert('⚠️ PERHATIAN!\\n\\nBobot berhasil disimpan, tetapi total bobot Anda = " . number_format($total, 2) . "\\n\\n📌 Disarankan total bobot = 1.00 untuk hasil akurat.\\nContoh: 0.25 + 0.25 + 0.20 + 0.15 + 0.15 = 1.00\\n\\nSilakan atur ulang bobot Anda.');
            window.location.href = 'bobot_user.php';
        </script>";
    } else {
        echo "<script>
            alert('✅ SUKSES!\\n\\nBobot kriteria berhasil disimpan!\\nTotal bobot = " . number_format($total, 2) . " (IDEAL)\\n\\nAnda akan diarahkan ke halaman Hitung TOPSIS.');
            window.location.href = 'index.php?page=hitung';
        </script>";
    }
    exit;
}

$default_bobot = [
    'harga' => 0.25,
    'ram' => 0.25,
    'storage' => 0.20,
    'baterai' => 0.15,
    'kamera' => 0.15
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Bobot Kriteria - Ratu Handphone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding: 20px 0;
        }
        
        .bobot-card {
            background: rgba(255,255,255,0.95);
            border-radius: 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: none;
            overflow: hidden;
            margin-top: 30px;
        }
        .bobot-card .card-header {
            background: linear-gradient(135deg, #f8bbd0, #bbdefb);
            padding: 25px;
            text-align: center;
        }
        .btn-simpan {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 14px;
            font-weight: bold;
            border-radius: 30px;
            transition: transform 0.3s;
            color: white;
        }
        .btn-simpan:hover {
            transform: scale(1.02);
            color: white;
        }
        .btn-kembali {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            transition: transform 0.3s;
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-kembali:hover {
            transform: translateY(-2px);
            color: white;
        }
        .kriteria-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 18px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .kriteria-item:hover {
            transform: translateX(8px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-range {
            width: 100%;
            height: 6px;
            -webkit-appearance: none;
            background: linear-gradient(90deg, #ec407a, #d81b60);
            border-radius: 5px;
            outline: none;
            margin: 15px 0;
        }
        .form-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #ec407a, #d81b60);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border: 2px solid white;
        }
        .badge-benefit {
            background: linear-gradient(135deg, #28a745, #20c997);
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 11px;
        }
        .badge-cost {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-size: 11px;
        }
        .total-bobot {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 30px;
            padding: 12px 20px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .total-bobot.warning {
            background: linear-gradient(135deg, #ff9800, #f57c00);
        }
        .total-bobot.danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .info-bobot {
            background: #e8f4f8;
            border-radius: 10px;
            padding: 12px;
            font-size: 13px;
            border-left: 4px solid #ec407a;
        }
        .nilai-bobot {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .bobot-card {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="bobot-card">
                <div class="card-header">
                    <i class="bi bi-sliders2" style="font-size: 2.5rem; color:#6a1b2a;"></i>
                    <h2 class="mb-0 mt-2" style="color:#6a1b2a;">Atur Bobot Kriteria</h2>
                    <p class="text-muted mt-2">Sesuaikan bobot sesuai preferensi Anda (0-1)</p>
                </div>
                <div class="card-body p-4">
                    <div class="info-bobot mb-4">
                        <i class="bi bi-lightbulb-fill text-warning"></i>
                        <strong>📌 Cara membaca bobot:</strong> Angka 0 sampai 1<br>
                        • 0.25 = 25% &nbsp;&nbsp; • 0.50 = 50% &nbsp;&nbsp; • 0.75 = 75% &nbsp;&nbsp; • 1.00 = 100%<br>
                        <strong>✅ Total bobot yang disarankan = 1.00 (100%)</strong><br>
                        Contoh ideal: 0.25 + 0.25 + 0.20 + 0.15 + 0.15 = 1.00
                    </div>
                    
                    <form method="POST" id="bobotForm">
                        <div class="kriteria-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold"><i class="bi bi-cash-stack text-success"></i> Harga <span class="badge-cost ms-2">Cost</span></label>
                                <span class="nilai-bobot" id="harga_value"><?= number_format($bobot ? $bobot['bobot_harga'] : $default_bobot['harga'], 2) ?></span>
                            </div>
                            <input type="range" name="bobot_harga" class="form-range" min="0" max="1" step="0.01" 
                                   value="<?= $bobot ? $bobot['bobot_harga'] : $default_bobot['harga'] ?>" 
                                   oninput="document.getElementById('harga_value').innerText = parseFloat(this.value).toFixed(2); updateTotal()">
                            <small class="text-muted">Semakin kecil harga, semakin baik (Cost)</small>
                        </div>

                        <div class="kriteria-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold"><i class="bi bi-memory text-primary"></i> RAM <span class="badge-benefit ms-2">Benefit</span></label>
                                <span class="nilai-bobot" id="ram_value"><?= number_format($bobot ? $bobot['bobot_ram'] : $default_bobot['ram'], 2) ?></span>
                            </div>
                            <input type="range" name="bobot_ram" class="form-range" min="0" max="1" step="0.01" 
                                   value="<?= $bobot ? $bobot['bobot_ram'] : $default_bobot['ram'] ?>" 
                                   oninput="document.getElementById('ram_value').innerText = parseFloat(this.value).toFixed(2); updateTotal()">
                            <small class="text-muted">Semakin besar RAM, semakin baik (Benefit)</small>
                        </div>

                        <div class="kriteria-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold"><i class="bi bi-hdd-stack text-warning"></i> Storage <span class="badge-benefit ms-2">Benefit</span></label>
                                <span class="nilai-bobot" id="storage_value"><?= number_format($bobot ? $bobot['bobot_storage'] : $default_bobot['storage'], 2) ?></span>
                            </div>
                            <input type="range" name="bobot_storage" class="form-range" min="0" max="1" step="0.01" 
                                   value="<?= $bobot ? $bobot['bobot_storage'] : $default_bobot['storage'] ?>" 
                                   oninput="document.getElementById('storage_value').innerText = parseFloat(this.value).toFixed(2); updateTotal()">
                            <small class="text-muted">Semakin besar Storage, semakin baik (Benefit)</small>
                        </div>

                        <div class="kriteria-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold"><i class="bi bi-battery-full text-info"></i> Baterai <span class="badge-benefit ms-2">Benefit</span></label>
                                <span class="nilai-bobot" id="baterai_value"><?= number_format($bobot ? $bobot['bobot_baterai'] : $default_bobot['baterai'], 2) ?></span>
                            </div>
                            <input type="range" name="bobot_baterai" class="form-range" min="0" max="1" step="0.01" 
                                   value="<?= $bobot ? $bobot['bobot_baterai'] : $default_bobot['baterai'] ?>" 
                                   oninput="document.getElementById('baterai_value').innerText = parseFloat(this.value).toFixed(2); updateTotal()">
                            <small class="text-muted">Semakin besar baterai, semakin baik (Benefit)</small>
                        </div>

                        <div class="kriteria-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold"><i class="bi bi-camera text-danger"></i> Kamera <span class="badge-benefit ms-2">Benefit</span></label>
                                <span class="nilai-bobot" id="kamera_value"><?= number_format($bobot ? $bobot['bobot_kamera'] : $default_bobot['kamera'], 2) ?></span>
                            </div>
                            <input type="range" name="bobot_kamera" class="form-range" min="0" max="1" step="0.01" 
                                   value="<?= $bobot ? $bobot['bobot_kamera'] : $default_bobot['kamera'] ?>" 
                                   oninput="document.getElementById('kamera_value').innerText = parseFloat(this.value).toFixed(2); updateTotal()">
                            <small class="text-muted">Semakin besar kamera, semakin baik (Benefit)</small>
                        </div>

                        <div class="total-bobot mt-4" id="totalBobotDisplay">
                            Total Bobot: <?= number_format(($bobot ? ($bobot['bobot_harga'] + $bobot['bobot_ram'] + $bobot['bobot_storage'] + $bobot['bobot_baterai'] + $bobot['bobot_kamera']) : 1.00), 2) ?>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <a href="index.php?page=hitung" class="btn-kembali"><i class="bi bi-arrow-left"></i> Kembali</a>
                            <button type="submit" class="btn-simpan flex-grow-1"><i class="bi bi-save"></i> Simpan Bobot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTotal() {
    let harga = parseFloat(document.querySelector('input[name="bobot_harga"]').value) || 0;
    let ram = parseFloat(document.querySelector('input[name="bobot_ram"]').value) || 0;
    let storage = parseFloat(document.querySelector('input[name="bobot_storage"]').value) || 0;
    let baterai = parseFloat(document.querySelector('input[name="bobot_baterai"]').value) || 0;
    let kamera = parseFloat(document.querySelector('input[name="bobot_kamera"]').value) || 0;
    let total = harga + ram + storage + baterai + kamera;
    
    let displayDiv = document.getElementById('totalBobotDisplay');
    let formattedTotal = total.toFixed(2);
    
    displayDiv.innerHTML = `📊 Total Bobot: ${formattedTotal}`;
    
    if (Math.abs(total - 1.00) < 0.01) {
        displayDiv.className = 'total-bobot mt-4';
    } else if (Math.abs(total - 1.00) < 0.1) {
        displayDiv.className = 'total-bobot warning mt-4';
    } else {
        displayDiv.className = 'total-bobot danger mt-4';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>