<?php
require 'config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Proses Tambah
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = $_POST['harga'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $baterai = $_POST['baterai'];
    $kamera = $_POST['kamera'];
    $gambar = 'assets/images/default.jpg'; // default

    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "assets/images/";
        $gambar = $target_dir . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $gambar);
    }

    $query = "INSERT INTO handphone (nama, harga, ram, storage, baterai, kamera, gambar) 
              VALUES ('$nama', '$harga', '$ram', '$storage', '$baterai', '$kamera', '$gambar')";
    mysqli_query($conn, $query);
    header('Location: index.php?page=data');
    exit;
}

// Proses Edit
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = $_POST['harga'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $baterai = $_POST['baterai'];
    $kamera = $_POST['kamera'];
    $gambar = $_POST['gambar_lama'];

    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "assets/images/";
        $gambar = $target_dir . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $gambar);
    }

    $query = "UPDATE handphone SET 
              nama='$nama', harga='$harga', ram='$ram', storage='$storage', 
              baterai='$baterai', kamera='$kamera', gambar='$gambar' 
              WHERE id=$id";
    mysqli_query($conn, $query);
    header('Location: index.php?page=data');
    exit;
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($conn, "SELECT gambar FROM handphone WHERE id=$id");
    $d = mysqli_fetch_assoc($q);
    if ($d && $d['gambar'] != 'assets/images/default.jpg' && file_exists($d['gambar'])) {
        unlink($d['gambar']);
    }
    mysqli_query($conn, "DELETE FROM handphone WHERE id=$id");
    header('Location: index.php?page=data');
    exit;
}

// Ambil data handphone
$data = mysqli_query($conn, "SELECT * FROM handphone ORDER BY id DESC");
?>
<div class="container-fluid">
    <h3><i class="bi bi-database"></i> Data Handphone</h3>
    <p class="text-muted">Kelola data handphone yang akan dihitung dengan metode TOPSIS</p>

    <!-- Tombol Tambah di sebelah kanan -->
    <div class="row mb-3">
        <div class="col-md-3 offset-md-9">
            <button class="btn btn-success w-100 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#tambahModal" style="border-radius:15px;">
                <i class="bi bi-plus-circle"></i> Tambah Produk Baru
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>RAM</th>
                    <th>Storage</th>
                    <th>Baterai</th>
                    <th>Kamera</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><img src="<?= $row['gambar'] ?>" alt="<?= $row['nama'] ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;" onerror="this.src='assets/images/default.jpg'"></td>
                    <td><?= $row['nama'] ?></td>
                    <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
                    <td><?= $row['ram'] ?> GB</div>
                    <td><?= $row['storage'] ?> GB</div>
                    <td><?= $row['baterai'] ?> mAh</div>
                    <td><?= $row['kamera'] ?> MP</div>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <a href="data.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus <?= $row['nama'] ?>?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </div>
                </tr>

                <!-- Modal Edit per baris -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background:linear-gradient(135deg, #f8bbd0, #bbdefb);">
                                <h5 class="modal-title">Edit Handphone</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
                                    <div class="mb-3">
                                        <label>Nama Handphone</label>
                                        <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Harga</label>
                                        <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>RAM (GB)</label>
                                            <input type="number" name="ram" class="form-control" value="<?= $row['ram'] ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Storage (GB)</label>
                                            <input type="number" name="storage" class="form-control" value="<?= $row['storage'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Baterai (mAh)</label>
                                            <input type="number" name="baterai" class="form-control" value="<?= $row['baterai'] ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Kamera (MP)</label>
                                            <input type="number" name="kamera" class="form-control" value="<?= $row['kamera'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label>Gambar (opsional)</label>
                                        <input type="file" name="gambar" class="form-control">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #28a745, #20c997); color:white;">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Handphone Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Handphone</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>RAM (GB)</label>
                            <input type="number" name="ram" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Storage (GB)</label>
                            <input type="number" name="storage" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Baterai (mAh)</label>
                            <input type="number" name="baterai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kamera (MP)</label>
                            <input type="number" name="kamera" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Gambar (opsional)</label>
                        <input type="file" name="gambar" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>