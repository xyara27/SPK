<div class="card card-dashboard p-5 text-center" style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 25px;">
    <div class="text-white">
        <i class="bi bi-calculator-fill" style="font-size: 4rem;"></i>
        <h2 class="mt-3 fw-bold">Hitung TOPSIS</h2>
        <p class="lead mt-2">Atur bobot kriteria sesuai preferensi Anda untuk mendapatkan rekomendasi handphone terbaik</p>
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <div class="d-grid gap-3">
                    <a href="bobot_user.php" class="btn btn-light btn-lg rounded-pill py-3">
                        <i class="bi bi-sliders2"></i> Atur Bobot Kriteria
                    </a>
                    <?php
                    $username = $_SESSION['username'];
                    $cek_bobot = mysqli_query($conn, "SELECT * FROM user_bobot WHERE user_id='$username'");
                    if (mysqli_num_rows($cek_bobot) > 0) {
                        echo '<a href="proses.php" class="btn btn-success btn-lg rounded-pill py-3">
                                <i class="bi bi-play-fill"></i> Hitung Sekarang
                              </a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Metode TOPSIS -->
<div class="card card-dashboard mt-4 p-4">
    <h5 class="text-center mb-3" style="color:#d81b60;">
        <i class="bi bi-info-circle-fill"></i> Tentang Metode TOPSIS
    </h5>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-arrow-up-circle" style="font-size: 2rem; color:#28a745;"></i>
                <h6 class="mt-2">Solusi Ideal Positif</h6>
                <p class="small text-muted">Alternatif dengan nilai benefit tertinggi dan cost terendah</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-arrow-down-circle" style="font-size: 2rem; color:#dc3545;"></i>
                <h6 class="mt-2">Solusi Ideal Negatif</h6>
                <p class="small text-muted">Alternatif dengan nilai benefit terendah dan cost tertinggi</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <i class="bi bi-trophy" style="font-size: 2rem; color:#ffc107;"></i>
                <h6 class="mt-2">Nilai Preferensi</h6>
                <p class="small text-muted">Semakin mendekati 1, semakin baik alternatif tersebut</p>
            </div>
        </div>
    </div>
</div>

<!-- Rekomendasi -->
<?php
$username = $_SESSION['username'];
$cek_bobot = mysqli_query($conn, "SELECT * FROM user_bobot WHERE user_id='$username'");
if (mysqli_num_rows($cek_bobot) > 0) {
    // Ambil hasil terakhir dari session jika ada
    if (isset($_SESSION['hasil_rank']) && !empty($_SESSION['hasil_rank'])) {
        $top1 = $_SESSION['hasil_rank'][0];
        echo '
        <div class="card card-dashboard mt-4 p-4 text-center" style="background: linear-gradient(135deg, #ffd70020, #ffb34720);">
            <h5><i class="bi bi-trophy-fill text-warning"></i> Rekomendasi Terakhir</h5>
            <h4 class="mt-2" style="color:#d81b60;">🏆 ' . htmlspecialchars($top1['nama']) . '</h4>
            <p>dengan nilai preferensi <strong>' . $top1['skor'] . '</strong></p>
            <a href="proses.php" class="btn btn-pink">Hitung Ulang</a>
        </div>';
    }
}
?>