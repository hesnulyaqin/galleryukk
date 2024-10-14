<?php
include '../koneksi.php';

$id = $_GET['id'];
$sql = "SELECT * FROM album WHERE AlbumID = $id";
$result = $koneksi->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaAlbum = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];

    $userID = $_POST['user_id'];

    $sql = "UPDATE album SET NamaAlbum = '$namaAlbum', Deskripsi = '$deskripsi', UserID = '$userID' WHERE AlbumID = $id";

    if ($koneksi->query($sql) === TRUE) {
        echo "Album berhasil diupdate";
        $redirect = $_GET['redirect'];
        header("Location: ../$redirect.php"); // Gunakan string interpolasi
        exit; // Tambahkan exit setelah header untuk menghentikan skrip
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }

    $koneksi->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Album</title>
</head>
<style>
/* Tambahkan styling jika perlu */
</style>

<body>
    <h1>Edit Album</h1>
    <form method="POST" action="">
        Nama Album: <input type="text" name="nama_album" value="<?php echo $row['NamaAlbum']; ?>" required><br>
        Deskripsi: <textarea name="deskripsi"><?php echo $row['Deskripsi']; ?></textarea><br>

        <input type="number" name="user_id" value="<?php echo $row['UserID']; ?>" hidden><br>
        <input type="submit" value="Update">
    </form>
</body>

</html>