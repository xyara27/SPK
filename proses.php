<?php
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Ambil bobot dari database user
$query_bobot = mysqli_query($conn, "SELECT * FROM user_bobot WHERE user_id='$username'");
$bobot_user = mysqli_fetch_assoc($query_bobot);

if (!$bobot_user) {
    echo "<script>alert('⚠️ Silakan atur bobot kriteria terlebih dahulu!'); window.location='bobot_user.php';</script>";
    exit;
}

// Ambil bobot dari database
$w = [
    'harga'   => floatval($bobot_user['bobot_harga']),
    'ram'     => floatval($bobot_user['bobot_ram']),
    'storage' => floatval($bobot_user['bobot_storage']),
    'baterai' => floatval($bobot_user['bobot_baterai']),
    'kamera'  => floatval($bobot_user['bobot_kamera'])
];

// Ambil data handphone dari database
$query = mysqli_query($conn, "SELECT * FROM handphone");
$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}
$m = count($data);

if ($m == 0) {
    echo "<script>alert('⚠️ Data handphone kosong! Silakan tambah data terlebih dahulu.'); window.location='index.php?page=data';</script>";
    exit;
}

$n = 5;

// --- Matriks X ---
$X = [];
foreach ($data as $i => $row) {
    $X[$i][0] = $row['harga'];
    $X[$i][1] = $row['ram'];
    $X[$i][2] = $row['storage'];
    $X[$i][3] = $row['baterai'];
    $X[$i][4] = $row['kamera'];
}

// --- Normalisasi ---
$normalisasi = [];
for ($j = 0; $j < $n; $j++) {
    $sum_sq = 0;
    for ($i = 0; $i < $m; $i++) {
        $sum_sq += pow($X[$i][$j], 2);
    }
    $akar = sqrt($sum_sq);
    for ($i = 0; $i < $m; $i++) {
        $normalisasi[$i][$j] = ($akar > 0) ? $X[$i][$j] / $akar : 0;
    }
}

// --- Matriks terbobot Y ---
$Y = [];
$bobot_list = [$w['harga'], $w['ram'], $w['storage'], $w['baterai'], $w['kamera']];
for ($i = 0; $i < $m; $i++) {
    for ($j = 0; $j < $n; $j++) {
        $Y[$i][$j] = $bobot_list[$j] * $normalisasi[$i][$j];
    }
}

// --- Solusi ideal ---
$A_plus = [];
$A_minus = [];
for ($j = 0; $j < $n; $j++) {
    $col = array_column($Y, $j);
    if ($j == 0) {
        $A_plus[$j] = min($col);
        $A_minus[$j] = max($col);
    } else {
        $A_plus[$j] = max($col);
        $A_minus[$j] = min($col);
    }
}

// --- Jarak ---
$D_plus = [];
$D_minus = [];
for ($i = 0; $i < $m; $i++) {
    $sum_plus = 0;
    $sum_minus = 0;
    for ($j = 0; $j < $n; $j++) {
        $sum_plus += pow($Y[$i][$j] - $A_plus[$j], 2);
        $sum_minus += pow($Y[$i][$j] - $A_minus[$j], 2);
    }
    $D_plus[$i] = sqrt($sum_plus);
    $D_minus[$i] = sqrt($sum_minus);
}

// --- Preferensi V ---
$V = [];
for ($i = 0; $i < $m; $i++) {
    $denom = $D_plus[$i] + $D_minus[$i];
    $V[$i] = $denom > 0 ? $D_minus[$i] / $denom : 0;
}

// --- Ranking ---
$ranking = $V;
arsort($ranking);
$rank = 1;
$hasil_rank = [];
foreach ($ranking as $i => $v) {
    $hasil_rank[] = [
        'no'    => $rank++,
        'nama'  => $data[$i]['nama'],
        'gambar'=> $data[$i]['gambar'],
        'skor'  => round($v, 4),
        'idx'   => $i
    ];
}

