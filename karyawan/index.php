<?php
require_once 'koneksi.php';

$sql    = "SELECT * FROM karyawan ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Karyawan</title>
    <style>
        body       { font-family: Arial, sans-serif; margin: 30px; }
        h2         { color: #333; }
        table      { border-collapse: collapse; width: 100%; }
        th, td     { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th         { background: #4f46e5; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn       { padding: 5px 12px; border-radius: 5px; text-decoration: none;
                     font-size: 13px; }
        .btn-edit  { background: #f59e0b; color: white; }
        .btn-hapus { background: #ef4444; color: white; }
        .btn-tambah{ background: #4f46e5; color: white; padding: 8px 16px;
                     border-radius: 6px; text-decoration: none; }
    </style>
</head>
<body>
<?php
if (isset($_GET['status'])) {
    $pesan = [
        'sukses'          => ['bg' => '#dcfce7', 'warna' => '#166534', 'teks' => '✅ Data berhasil dihapus.'],
        'tambah_sukses'   => ['bg' => '#dcfce7', 'warna' => '#166534', 'teks' => '✅ Data karyawan berhasil ditambahkan.'],
        'edit_sukses'     => ['bg' => '#dbeafe', 'warna' => '#1e40af', 'teks' => '✅ Data karyawan berhasil diperbarui.'],
        'tidak_ditemukan' => ['bg' => '#fef3c7', 'warna' => '#92400e', 'teks' => '⚠️ Data tidak ditemukan.'],
        'gagal'           => ['bg' => '#fee2e2', 'warna' => '#991b1b', 'teks' => '❌ Gagal menghapus data.'],
    ];

    $s = $_GET['status'];
    if (isset($pesan[$s])) {
        echo '<div style="background:' . $pesan[$s]['bg'] . ';color:' . $pesan[$s]['warna'] . ';
                          padding:10px 14px;border-radius:6px;margin-bottom:16px;">
              ' . $pesan[$s]['teks'] . '</div>';
    }
}
?>
<h2>📋 Data Karyawan</h2>
<a href="tambah.php" class="btn-tambah">+ Tambah Karyawan</a>

<br><br>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Departemen</th>
            <th>No. HP</th>
            <th>Gaji</th>
            <th>Tgl Masuk</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)):
    ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nik']) ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['jabatan']) ?></td>
            <td><?= htmlspecialchars($row['departemen']) ?></td>
            <td><?= htmlspecialchars($row['no_hp']) ?></td>
            <td>Rp <?= number_format($row['gaji'], 0, ',', '.') ?></td>
            <td><?= $row['tgl_masuk'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>"
                   class="btn btn-hapus"
                   onclick="return confirm('Yakin hapus <?= addslashes($row['nama']) ?>?')">
                   🗑️ Hapus
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php
mysqli_free_result($result);
mysqli_close($conn);
?>
</body>
</html>