<?php
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil data dari session (support kedua format)
$hasil_rank = isset($_SESSION['hasil_topsis']['hasil_rank']) ? $_SESSION['hasil_topsis']['hasil_rank'] : [];
if (empty($hasil_rank)) {
    $hasil_rank = isset($_SESSION['hasil_rank']) ? $_SESSION['hasil_rank'] : [];
}

$penjelasan_rank = isset($_SESSION['hasil_topsis']['penjelasan_rank']) ? $_SESSION['hasil_topsis']['penjelasan_rank'] : [];
if (empty($penjelasan_rank)) {
    $penjelasan_rank = isset($_SESSION['penjelasan_rank']) ? $_SESSION['penjelasan_rank'] : [];
}

// Cek format data lama (string) vs baru (array)
// Jika masih format string, konversi ke format array untuk kompatibilitas
if (!empty($penjelasan_rank) && isset($penjelasan_rank[0]['harga']) && !is_array($penjelasan_rank[0]['harga'])) {
    // Format lama: ['harga' => 'string', 'ram' => 'string', ...]
    // Ubah ke format baru: ['harga' => ['text' => 'string', 'class' => '...'], ...]
    $new_penjelasan = [];
    foreach ($penjelasan_rank as $p) {
        // Tentukan class berdasarkan teks
        $harga_class = 'bg-kuning';
        $ram_class = 'bg-kuning';
        $storage_class = 'bg-kuning';
        $baterai_class = 'bg-kuning';
        $kamera_class = 'bg-kuning';
        
        if (strpos($p['harga'], 'Lebih murah') !== false) $harga_class = 'bg-hijau';
        elseif (strpos($p['harga'], 'Lebih mahal') !== false) $harga_class = 'bg-merah';
        
        if (strpos($p['ram'], 'Lebih besar') !== false) $ram_class = 'bg-hijau';
        elseif (strpos($p['ram'], 'Lebih kecil') !== false) $ram_class = 'bg-merah';
        
        if (strpos($p['storage'], 'Lebih besar') !== false) $storage_class = 'bg-hijau';
        elseif (strpos($p['storage'], 'Lebih kecil') !== false) $storage_class = 'bg-merah';
        
        if (strpos($p['baterai'], 'Lebih besar') !== false) $baterai_class = 'bg-hijau';
        elseif (strpos($p['baterai'], 'Lebih kecil') !== false) $baterai_class = 'bg-merah';
        
        if (strpos($p['kamera'], 'Lebih besar') !== false) $kamera_class = 'bg-hijau';
        elseif (strpos($p['kamera'], 'Lebih kecil') !== false) $kamera_class = 'bg-merah';
        
        $new_penjelasan[] = [
            'no' => $p['no'],
            'nama' => $p['nama'],
            'harga' => ['text' => $p['harga'], 'class' => $harga_class],
            'ram' => ['text' => $p['ram'], 'class' => $ram_class],
            'storage' => ['text' => $p['storage'], 'class' => $storage_class],
            'baterai' => ['text' => $p['baterai'], 'class' => $baterai_class],
            'kamera' => ['text' => $p['kamera'], 'class' => $kamera_class]
        ];
    }
    $penjelasan_rank = $new_penjelasan;
}

if (empty($hasil_rank)) {
    echo "<script>alert('Tidak ada data. Lakukan perhitungan TOPSIS terlebih dahulu.'); window.location='index.php?page=hitung';</script>";
    exit;
}

// Header untuk download file Excel (.xls)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Hasil_TOPSIS_RatuHandphone_" . date('Y-m-d_H-i-s') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; }';
echo 'h2 { color: #d81b60; }';
echo 'h3 { color: #6a1b2a; margin-top: 20px; }';
echo 'th { background-color: #f8bbd0; color: #6a1b2a; padding: 10px; text-align: center; }';
echo 'td { border: 1px solid #ddd; padding: 8px; text-align: center; vertical-align: middle; }';
echo '.peringkat1 { background-color: #ffd700; }';
echo '.peringkat2 { background-color: #c0c0c0; }';
echo '.peringkat3 { background-color: #cd7f32; }';
echo '.bg-hijau { background-color: #d4edda; }';
echo '.bg-merah { background-color: #f8d7da; }';
echo '.bg-kuning { background-color: #fff3cd; }';
echo '.header-title { background: linear-gradient(135deg, #f8bbd0, #bbdefb); padding: 15px; text-align: center; }';
echo '.footer { margin-top: 20px; text-align: center; color: gray; font-size: 10px; }';
echo '.keterangan { background-color: #f0f4ff; padding: 10px; margin-top: 20px; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Header
echo '<div class="header-title">';
echo '<h2>🏆 RATU HANDPHONE - HASIL PERHITUNGAN TOPSIS</h2>';
echo '<p><strong>Tanggal:</strong> ' . date('d-m-Y H:i:s') . '</p>';
echo '<p><strong>User:</strong> ' . $_SESSION['username'] . '</p>';
echo '</div><br>';