// --- Rata-rata untuk perbandingan ---
$rata_harga   = array_sum(array_column($data, 'harga')) / $m;
$rata_ram     = array_sum(array_column($data, 'ram')) / $m;
$rata_storage = array_sum(array_column($data, 'storage')) / $m;
$rata_baterai = array_sum(array_column($data, 'baterai')) / $m;
$rata_kamera  = array_sum(array_column($data, 'kamera')) / $m;

// Fungsi untuk menentukan class warna dan icon
function getWarnaDanIcon($nilai, $rata, $tipe = 'benefit') {
    if ($tipe == 'cost') {
        if ($nilai < $rata) return ['class' => 'bg-hijau', 'icon' => '✅'];
        elseif ($nilai > $rata) return ['class' => 'bg-merah', 'icon' => '⚠️'];
        else return ['class' => 'bg-kuning', 'icon' => '📊'];
    } else {
        if ($nilai > $rata) return ['class' => 'bg-hijau', 'icon' => '✅'];
        elseif ($nilai < $rata) return ['class' => 'bg-merah', 'icon' => '⚠️'];
        else return ['class' => 'bg-kuning', 'icon' => '📊'];
    }
}

// Fungsi untuk format nilai
function formatNilai($kriteria, $nilai) {
    if ($kriteria == 'harga') {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    } elseif ($kriteria == 'ram' || $kriteria == 'storage') {
        return $nilai . ' GB';
    } elseif ($kriteria == 'baterai') {
        return $nilai . ' mAh';
    } elseif ($kriteria == 'kamera') {
        return $nilai . ' MP';
    }
    return $nilai;
}

$penjelasan_rank = [];
foreach ($hasil_rank as $hr) {
    $i = $hr['idx'];
    
    $harga_info = getWarnaDanIcon($data[$i]['harga'], $rata_harga, 'cost');
    $ram_info = getWarnaDanIcon($data[$i]['ram'], $rata_ram, 'benefit');
    $storage_info = getWarnaDanIcon($data[$i]['storage'], $rata_storage, 'benefit');
    $baterai_info = getWarnaDanIcon($data[$i]['baterai'], $rata_baterai, 'benefit');
    $kamera_info = getWarnaDanIcon($data[$i]['kamera'], $rata_kamera, 'benefit');
    
    $penjelasan_rank[] = [
        'no'      => $hr['no'],
        'nama'    => $hr['nama'],
        'harga'   => [
            'nilai' => formatNilai('harga', $data[$i]['harga']),
            'icon' => $harga_info['icon'],
            'class' => $harga_info['class']
        ],
        'ram'   => [
            'nilai' => formatNilai('ram', $data[$i]['ram']),
            'icon' => $ram_info['icon'],
            'class' => $ram_info['class']
        ],
        'storage'   => [
            'nilai' => formatNilai('storage', $data[$i]['storage']),
            'icon' => $storage_info['icon'],
            'class' => $storage_info['class']
        ],
        'baterai'   => [
            'nilai' => formatNilai('baterai', $data[$i]['baterai']),
            'icon' => $baterai_info['icon'],
            'class' => $baterai_info['class']
        ],
        'kamera'   => [
            'nilai' => formatNilai('kamera', $data[$i]['kamera']),
            'icon' => $kamera_info['icon'],
            'class' => $kamera_info['class']
        ]
    ];
}

// --- Simpan ke session untuk export ---
$_SESSION['bobot_harga']   = $w['harga'];
$_SESSION['bobot_ram']     = $w['ram'];
$_SESSION['bobot_storage'] = $w['storage'];
$_SESSION['bobot_baterai'] = $w['baterai'];
$_SESSION['bobot_kamera']  = $w['kamera'];
$_SESSION['hasil_rank']    = $hasil_rank;
$_SESSION['penjelasan_rank'] = $penjelasan_rank;
$_SESSION['hasil_topsis'] = [
    'hasil_rank' => $hasil_rank,
    'penjelasan_rank' => $penjelasan_rank
];

// Redirect ke halaman hasil di index.php
header('Location: index.php?page=hasil');
exit;
?>