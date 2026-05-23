<?php
require_once 'koneksi.php';

$id = (int) $_GET['id'];

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM karyawan WHERE id = ?");

if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $status = "sukses";
    } else {
        $status = "tidak_ditemukan";
    }
} else {
    $status = "gagal";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: index.php?status=" . $status);
exit;
?>