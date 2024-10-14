<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi.php sudah ada dan benar

// Inisialisasi variabel pencarian
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Ambil data foto dari database dengan opsi pencarian~~
$sql = "SELECT f.FotoID, f.LokasiFile, f.JudulFoto, u.Username, f.TanggalUnggah, f.UserID, 
        (SELECT COUNT(*) FROM likefoto WHERE FotoID = f.FotoID) AS JumlahLike
        FROM foto f 
        JOIN user u ON f.UserID = u.UserID";

if (!empty($searchQuery)) {
    $sql .= " WHERE f.JudulFoto LIKE '%$searchQuery%'
                OR u.Username LIKE '%$searchQuery%'
                OR f.TanggalUnggah LIKE '%$searchQuery%'
    
    ";
}

$sql .= " ORDER BY f.TanggalUnggah DESC";
$result = $koneksi->query($sql);

// Cek apakah ada foto yang diunggah
$fotoList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fotoList[] = $row; // Simpan hasil ke dalam array
    }
}

// Ambil role pengguna dari sesi
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Pastikan Role sudah disimpan dalam session saat login

// Fungsi untuk menangani Like dan Unlike
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['like_foto'])) {
    if (!isset($_SESSION['UserID'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk menyukai foto!'); window.location.href = 'login.php';</script>";
        exit;
    }

    $fotoID = $_POST['foto_id'];
    $userID = $_SESSION['UserID'];
    $tanggalLike = date('Y-m-d H:i:s');

    // Cek apakah pengguna sudah memberikan like pada foto ini
    $sqlCheck = "SELECT * FROM likefoto WHERE FotoID = '$fotoID' AND UserID = '$userID'";
    $resultCheck = $koneksi->query($sqlCheck);

    if ($resultCheck->num_rows > 0) {
        // Jika sudah memberikan like, hapus dari tabel likefoto
        $sqlUnlike = "DELETE FROM likefoto WHERE FotoID = '$fotoID' AND UserID = '$userID'";
        if ($koneksi->query($sqlUnlike) === TRUE) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        // Jika belum memberikan like, tambahkan ke tabel likefoto
        $sqlLike = "INSERT INTO likefoto (FotoID, UserID, TanggalLike) 
                    VALUES ('$fotoID', '$userID', '$tanggalLike')";
        if ($koneksi->query($sqlLike) === TRUE) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// // Fungsi untuk menangani komentar
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_foto'])) {
//     $fotoID = $_POST['foto_id'];
//     $userID = $_SESSION['UserID'];
//     $komentar = $_POST['komentar'];
//     $tanggalKomentar = date('Y-m-d H:i:s');

//     // Query untuk memasukkan komentar ke tabel komentarfoto
//     $sqlComment = "INSERT INTO komentarfoto (FotoID, UserID, Komentar, TanggalKomentar) 
//                    VALUES ('$fotoID', '$userID', '$komentar', '$tanggalKomentar')";
//     if ($koneksi->query($sqlComment) === TRUE) {
//         header('Location: ' . $_SERVER['PHP_SELF']); // Redirect ke halaman ini lagi
//         exit;
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    nav {
        display: flex;
        background-color: #007BFF;
        padding: 10px;
    }

    nav ul {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    nav ul li {
        margin-right: 15px;
    }

    nav a {
        color: white;
        text-decoration: none;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 20px;
    }

    .gallery-item {
        margin: 15px;
        padding: 12px;
        text-align: center;
        border-radius: 10px;
        background-color: #f8f9fa;
        width: 250px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .gallery-item p {
        margin: 5px 0;
        font-size: 14px;
    }

    .gallery-item .icon-bar {
        margin-top: 10px;
    }

    .gallery-item .edit-button {
        margin-top: 10px;
    }

    .icon-bar {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 20px;
        font-size: 20px;
    }

    .icon-bar i {
        cursor: pointer;
    }

    .icon-bar i:hover {
        color: #0056b3;
    }

    .like-count p {
        font-size: 14px;
        color: black;
        margin-left: 10px;
    }
    </style>
</head>

<body class="bg-gray-200">

    <?php require 'navbar.php'; ?>

    <h2 class="flex justify-center mt-10 rounded-2xl">Galeri Foto</h2>

    <!-- Form Pencarian -->
    <div class="flex justify-center mt-4">
        <form action="" method="POST" class="flex">
            <input type="text" name="search" placeholder="Cari foto..." value="<?= htmlspecialchars($searchQuery) ?>"
                class="p-2 border rounded-l">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-r">Cari</button>
        </form>
    </div>

    <div class="gallery">
        <?php if (!empty($fotoList)): ?>
        <?php foreach ($fotoList as $foto): ?>
        <div class="gallery-item">
            <p class="flex justify-start">ID <?= $foto['FotoID'] ?></p>
            <a href="viewkomen.php?id=<?= $foto['FotoID']; ?>">
                <img src="<?= $foto['LokasiFile'] ?>" alt="<?= $foto['JudulFoto'] ?>">
            </a>


            <p><?= $foto['JudulFoto'] ?></p>
            <p>Diunggah oleh <?= $foto['Username'] ?></p>
            <p><?= date('d M Y', strtotime($foto['TanggalUnggah'])) ?></p>

            <div class="flex justify-center items-center space-x-4">
                <?php
                        $sqlCheck = "SELECT * FROM likefoto WHERE FotoID = '{$foto['FotoID']}' AND UserID = '" . (isset($_SESSION['UserID']) ? $_SESSION['UserID'] : 0) . "'";
                        $resultCheck = $koneksi->query($sqlCheck);
                        ?>

                <!-- Form untuk Like/Unlike -->
                <form action="" method="POST" class="flex items-center" onsubmit="return checkLogin()">
                    <input type="hidden" name="foto_id" value="<?= $foto['FotoID'] ?>">
                    <button type="submit" name="like_foto" class="like-button flex items-center">
                        <i class="fas fa-thumbs-up"></i>
                        <span
                            class="ml-2"><?= ($resultCheck && $resultCheck->num_rows > 0) ? 'Unlike' : 'Like' ?></span>
                        <p class="like-count ml-4"><?= $foto['JumlahLike'] ?></p>
                    </button>
                </form>

                <div>
                    <a href="viewkomen.php?id=<?= $foto['FotoID']; ?>">
                        <i class="fas fa-comment"></i> Comment
                    </a>
                </div>
            </div>

            <!-- Cek jika pengguna adalah pemilik foto atau admin, tampilkan tombol Edit -->
            <?php if (isset($_SESSION['UserID']) && ($_SESSION['UserID'] == $foto['UserID'] || $userRole == 'admin')): ?>
            <div class="flex justify-center mt-2">
                <a href="crudfoto/edit.php?id=<?= $foto['FotoID']; ?>&redirect=<?= $_SERVER['PHP_SELF']; ?>"
                    class="edit-button bg-red-500 text-white px-2 py-1 rounded">Edit</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-center">Tidak ada foto ditemukan.</p>
        <?php endif; ?>
    </div>

    <script>
    function checkLogin() {
        <?php if (!isset($_SESSION['UserID'])): ?>
        alert('Silakan login terlebih dahulu untuk menyukai foto!');
        return false; // Mencegah pengiriman form jika pengguna tidak login
        <?php endif; ?>
        return true; // Mengizinkan pengiriman form jika pengguna sudah login
    }
    </script>
</body>

</html>