// Tabel Ranking
echo '<h3>📊 PERINGKAT HANDPHONE</h3>';
echo '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
echo '<tr><th width="10%">Peringkat</th><th width="15%">Foto</th><th width="50%">Nama Handphone</th><th width="25%">Nilai Preferensi (V)</th></tr>';
foreach ($hasil_rank as $hr) {
    $class = '';
    if ($hr['no'] == 1) $class = 'class="peringkat1"';
    elseif ($hr['no'] == 2) $class = 'class="peringkat2"';
    elseif ($hr['no'] == 3) $class = 'class="peringkat3"';
    
    echo "<tr $class>
            <td align='center'><strong>{$hr['no']}</strong></td>
            <td align='center'><img src='{$hr['gambar']}' width='50' height='50'></div>
            <td><strong>{$hr['nama']}</strong></div>
            <td align='center'><strong>" . number_format($hr['skor'], 4) . "</strong></div>
           </div>";
}
echo '</table><br>';

// Tabel Penjelasan
echo '<h3>📝 PENJELASAN SETIAP PERINGKAT</h3>';
echo '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
echo '<tr style="background:#bbdefb;">
        <th width="8%">Peringkat</th>
        <th width="20%">Nama Handphone</th>
        <th width="18%">💰 Harga (Cost)</th>
        <th width="18%">⚡ RAM (Benefit)</th>
        <th width="18%">💾 Storage (Benefit)</th>
        <th width="18%">🔋 Baterai (Benefit)</th>
        <th width="18%">📷 Kamera (Benefit)</th>
       </div>';
foreach ($penjelasan_rank as $p) {
    // Cek apakah data dalam format array (baru) atau string (lama)
    if (is_array($p['harga'])) {
        $harga_class = $p['harga']['class'];
        $harga_text = $p['harga']['text'];
        $ram_class = $p['ram']['class'];
        $ram_text = $p['ram']['text'];
        $storage_class = $p['storage']['class'];
        $storage_text = $p['storage']['text'];
        $baterai_class = $p['baterai']['class'];
        $baterai_text = $p['baterai']['text'];
        $kamera_class = $p['kamera']['class'];
        $kamera_text = $p['kamera']['text'];
    } else {
        // Format lama
        $harga_class = 'bg-kuning';
        $ram_class = 'bg-kuning';
        $storage_class = 'bg-kuning';
        $baterai_class = 'bg-kuning';
        $kamera_class = 'bg-kuning';
        
        if (strpos($p['harga'], 'Lebih murah') !== false) $harga_class = 'bg-hijau';
        elseif (strpos($p['harga'], 'Lebih mahal') !== false) $harga_class = 'bg-merah';
        
        if (strpos($p['ram'], 'Lebih besar') !== false) $ram_class = 'bg-hijau';
        elseif (strpos($p['ram'], 'Lebih kecil') !== false) $ram_class = 'bg-merah';
        
        if (strpos($p['storage'], 'Lebih besar') !== false) $storage_class = 'bg-hijau';
        elseif (strpos($p['storage'], 'Lebih kecil') !== false) $storage_class = 'bg-merah';
        
        if (strpos($p['baterai'], 'Lebih besar') !== false) $baterai_class = 'bg-hijau';
        elseif (strpos($p['baterai'], 'Lebih kecil') !== false) $baterai_class = 'bg-merah';
        
        if (strpos($p['kamera'], 'Lebih besar') !== false) $kamera_class = 'bg-hijau';
        elseif (strpos($p['kamera'], 'Lebih kecil') !== false) $kamera_class = 'bg-merah';
        
        $harga_text = $p['harga'];
        $ram_text = $p['ram'];
        $storage_text = $p['storage'];
        $baterai_text = $p['baterai'];
        $kamera_text = $p['kamera'];
    }
    
    echo "<tr>
            <td align='center'><strong>{$p['no']}</strong></td>
            <td><strong>{$p['nama']}</strong></td>
            <td class='{$harga_class}'>{$harga_text}</td>
            <td class='{$ram_class}'>{$ram_text}</td>
            <td class='{$storage_class}'>{$storage_text}</td>
            <td class='{$baterai_class}'>{$baterai_text}</td>
            <td class='{$kamera_class}'>{$kamera_text}</td>
           </div>";
}
echo '</table><br>';

// Keterangan Warna
echo '<div class="keterangan">';
echo '<strong>📋 KETERANGAN WARNA:</strong><br>';
echo '✅ <span style="background-color:#d4edda; padding:2px 8px;">Hijau</span> = Lebih baik dari rata-rata (lebih murah untuk cost, lebih besar untuk benefit)<br>';
echo '⚠️ <span style="background-color:#f8d7da; padding:2px 8px;">Merah</span> = Kurang baik dari rata-rata (lebih mahal untuk cost, lebih kecil untuk benefit)<br>';
echo '📊 <span style="background-color:#fff3cd; padding:2px 8px;">Kuning</span> = Sama dengan rata-rata';
echo '</div>';

// Footer
echo '<div class="footer"><hr>';
echo '<p>© ' . date('Y') . ' Ratu Handphone - Sistem Pendukung Keputusan Metode TOPSIS</p>';
echo '<p>Dicetak pada: ' . date('d-m-Y H:i:s') . '</p>';
echo '</div>';

echo '</body></html>';
exit;
?>