<?php
require_once 'koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Ambil data
    $nik        = trim($_POST['nik']);
    $nama       = trim($_POST['nama']);
    $jabatan    = trim($_POST['jabatan']);
    $departemen = trim($_POST['departemen']);
    $no_hp      = trim($_POST['no_hp']);
    $gaji       = $_POST['gaji'];
    $tgl_masuk  = $_POST['tgl_masuk'];

    // 2. Validasi
if (empty($nik) || empty($nama) || empty($jabatan) ||
    empty($departemen) || empty($no_hp) || empty($gaji) || empty($tgl_masuk)) {
    $error = "Semua field wajib diisi!";

} elseif (!preg_match('/^[A-Z0-9]+$/', $nik)) {
    $error = "NIK hanya boleh huruf kapital dan angka. contoh: KRY006";

} elseif (!preg_match('/^(08|\+62)\d{7,13}$/', $no_hp)) {
    $error = "Format No. HP tidak valid. contoh: 081234567890";

} elseif ($gaji < 0) {
    $error = "Gaji tidak boleh negatif.";

} else {
    // Cek duplikasi NIK
    $cek  = mysqli_prepare($conn, "SELECT id FROM karyawan WHERE nik = ?");
    mysqli_stmt_bind_param($cek, "s", $nik);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        $error = "NIK <strong>$nik</strong> sudah terdaftar. Gunakan NIK lain.";
        mysqli_stmt_close($cek);
    } else {
        mysqli_stmt_close($cek);

        $stmt = mysqli_prepare($conn,
            "INSERT INTO karyawan (nik, nama, jabatan, departemen, no_hp, gaji, tgl_masuk)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sssssds",
            $nik, $nama, $jabatan, $departemen, $no_hp, $gaji, $tgl_masuk
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: index.php?status=tambah_sukses");
            exit;
        } else {
            $error = "Gagal menyimpan data: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karyawan</title>
    <style>
        body       { font-family: Arial, sans-serif; margin: 30px; }
        h2         { color: #333; }
        .form-group{ margin-bottom: 15px; }
        label      { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; max-width: 400px; padding: 8px 10px;
                        border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn-simpan{ background: #4f46e5; color: white; padding: 9px 20px;
                     border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn-batal { background: #6b7280; color: white; padding: 9px 20px;
                     border-radius: 6px; text-decoration: none; font-size: 14px; }
        .error     { background: #fee2e2; color: #991b1b; padding: 10px 14px;
                     border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>➕ Tambah Karyawan</h2>

<?php if ($error): ?>
    <div class="error">⚠️ <?= $error ?></div>
<?php endif; ?>

<form method="POST" action="tambah.php">

    <div class="form-group">
        <label>NIK</label>
        <input type="text" name="nik" placeholder="contoh: KRY...">
    </div>

    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" placeholder="contoh: Andi...">
    </div>

    <div class="form-group">
        <label>Jabatan</label>
        <input type="text" name="jabatan" placeholder="contoh: Staff...">
    </div>

    <div class="form-group">
        <label>Departemen</label>
        <select name="departemen">
            <option value="">-- Pilih Departemen --</option>
            <option value="HRD">HRD</option>
            <option value="IT">IT</option>
            <option value="Keuangan">Keuangan</option>
            <option value="Marketing">Marketing</option>
            <option value="Operasional">Operasional</option>
        </select>
    </div>

    <div class="form-group">
        <label>No. HP</label>
        <input type="text" name="no_hp" placeholder="contoh: 081234567896">
    </div>

    <div class="form-group">
        <label>Gaji</label>
        <input type="number" name="gaji" placeholder="contoh: 6000000">
    </div>

    <div class="form-group">
        <label>Tanggal Masuk</label>
        <input type="date" name="tgl_masuk">
    </div>

    <br>
    <button type="submit" class="btn-simpan">💾 Simpan</button>
    <a href="index.php" class="btn-batal">✕ Batal</a>

</form>

<?php mysqli_close($conn); ?>
</body>
</html>