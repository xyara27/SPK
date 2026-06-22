<?php
ob_start();
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratu Handphone - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/animated-bg.css">
    <style>
        .galeri-card { position: relative; }
        .card-dashboard, .galeri-card, .btn-pink { transition: all 0.3s ease-in-out !important; }
        .card-dashboard:hover, .galeri-card:hover { transform: translateY(-5px) scale(1.01); box-shadow: 0 12px 25px rgba(0,0,0,0.15) !important; }
        .btn-pink:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(233,30,99,0.4); }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(-45deg, #f8bbd0, #ec407a, #bbdefb, #1976d2); background-size: 400% 400%; animation: gradientBG 10s ease infinite; min-height: 100vh; }
        @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .sidebar { background: linear-gradient(180deg, #f8bbd0 0%, #bbdefb 100%); min-height: 100vh; color: #3e2723; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 20px 15px; font-size: 1.3rem; font-weight: bold; border-bottom: 2px solid #ec407a; color: #6a1b2a; }
        .sidebar .brand i { color: #1976d2; margin-right: 8px; }
        .sidebar a { color: #3e2723; text-decoration: none; padding: 12px 15px; display: block; border-radius: 5px; transition: 0.3s; margin: 2px 10px; }
        .sidebar a:hover { background-color: #f48fb1; color: #1a237e; font-weight: bold; }
        .sidebar a.active { background-color: #ec407a; color: white; }
        .sidebar .logout-container { margin-top: auto; padding: 15px; }
        .sidebar .logout-btn { background-color: #c62828 !important; color: white !important; border: 2px solid #b71c1c; padding: 10px; border-radius: 8px; text-align: center; font-weight: bold; display: block; text-decoration: none; }
        .card-dashboard { background-color: #fff; border-radius: 15px; border: 2px solid #bbdefb; box-shadow: 0 4px 12px rgba(25,118,210,0.1); }
        .btn-pink { background-color: #ec407a; color: white; border: none; }
        .btn-pink:hover { background-color: #d81b60; }
        .welcome-center { text-align: center; margin: 40px 0; }
        .search-box { max-width: 400px; margin: 20px auto; background: #fce4ec; padding: 10px; border-radius: 15px; }
        .galeri-foto-wrapper { width: 100%; height: 140px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 10px 10px 0 0; overflow: hidden; }
        .galeri-foto-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .galeri-card { border: 2px solid #bbdefb; transition: 0.3s; height: 100%; }
        .galeri-card:hover { border-color: #ec407a; box-shadow: 0 4px 15px rgba(233,30,99,0.2); }
        .table-sm td { padding: 2px 5px; font-size: 0.9rem; }
        .stat-card { cursor: pointer; transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .peringkat-1 { background-color: #ffd700 !important; }
        .peringkat-2 { background-color: #c0c0c0 !important; }
        .peringkat-3 { background-color: #cd7f32 !important; }
        .bg-hijau { background-color: #d4edda !important; }
        .bg-merah { background-color: #f8d7da !important; }
        .bg-kuning { background-color: #fff3cd !important; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar p-0">
            <div class="brand">
                <i class="bi bi-phone"></i> Ratu Handphone
            </div>
            <nav class="mt-2">
                <a href="index.php?page=dashboard" class="<?= ($page=='dashboard')?'active':'' ?>">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a href="index.php?page=data" class="<?= ($page=='data')?'active':'' ?>">
                    <i class="bi bi-table"></i> Data Handphone
                </a>
                <a href="index.php?page=bobot" class="<?= ($page=='bobot')?'active':'' ?>">
                    <i class="bi bi-sliders2"></i> Atur Bobot
                </a>
                <a href="index.php?page=hitung" class="<?= ($page=='hitung')?'active':'' ?>">
                    <i class="bi bi-calculator"></i> Hitung TOPSIS
                </a>
                <a href="index.php?page=about" class="<?= ($page=='about')?'active':'' ?>">
                    <i class="bi bi-shop"></i> About Store
                </a>
            </nav>
            <div class="logout-container">
                <a href="logout.php" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- KONTEN -->
        <div class="col-md-10 p-4">
            <?php 
            if ($page == 'dashboard'): 
                $jml = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM handphone"))['total'];
            ?>
                <div class="welcome-center">
                    <h2 style="color:#d81b60;">Selamat Datang, <?= $_SESSION['username'] ?>! 👋</h2>
                    <p class="text-muted">Ratu Handphone - Sistem Pendukung Keputusan Pemilihan Handphone Terbaik dengan Metode TOPSIS</p>
                </div>

                <div class="search-box">
                    <form method="GET" action="" class="d-flex">
                        <input type="hidden" name="page" value="dashboard">
                        <input class="form-control me-2" type="search" name="cari" placeholder="Cari handphone..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>" style="border-color:#f48fb1;">
                        <button class="btn btn-pink" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-dashboard p-3 text-center stat-card" onclick="window.location.href='index.php?page=dashboard&show=handphone_list'">
                            <i class="bi bi-phone" style="font-size:3rem; color:#ec407a;"></i>
                            <h4 class="mt-2">Total Handphone</h4>
                            <p class="fs-3 fw-bold"><?= $jml ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-dashboard p-3 text-center stat-card" data-bs-toggle="modal" data-bs-target="#adminModal">
                            <i class="bi bi-people" style="font-size:3rem; color:#ec407a;"></i>
                            <h4 class="mt-2">Admin Aktif</h4>
                            <p class="fs-3 fw-bold">2</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-dashboard p-3 text-center stat-card">
                            <i class="bi bi-star" style="font-size:3rem; color:#ec407a;"></i>
                            <h4 class="mt-2">Kriteria</h4>
                            <p class="fs-3 fw-bold">5</p>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['show']) && $_GET['show'] == 'handphone_list'): 
                    $query_list = mysqli_query($conn, "SELECT * FROM handphone ORDER BY id ASC");
                ?>
                <div class="card card-dashboard mt-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0" style="color:#d81b60;"><i class="bi bi-phone-fill"></i> Daftar Semua Handphone</h4>
                        <a href="index.php?page=dashboard" class="btn btn-sm btn-secondary"><i class="bi bi-x-lg"></i> Tutup</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead style="background:#f8bbd0;">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:10%">Foto</th>
                                    <th style="width:25%">Nama Handphone</th>
                                    <th style="width:15%">Harga</th>
                                    <th style="width:10%">RAM</th>
                                    <th style="width:10%">Storage</th>
                                    <th style="width:15%">Baterai</th>
                                    <th style="width:10%">Kamera</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; while($hp = mysqli_fetch_assoc($query_list)): ?>
                                <tr>
                                    <td class="text-center"><strong><?= $no++ ?></strong></td>
                                    <td class="text-center"><img src="<?= $hp['gambar'] ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" onerror="this.src='assets/images/default.jpg'"></div>
                                    <td><strong><?= $hp['nama'] ?></strong></div>
                                    <td class="text-end">Rp <?= number_format($hp['harga'],0,',','.') ?></div>
                                    <td class="text-center"><?= $hp['ram'] ?> GB</div>
                                    <td class="text-center"><?= $hp['storage'] ?> GB</div>
                                    <td class="text-center"><?= $hp['baterai'] ?> mAh</div>
                                    <td class="text-center"><?= $hp['kamera'] ?> MP</div>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="index.php?page=data" class="btn btn-pink"><i class="bi bi-plus-circle"></i> Kelola Data Handphone</a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['cari']) && $_GET['cari'] != ''): 
                    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                    $result = mysqli_query($conn, "SELECT * FROM handphone WHERE nama LIKE '%$cari%'");
                    if (mysqli_num_rows($result) > 0):
                ?>
                <div class="card card-dashboard mt-4 p-3">
                    <h5>Hasil Pencarian: "<?= htmlspecialchars($_GET['cari']) ?>"</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mt-3 align-middle">
                            <thead style="background:#f8bbd0;">
                                <tr><th>No</th><th>Foto</th><th>Nama</th><th>Harga</th><th>RAM</th><th>Storage</th><th>Baterai</th><th>Kamera</th></tr>
                            </thead>
                            <tbody>
                                <?php $no=1; while($row=mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></div>
                                    <td class="text-center"><img src="<?= $row['gambar'] ?>" style="width:50px;height:50px;object-fit:cover;" onerror="this.src='assets/images/default.jpg'"></div>
                                    <td><strong><?= $row['nama'] ?></strong></div>
                                    <td class="text-end">Rp <?= number_format($row['harga'],0,',','.') ?></div>
                                    <td class="text-center"><?= $row['ram'] ?> GB</div>
                                    <td class="text-center"><?= $row['storage'] ?> GB</div>
                                    <td class="text-center"><?= $row['baterai'] ?> mAh</div>
                                    <td class="text-center"><?= $row['kamera'] ?> MP</div>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning mt-3">Tidak ditemukan handphone dengan nama "<?= htmlspecialchars($_GET['cari']) ?>"</div>
                <?php endif; endif; ?>

                <div class="card card-dashboard mt-4 p-3">
                    <h5>Galeri Handphone</h5>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="color:#6a1b2a;">Koleksi Handphone Tersedia</h6>
                        <div>
                            <label class="form-label me-2 fw-bold">Urutkan:</label>
                            <select id="sortSelect" class="form-select form-select-sm d-inline w-auto" onchange="sortGallery()">
                                <option value="default">Default</option>
                                <option value="price-asc">Harga Termurah</option>
                                <option value="price-desc">Harga Termahal</option>
                                <option value="ram-desc">RAM Terbesar</option>
                                <option value="name-asc">Nama A-Z</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3" id="galeriContainer">
                        <?php 
                        $galeri = mysqli_query($conn, "SELECT * FROM handphone ORDER BY nama ASC");
                        while ($hp = mysqli_fetch_assoc($galeri)):
                        ?>
                        <div class="col-md-4 mb-4 galeri-item">
                            <div class="card galeri-card">
                                <div class="galeri-foto-wrapper">
                                    <img src="<?= $hp['gambar'] ?>" onerror="this.src='assets/images/default.jpg'">
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title text-center fw-bold" style="color:#d81b60;"><?= $hp['nama'] ?></h6>
                                    <table class="table table-sm table-borderless mt-2">
                                        <tr>
                                            <td><i class="bi bi-cash-coin text-success"></i> Harga</div>
                                            <td class="text-end harga">Rp <?= number_format($hp['harga'],0,',','.') ?></div>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-memory text-primary"></i> RAM</div>
                                            <td class="text-end ram"><?= $hp['ram'] ?> GB</div>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-hdd-stack text-warning"></i> Storage</div>
                                            <td class="text-end"><?= $hp['storage'] ?> GB</div>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-battery-full text-info"></i> Baterai</div>
                                            <td class="text-end"><?= $hp['baterai'] ?> mAh</div>
                                        </tr>
                                        <tr>
                                            <td><i class="bi bi-camera text-danger"></i> Kamera</div>
                                            <td class="text-end"><?= $hp['kamera'] ?> MP</div>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            <?php elseif ($page == 'data'): 
                include 'data.php';
            elseif ($page == 'bobot'):
                include 'bobot_user.php';
            elseif ($page == 'hitung'): 
                include 'hitung.php';
            elseif ($page == 'about'): 
                include 'about_store.php';
            elseif ($page == 'hasil'): 
                $hasil_rank = isset($_SESSION['hasil_topsis']['hasil_rank']) ? $_SESSION['hasil_topsis']['hasil_rank'] : [];
                $penjelasan_rank = isset($_SESSION['hasil_topsis']['penjelasan_rank']) ? $_SESSION['hasil_topsis']['penjelasan_rank'] : [];
                
                $query_rata = mysqli_query($conn, "SELECT AVG(harga) as avg_harga, AVG(ram) as avg_ram, AVG(storage) as avg_storage, AVG(baterai) as avg_baterai, AVG(kamera) as avg_kamera FROM handphone");
                $rata = mysqli_fetch_assoc($query_rata);
                $rata_harga = $rata['avg_harga'];
                $rata_ram = $rata['avg_ram'];
                $rata_storage = $rata['avg_storage'];
                $rata_baterai = $rata['avg_baterai'];
                $rata_kamera = $rata['avg_kamera'];
                
                if (empty($hasil_rank)) {
                    echo "<div class='alert alert-warning'>Belum ada hasil perhitungan. Silakan hitung TOPSIS terlebih dahulu.</div>";
                    echo "<a href='index.php?page=hitung' class='btn btn-pink'>Kembali ke Hitung TOPSIS</a>";
                } else {
            ?>
                <div class="card card-dashboard p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h4 class="mb-0" style="color:#d81b60;"><i class="bi bi-trophy-fill"></i> Hasil Perhitungan TOPSIS</h4>
                        <a href="export_excel.php" class="btn btn-success"><i class="bi bi-download"></i> Export ke Excel</a>
                    </div>

                    <h5 class="mt-3 mb-3">🏆 Peringkat Handphone</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead style="background:linear-gradient(135deg, #f8bbd0, #bbdefb);">
                                <tr><th style="width:10%">Peringkat</th><th style="width:15%">Foto</th><th style="width:50%">Nama Handphone</th><th style="width:25%">Nilai Preferensi (V)</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil_rank as $hr): 
                                    $class = ($hr['no'] == 1) ? 'peringkat-1' : (($hr['no'] == 2) ? 'peringkat-2' : (($hr['no'] == 3) ? 'peringkat-3' : ''));
                                ?>
                                <tr class="<?= $class ?>">
                                    <td><strong><?= $hr['no'] ?></strong></td>
                                    <td><img src="<?= $hr['gambar'] ?>" style="width:60px;height:60px;object-fit:cover;border-radius:10px;" onerror="this.src='assets/images/default.jpg'"></div>
                                    <td><strong><?= $hr['nama'] ?></strong></div>
                                    <td><strong><?= number_format($hr['skor'], 4) ?></strong></div>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4 mb-3">📊 Detail Nilai Setiap Handphone</h5>
                    <?php if (!empty($penjelasan_rank)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead style="background:linear-gradient(135deg, #bbdefb, #e3f2fd);">
                                <tr><th>Peringkat</th><th>Nama Handphone</th><th>💰 Harga</th><th>⚡ RAM</th><th>💾 Storage</th><th>🔋 Baterai</th><th>📷 Kamera</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($penjelasan_rank as $p): ?>
                                <tr>
                                    <td><strong><?= $p['no'] ?></strong></div>
                                    <td><strong><?= $p['nama'] ?></strong></div>
                                    <td class="<?= is_array($p['harga']) ? $p['harga']['class'] : '' ?>"><?= is_array($p['harga']) ? $p['harga']['icon'] . ' ' . $p['harga']['nilai'] : $p['harga'] ?></div>
                                    <td class="<?= is_array($p['ram']) ? $p['ram']['class'] : '' ?>"><?= is_array($p['ram']) ? $p['ram']['icon'] . ' ' . $p['ram']['nilai'] : $p['ram'] ?></div>
                                    <td class="<?= is_array($p['storage']) ? $p['storage']['class'] : '' ?>"><?= is_array($p['storage']) ? $p['storage']['icon'] . ' ' . $p['storage']['nilai'] : $p['storage'] ?></div>
                                    <td class="<?= is_array($p['baterai']) ? $p['baterai']['class'] : '' ?>"><?= is_array($p['baterai']) ? $p['baterai']['icon'] . ' ' . $p['baterai']['nilai'] : $p['baterai'] ?></div>
                                    <td class="<?= is_array($p['kamera']) ? $p['kamera']['class'] : '' ?>"><?= is_array($p['kamera']) ? $p['kamera']['icon'] . ' ' . $p['kamera']['nilai'] : $p['kamera'] ?></div>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4 p-3 rounded" style="background: #f8f9fa;">
                        <h6><i class="bi bi-bar-chart-fill"></i> Rata-rata Seluruh Handphone:</h6>
                        <div class="row mt-2">
                            <div class="col-md-2">💰 Harga: Rp <?= number_format($rata_harga, 0, ',', '.') ?></div>
                            <div class="col-md-2">⚡ RAM: <?= round($rata_ram, 1) ?> GB</div>
                            <div class="col-md-2">💾 Storage: <?= round($rata_storage, 1) ?> GB</div>
                            <div class="col-md-2">🔋 Baterai: <?= round($rata_baterai, 0) ?> mAh</div>
                            <div class="col-md-2">📷 Kamera: <?= round($rata_kamera, 1) ?> MP</div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded" style="background: #f8f9fa;">
                        <h6><i class="bi bi-info-circle-fill"></i> Keterangan:</h6>
                        <div class="d-flex flex-wrap gap-4 mt-2">
                            <div><span class="badge bg-hijau p-2">✅ Hijau</span> = Lebih baik dari rata-rata</div>
                            <div><span class="badge bg-merah p-2">⚠️ Merah</span> = Kurang baik dari rata-rata</div>
                            <div><span class="badge bg-kuning p-2">📊 Kuning</span> = Sama dengan rata-rata</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="index.php?page=hitung" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Hitung Ulang</a>
                        <a href="export_excel.php" class="btn btn-success"><i class="bi bi-download"></i> Export ke Excel</a>
                    </div>
                </div>
            <?php 
                }
            else: 
                echo "<div class='alert alert-warning'>Halaman tidak ditemukan.</div>";
            endif; 
            ?>
        </div>
    </div>
</div>

<!-- Modal Profil Admin -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #f8bbd0, #bbdefb);">
                <h5 class="modal-title"><i class="bi bi-people-fill"></i> Profil Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center border-end">
                        <img src="assets/images/admin1_aura.jpg" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;" onerror="this.src='assets/images/default-user.jpg'">
                        <h5>Aura Najwa Chairunisa</h5>
                        <p>NIM: 0110124069<br>Jurusan: Sistem Informasi<br>Kampus: STT-TERPADU NURUL FIKRI</p>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="assets/images/admin2_rara.jpg" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;" onerror="this.src='assets/images/default-user.jpg'">
                        <h5>Yumna Dzakirah</h5>
                        <p>NIM: 0110124100<br>Jurusan: Sistem Informasi<br>Kampus: STT-TERPADU NURUL FIKRI</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function sortGallery() {
    var select = document.getElementById('sortSelect');
    var value = select.value;
    var container = document.getElementById('galeriContainer');
    var items = Array.from(container.getElementsByClassName('galeri-item'));
    if (value === 'default') { location.reload(); return; }
    items.sort(function(a, b) {
        var valA, valB;
        switch(value) {
            case 'price-asc': valA = parseInt(a.querySelector('.harga').textContent.replace(/[^0-9]/g, '')); valB = parseInt(b.querySelector('.harga').textContent.replace(/[^0-9]/g, '')); return valA - valB;
            case 'price-desc': valA = parseInt(a.querySelector('.harga').textContent.replace(/[^0-9]/g, '')); valB = parseInt(b.querySelector('.harga').textContent.replace(/[^0-9]/g, '')); return valB - valA;
            case 'ram-desc': valA = parseInt(a.querySelector('.ram').textContent); valB = parseInt(b.querySelector('.ram').textContent); return valB - valA;
            case 'name-asc': valA = a.querySelector('.card-title').textContent.toLowerCase(); valB = b.querySelector('.card-title').textContent.toLowerCase(); return valA < valB ? -1 : 1;
        }
    });
    items.forEach(function(item){ container.appendChild(item); });
}
</script>
<?php ob_end_flush(); ?>
</body>
</html>