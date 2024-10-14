<?php
include '../koneksi.php';

// Mengambil parameter redirect dari URL
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'read_foto.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM foto WHERE FotoID = $id";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit'])) {
    $judul_foto = $_POST['judul_foto'];
    $deskripsi_foto = $_POST['deskripsi_foto'];
    $query_update = "UPDATE foto SET JudulFoto = '$judul_foto', DeskripsiFoto = '$deskripsi_foto' WHERE FotoID = $id";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: $redirect"); // Menggunakan nilai redirect untuk pengalihan
        exit; // Menambahkan exit untuk menghentikan eksekusi skrip setelah pengalihan
    } else {
        echo "Error: " . $query_update . "<br>" . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Foto</title>
</head>

<body>
    <h2>Edit Foto</h2>
    <form action="" method="POST">
        <label>Judul Foto:</label><br>
        <input type="text" name="judul_foto" value="<?= $data['JudulFoto'] ?>" required><br><br>

        <label>Deskripsi Foto:</label><br>
        <textarea name="deskripsi_foto"><?= $data['DeskripsiFoto'] ?></textarea><br><br>

        <button type="submit" name="submit">Update Foto</button>
    </form>
</body>

</html>