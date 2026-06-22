<?php
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Proses tambah kriteria
if (isset($_POST['tambah_kriteria'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kriteria']);
    $tipe = $_POST['tipe'];
    mysqli_query($conn, "INSERT INTO user_kriteria_dinamis (user_id, nama_kriteria, tipe) VALUES ('$username','$nama','$tipe')");
    echo "<script>alert('✅ Kriteria berhasil ditambahkan!'); window.location='kelola_kriteria.php';</script>";
    exit;
}

// Proses hapus kriteria
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM user_kriteria_dinamis WHERE id='$id' AND user_id='$username'");
    mysqli_query($conn, "DELETE FROM user_nilai_dinamis WHERE kriteria_id='$id' AND user_id='$username'");
    echo "<script>alert('🗑️ Kriteria dihapus!'); window.location='kelola_kriteria.php';</script>";
    exit;
}

// Ambil semua kriteria dinamis user
$kriteria_dinamis = mysqli_query($conn, "SELECT * FROM user_kriteria_dinamis WHERE user_id='$username' ORDER BY id ASC");

// Ambil semua handphone (alternatif)
$alternatif = mysqli_query($conn, "SELECT * FROM handphone ORDER BY id ASC");

// Proses simpan nilai dinamis
if (isset($_POST['simpan_nilai'])) {
    foreach ($_POST['nilai'] as $kriteria_id => $nilai_per_alternatif) {
        foreach ($nilai_per_alternatif as $alt_id => $nilai) {
            $nilai = floatval($nilai);
            $cek = mysqli_query($conn, "SELECT id FROM user_nilai_dinamis WHERE user_id='$username' AND alternatif_id='$alt_id' AND kriteria_id='$kriteria_id'");
            if (mysqli_num_rows($cek) > 0) {
                mysqli_query($conn, "UPDATE user_nilai_dinamis SET nilai='$nilai' WHERE user_id='$username' AND alternatif_id='$alt_id' AND kriteria_id='$kriteria_id'");
            } else {
                mysqli_query($conn, "INSERT INTO user_nilai_dinamis (user_id, alternatif_id, kriteria_id, nilai) VALUES ('$username','$alt_id','$kriteria_id','$nilai')");
            }
        }
    }
    echo "<script>alert('💾 Nilai kriteria berhasil disimpan!'); window.location='kelola_kriteria.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kriteria - Ratu Handphone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/animated-bg.css">
    <style>
        .kriteria-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .kriteria-card .card-header {
            background: linear-gradient(135deg, #f8bbd0, #bbdefb);
            padding: 15px 20px;
            font-weight: bold;
        }
        .btn-tambah {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            transition: transform 0.3s;
        }
        .btn-tambah:hover {
            transform: translateY(-2px);
        }
        .btn-simpan {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            transition: transform 0.3s;
        }
        .btn-simpan:hover {
            transform: translateY(-2px);
        }
        .btn-hapus {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 12px;
        }
        .badge-benefit {
            background: linear-gradient(135deg, #28a745, #20c997);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
        }
        .badge-cost {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
        }
        .table-kriteria th {
            background: linear-gradient(135deg, #f8bbd0, #bbdefb);
            padding: 12px;
        }
        .nilai-input {
            width: 100px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 8px;
        }
        .nilai-input:focus {
            outline: none;
            border-color: #ec407a;
            box-shadow: 0 0 5px rgba(236,64,122,0.3);
        }
        .info-kriteria {
            background: linear-gradient(135deg, #e8f4f8, #fff3e0);
            border-left: 5px solid #ec407a;
        }
        .kriteria-item-dinamis {
            transition: all 0.3s;
        }
        .kriteria-item-dinamis:hover {
            transform: translateX(5px);
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white"><i class="bi bi-pencil-square"></i> Kelola Kriteria Dinamis</h2>
                    <p class="text-white-50">Tambahkan kriteria sendiri sesuai kebutuhan Anda</p>
                </div>
                <a href="index.php" class="btn btn-light rounded-pill">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>

            <!-- Form Tambah Kriteria -->
            <div class="kriteria-card">
                <div class="card-header">
                    <i class="bi bi-plus-circle"></i> Tambah Kriteria Baru
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Desain, Garansi, Fitur AI" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipe</label>
                            <select name="tipe" class="form-select">
                                <option value="benefit">Benefit (semakin besar semakin baik)</option>
                                <option value="cost">Cost (semakin kecil semakin baik)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="tambah_kriteria" class="btn btn-tambah text-white w-100">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daftar Kriteria yang Sudah Ada -->
            <div class="kriteria-card mt-4">
                <div class="card-header">
                    <i class="bi bi-list-ul"></i> Daftar Kriteria Saya
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($kriteria_dinamis) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kriteria</th>
                                        <th>Tipe</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=1; while($k = mysqli_fetch_assoc($kriteria_dinamis)): ?>
                                    <tr class="kriteria-item-dinamis">
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($k['nama_kriteria']) ?></strong></td>
                                        <td>
                                            <?php if($k['tipe'] == 'benefit'): ?>
                                                <span class="badge-benefit">Benefit 📈</span>
                                            <?php else: ?>
                                                <span class="badge-cost">Cost 📉</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?hapus=<?= $k['id'] ?>" class="btn btn-hapus text-white" onclick="return confirm('Yakin hapus kriteria ini? Data nilai akan hilang.')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> Belum ada kriteria tambahan. Silakan tambah kriteria di atas!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Input Nilai untuk Kriteria Dinamis -->
            <?php if (mysqli_num_rows($kriteria_dinamis) > 0 && mysqli_num_rows($alternatif) > 0): ?>
            <div class="kriteria-card mt-4">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Input Nilai untuk Kriteria Dinamis
                </div>
                <div class="card-body">
                    <div class="info-kriteria p-3 mb-4 rounded">
                        <i class="bi bi-info-circle-fill text-primary"></i>
                        <strong>Petunjuk:</strong> Isi nilai untuk setiap handphone pada kriteria yang sudah Anda tambahkan. Nilai bisa berupa angka (1-100) atau sesuai skala penilaian Anda.
                    </div>
                    
                    <form method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered table-kriteria">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Handphone</th>
                                        <?php 
                                        $kriteria_list = [];
                                        $kriteria_reset = mysqli_query($conn, "SELECT * FROM user_kriteria_dinamis WHERE user_id='$username'");
                                        while($k = mysqli_fetch_assoc($kriteria_reset)): 
                                            $kriteria_list[] = $k;
                                        ?>
                                            <th><?= htmlspecialchars($k['nama_kriteria']) ?> <br><small class="text-muted">(<?= $k['tipe'] ?>)</small></th>
                                        <?php endwhile; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no_alt = 1;
                                    $alternatif_reset = mysqli_query($conn, "SELECT * FROM handphone ORDER BY id ASC");
                                    while($alt = mysqli_fetch_assoc($alternatif_reset)): 
                                    ?>
                                    <tr>
                                        <td><?= $no_alt++ ?></td>
                                        <td><strong><?= htmlspecialchars($alt['nama']) ?></strong></td>
                                        <?php foreach($kriteria_list as $k): 
                                            // Ambil nilai yang sudah tersimpan
                                            $nilai_query = mysqli_query($conn, "SELECT nilai FROM user_nilai_dinamis WHERE user_id='$username' AND alternatif_id='{$alt['id']}' AND kriteria_id='{$k['id']}'");
                                            $nilai_data = mysqli_fetch_assoc($nilai_query);
                                            $nilai = $nilai_data ? $nilai_data['nilai'] : '';
                                        ?>
                                        <td>
                                            <input type="number" step="any" name="nilai[<?= $k['id'] ?>][<?= $alt['id'] ?>]" 
                                                   value="<?= $nilai ?>" class="nilai-input" placeholder="0">
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" name="simpan_nilai" class="btn btn-simpan text-white">
                                <i class="bi bi-save"></i> Simpan Semua Nilai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php elseif(mysqli_num_rows($alternatif) == 0): ?>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i> Belum ada data handphone. Silakan tambah handphone terlebih dahulu di menu Data Handphone.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>