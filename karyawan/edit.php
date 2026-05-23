<?php
require_once 'koneksi.php';

$error = "";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} else {
    $id = 0;
}

if ($id <= 0) {
    header("Location: index.php?status=edit_sukses");
    exit;
}

$sql    = "SELECT * FROM karyawan WHERE id = ?";
$stmt   = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id); 
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row    = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
   header("Location: index.php?status=edit_sukses");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nik        = trim($_POST['nik']);
    $nama       = trim($_POST['nama']);
    $jabatan    = trim($_POST['jabatan']);
    $departemen = trim($_POST['departemen']);
    $no_hp      = trim($_POST['no_hp']);
    $gaji       = $_POST['gaji'];
    $tgl_masuk  = $_POST['tgl_masuk'];

    if (empty($nik) || empty($nama) || empty($jabatan) ||
        empty($departemen) || empty($no_hp) || empty($gaji) || empty($tgl_masuk)) {
        $error = "Semua field wajib diisi!";
    } else {

        $stmt = mysqli_prepare($conn,
            "UPDATE karyawan
             SET nik=?, nama=?, jabatan=?, departemen=?, no_hp=?, gaji=?, tgl_masuk=?
             WHERE id=?"
        );

        mysqli_stmt_bind_param($stmt, "sssssdsi",
            $nik, $nama, $jabatan, $departemen, $no_hp, $gaji, $tgl_masuk, $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: index.php?status=edit_sukses");
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Karyawan</title>
    <style>
        body        { font-family: Arial, sans-serif; margin: 30px; }
        h2          { color: #333; }
        .form-group { margin-bottom: 15px; }
        label       { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; max-width: 400px; padding: 8px 10px;
                        border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn-simpan { background: #f59e0b; color: white; padding: 9px 20px;
                      border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn-batal  { background: #6b7280; color: white; padding: 9px 20px;
                      border-radius: 6px; text-decoration: none; font-size: 14px; }
        .error      { background: #fee2e2; color: #991b1b; padding: 10px 14px;
                      border-radius: 6px; margin-bottom: 20px; }
        .info       { background: #fef3c7; color: #92400e; padding: 10px 14px;
                      border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>

<h2>✏️ Edit Karyawan</h2>

<div class="info">
    ✏️ Mengedit data: <strong><?= htmlspecialchars($row['nama']) ?></strong>
    &nbsp;|&nbsp; NIK: <?= htmlspecialchars($row['nik']) ?>
</div>

<?php if ($error): ?>
    <div class="error">⚠️ <?= $error ?></div>
<?php endif; ?>

<form method="POST" action="edit.php?id=<?= $id ?>">

    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-group">
        <label>NIK</label>
        <input type="text" name="nik"
               value="<?= htmlspecialchars($row['nik']) ?>">
    </div>

    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama"
               value="<?= htmlspecialchars($row['nama']) ?>">
    </div>

    <div class="form-group">
        <label>Jabatan</label>
        <input type="text" name="jabatan"
               value="<?= htmlspecialchars($row['jabatan']) ?>">
    </div>

    <div class="form-group">
        <label>Departemen</label>
        <select name="departemen">
            <option value="">-- Pilih Departemen --</option>
            <?php
            $depts = ['HRD', 'IT', 'Keuangan', 'Marketing', 'Operasional'];
            foreach ($depts as $dept):
                $selected = ($row['departemen'] === $dept) ? 'selected' : '';
            ?>
            <option value="<?= $dept ?>" <?= $selected ?>><?= $dept ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>No. HP</label>
        <input type="text" name="no_hp"
               value="<?= htmlspecialchars($row['no_hp']) ?>">
    </div>

    <div class="form-group">
        <label>Gaji</label>
        <input type="number" name="gaji"
               value="<?= $row['gaji'] ?>">
    </div>

    <div class="form-group">
        <label>Tanggal Masuk</label>
        <input type="date" name="tgl_masuk"
               value="<?= $row['tgl_masuk'] ?>">
    </div>

    <br>
    <button type="submit" class="btn-simpan">💾 Simpan Perubahan</button>
    <a href="index.php" class="btn-batal">✕ Batal</a>

</form>

</body>
</html>