<?php
session_start();
include 'koneksi.php';

// Mengambil album ID dari query string
$albumID = isset($_GET['album_id']) ? intval($_GET['album_id']) : 0;

// Fungsi untuk menangani Like dan Unlike
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['like_foto'])) {
    // Jika user belum login, redirect ke halaman login
    if (!isset($_SESSION['UserID'])) {
        echo "<script>alert('Anda harus login untuk memberikan Like!'); window.location.href = 'login.php';</script>";
        exit;
    }

    $fotoID = $_POST['foto_id'];
    $userID = $_SESSION['UserID'];
    $tanggalLike = date('Y-m-d H:i:s');

    // Cek apakah pengguna sudah memberikan like pada foto ini
    $sqlCheck = "SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?";
    $stmt = mysqli_prepare($koneksi, $sqlCheck);
    mysqli_stmt_bind_param($stmt, "ii", $fotoID, $userID);
    mysqli_stmt_execute($stmt);
    $resultCheck = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultCheck) > 0) {
        // Jika sudah memberikan like, hapus dari tabel likefoto
        $sqlUnlike = "DELETE FROM likefoto WHERE FotoID = ? AND UserID = ?";
        $stmt = mysqli_prepare($koneksi, $sqlUnlike);
        mysqli_stmt_bind_param($stmt, "ii", $fotoID, $userID);
        mysqli_stmt_execute($stmt);
    } else {
        // Jika belum memberikan like, tambahkan ke tabel likefoto
        $sqlLike = "INSERT INTO likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sqlLike);
        mysqli_stmt_bind_param($stmt, "iis", $fotoID, $userID, $tanggalLike);
        mysqli_stmt_execute($stmt);
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?album_id=' . $albumID);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Foto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
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

    .icon-bar {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 20px;
        font-size: 20px;
    }

    .like-button {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .like-count {
        font-size: 14px;
        color: black;
        margin-left: 10px;
    }
    </style>
</head>

<body class="bg-gray-200">
    <?php
    // Query untuk mengambil foto berdasarkan album ID dengan prepared statement
    $sql = "SELECT f.*, 
            (SELECT COUNT(*) FROM likefoto WHERE FotoID = f.FotoID) AS JumlahLike
            FROM foto f 
            WHERE f.AlbumID = ?";

    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $albumID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h2 class='flex justify-center mt-10 rounded-2xl'>Foto dalam Album</h2>";
        echo "<div class='gallery'>";

        while ($row = mysqli_fetch_assoc($result)) {
            $judulFoto = htmlspecialchars($row["JudulFoto"]);
            $lokasiFile = htmlspecialchars($row["LokasiFile"]);
            $fotoID = $row['FotoID'];
            $userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : null; // Memastikan UserID tersedia
            $jumlahLike = $row['JumlahLike'];

            // Cek like status dengan prepared statement
            $sqlCheckLike = "SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?";
            $stmtCheck = mysqli_prepare($koneksi, $sqlCheckLike);
            mysqli_stmt_bind_param($stmtCheck, "ii", $fotoID, $userID);
            mysqli_stmt_execute($stmtCheck);
            $resultCheckLike = mysqli_stmt_get_result($stmtCheck);
            $isLiked = (mysqli_num_rows($resultCheckLike) > 0);

            echo "<div class='gallery-item'>
                    <a href='viewkomen.php?id={$fotoID}'>
                        <img src='$lokasiFile' alt='$judulFoto'>
                    </a>
                    <p>$judulFoto</p>
                    <div class='flex justify-center items-center space-x-4'>";

            echo "<form action='' method='POST' class='flex items-center'>";
            
            // Cek apakah user sudah login untuk like button
            if (isset($_SESSION['UserID'])) {
                echo "<input type='hidden' name='foto_id' value='$fotoID'>
                    <button type='submit' name='like_foto' class='like-button flex items-center'>
                        <i class='fas fa-thumbs-" . ($isLiked ? "down" : "up") . "'></i>
                        <span class='ml-2'>" . ($isLiked ? 'Unlike' : 'Like') . "</span>
                        <p class='like-count ml-4'>$jumlahLike</p>
                    </button>";
            } else {
                // Jika belum login, button akan memberikan notifikasi konfirmasi
                echo "<button type='button' class='like-button flex items-center' onclick=\"confirmLogin();\">
                        <i class='fas fa-thumbs-up'></i>
                        <span class='ml-2'>Like</span>
                        <p class='like-count ml-4'>$jumlahLike</p>
                      </button>";
            }

            echo "</form>";

            echo "<div>
                    <i class='fas fa-comment'></i> 
                    <a href='viewkomen.php?id={$fotoID}'>Comment</a>
                  </div>";

            echo "</div>";

            // Hanya menampilkan tombol Edit jika UserID sesuai
            if (isset($_SESSION['UserID']) && $userID == $row['UserID']) {
                echo "<div class='flex justify-center mt-2'>
                        <a href='crudfoto/edit.php?id=$fotoID&redirect=" . urlencode($_SERVER['REQUEST_URI']) . "'>
                            <button class='bg-red-500 text-white py-2 px-4 rounded'>
                                Edit
                            </button>
                        </a>
                      </div>";
            }

            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<h3 class='text-center my-4'>Tidak ada foto ditemukan dalam album ini.</h3>";
    }

    // Menutup prepared statements
    if (isset($stmt))
        mysqli_stmt_close($stmt);
    if (isset($stmtCheck))
        mysqli_stmt_close($stmtCheck);

    // Menutup koneksi database di akhir file
    mysqli_close($koneksi);
    ?>
    <script>
    function confirmLogin() {
        var confirmation = confirm("Anda harus login untuk memberikan Like. Apakah Anda ingin login sekarang?");
        if (confirmation) {
            window.location.href = 'login.php'; // Arahkan ke halaman login jika "Ya"
        }
        // Tidak ada aksi jika user memilih "Tidak"
    }
    </script>
</body>

</html>