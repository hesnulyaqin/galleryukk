<?php
session_start();
require 'koneksi.php'; // Pastikan kamu sudah membuat file koneksi.php

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Proses upload foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul_foto = $_POST['judul_foto'];
    $deskripsi_foto = $_POST['deskripsi_foto'];
    $album_id = $_POST['album_id'];
    $user_id = $_SESSION['UserID']; // Ambil UserID dari session

    // Proses upload file
    $lokasi_file = 'uploads/'; // Folder untuk menyimpan file
    $nama_file = basename($_FILES['foto']['name']);
    $target_file = $lokasi_file . $nama_file;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file adalah gambar
    $check = getimagesize($_FILES['foto']['tmp_name']);
    if ($check === false) {
        echo "File yang diupload bukan gambar.";
        $uploadOk = 0;
    }

    // Cek ukuran file (maksimal 2MB)
    if ($_FILES['foto']['size'] > 2000000) {
        echo "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Jika semuanya oke, upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Masukkan data ke database dengan NOW() untuk tanggal unggah
            $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                    VALUES ('$judul_foto', '$deskripsi_foto', NOW(), '$target_file', '$album_id', '$user_id')";
            if ($koneksi->query($sql) === TRUE) {
                echo "Foto berhasil diupload.";

                // Ambil nilai redirect dari URL jika ada
                $redirect_id = isset($_GET['redirect_id']) ? $_GET['redirect_id'] : 'datafoto.php';
                header("Location: $redirect_id");
                exit(); // Pastikan untuk keluar setelah header
            } else {
                echo "Terjadi kesalahan saat menyimpan data ke database: " . $koneksi->error;
            }
        } else {
            echo "Terjadi kesalahan saat mengupload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto</title>
    <link rel="stylesheet" href="style.css"> <!-- Tambahkan file CSS jika perlu -->
</head>

<body>
    <h2>Upload Foto</h2>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="judul_foto">Judul Foto:</label><br>
        <input type="text" name="judul_foto" required><br>

        <label for="deskripsi_foto">Deskripsi Foto:</label><br>
        <textarea name="deskripsi_foto" required></textarea><br>

        <label for="album_id">Album:</label><br>
        <select name="album_id" required>
            <?php
            // Ambil UserID dari sesi pengguna yang sedang login
            $user_id = $_SESSION['UserID']; // Pastikan ini sesuai dengan nama variabel sesi UserID Anda
            
            // Ambil data album yang sesuai dengan UserID dari database
            $sql = "SELECT AlbumID, NamaAlbum FROM album WHERE UserID = $user_id";
            $result = $koneksi->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['AlbumID'] . "'>" . $row['NamaAlbum'] . "</option>";
            }
            ?>
        </select><br>

        <label for="foto">Upload Foto:</label><br>
        <input type="file" name="foto" accept="image/*" required><br>

        <button type="submit">Upload</button>
    </form>

</body>

</html>

<?php
$koneksi->close();
?>