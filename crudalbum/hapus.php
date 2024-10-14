<?php
include '../koneksi.php';

$id = $_GET['id'];
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dataalbum'; // Jika redirect tidak ada, arahkan ke dataalbum

// Sanitasi input ID untuk menghindari SQL Injection
$id = mysqli_real_escape_string($koneksi, $id);

// Hapus album berdasarkan ID
$sql = "DELETE FROM album WHERE AlbumID = $id";

if ($koneksi->query($sql) === TRUE) {
    // Menggunakan session untuk menyampaikan pesan sukses
    session_start();
    $_SESSION['message'] = "Album berhasil dihapus";
} else {
    // Menggunakan session untuk menyampaikan pesan error
    session_start();
    $_SESSION['error'] = "Error: " . $koneksi->error;
}

$koneksi->close();

// Redirect ke halaman yang ditentukan
header("Location: ../$redirect.php");
exit();
?>