<?php
session_start();
require 'koneksi.php'; // Pastikan kamu sudah membuat file koneksi.php

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Menangkap nilai parameter redirect jika ada
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dataalbum'; // Default ke dataalbum jika tidak ada parameter

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAlbum = $_POST['nama_album'];  
    $deskripsi = $_POST['deskripsi'];
    $tanggalDibuat = date('Y-m-d'); // Menggunakan tanggal hari ini
    $userID = $_SESSION['UserID']; // Ambil UserID dari session

    // Query untuk menambahkan album
    $sql = "INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) VALUES (?, ?, ?, ?)";

    // Persiapkan dan eksekusi query
    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param('sssi', $namaAlbum, $deskripsi, $tanggalDibuat, $userID);

        if ($stmt->execute()) {
            // Redirect ke halaman yang sesuai berdasarkan nilai redirect
            header("Location: $redirect.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $koneksi->error;
    }
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Album</title>
</head>
<style>
.hidden {
    display: none;
}
</style>

<body>
    <h2>Tambah Album</h2>
    <form method="post" action="">
        <label for="nama_album">Nama Album:</label>
        <input type="text" name="nama_album" id="nama_album" required><br>

        <label for="deskripsi">Deskripsi:</label>
        <textarea name="deskripsi" id="deskripsi" required></textarea><br>

        <input class="hidden" type="number" id="UserID" name="UserID" value="<?= $_SESSION['UserID'] ?>" required>
        <button type="submit">Simpan</button>
    </form>
</body>

</html>