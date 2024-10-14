<?php
include 'koneksi.php';
session_start(); // Pastikan session dimulai untuk menggunakan $_SESSION

// Inisialisasi variabel pencarian
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

// Query untuk mengambil data album, gambar pertama, username pengupload, dan tanggal dibuat
$sql = "
    SELECT a.AlbumID, a.NamaAlbum, a.TanggalDibuat, u.Username, f.LokasiFile, a.UserID as AlbumUserID
    FROM album a 
    LEFT JOIN Foto f ON a.AlbumID = f.AlbumID 
    LEFT JOIN user u ON a.UserID = u.UserID
";

// Jika ada input pencarian, tambahkan kondisi WHERE
if (!empty($searchTerm)) {
    $sql .= "
        WHERE a.NamaAlbum LIKE '%$searchTerm%' 
            OR u.Username LIKE '%$searchTerm%' 
            OR a.TanggalDibuat LIKE '%$searchTerm%'
            OR a.AlbumID LIKE '%$searchTerm%'
    ";
}

$sql .= " GROUP BY a.AlbumID";
$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Album</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // Fungsi untuk mereset input pencarian saat halaman dimuat
    window.onload = function() {
        // Kosongkan input pencarian hanya jika tidak ada nilai yang di-submit
        if (window.history.state === null) {
            document.getElementsByName('search_term')[0].value = '';
        }
    };
    </script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold text-center mb-8">Daftar Album</h1>

        <!-- Form Pencarian -->
        <div class="mb-8 w-64">
            <form class="flex justify-normal" method="POST" action="">
                <input type="text" name="search_term" placeholder="Cari Album, Username, atau Tanggal Dibuat"
                    class="border border-gray-300 px-4 py-2 rounded-lg w-full"
                    value="<?= htmlspecialchars($searchTerm) ?>"> <!-- Input tetap terisi -->
                <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Cari
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 ml-3">

            <?php
            // Memeriksa apakah ada album yang ditemukan
            if (mysqli_num_rows($result) > 0) {
                // Menampilkan setiap album dengan gambar pertama, username pengupload, dan tanggal dibuat
                while ($row = mysqli_fetch_assoc($result)) {
                    $albumID = $row["AlbumID"];
                    $namaAlbum = $row["NamaAlbum"];
                    $tanggalDibuat = $row["TanggalDibuat"];
                    $username = $row["Username"];
                    $gambar = $row["LokasiFile"] ? $row["LokasiFile"] : 'placeholder.jpg'; // Gambar default jika tidak ada
                    $albumUserID = $row["AlbumUserID"]; // UserID pemilik album
            
                    echo "
                <div class='bg-white rounded-lg shadow-lg overflow-hidden'>
                    <a href='isiallalbum.php?album_id=$albumID'>
                        <img src='$gambar' alt='$namaAlbum' class='w-full h-48 object-cover'>
                        <div class='p-4'>
                            <h2 class='text-lg font-semibold text-gray-800'>$namaAlbum</h2>
                            <p class='text-sm text-gray-600'>Dibuat oleh $username</p>
                            <p class='text-sm text-gray-600'>Tanggal Dibuat $tanggalDibuat</p>
                            <p class='text-sm text-gray-600'>ID $albumID</p>
                            <div class='mt-4'>";

                    // Memeriksa apakah pengguna yang sedang login adalah pemilik album atau admin
                    if (isset($_SESSION['UserID'])) {
                        // Cek jika pengguna adalah pemilik album atau peran adalah admin
                        if ($_SESSION['UserID'] == $albumUserID || $_SESSION['role'] == 'admin') {
                            echo "
                                <a href='crudalbum/edit.php?id=$albumID&redirect=allalbum' class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600'>Edit</a>
                                <a href='crudalbum/hapus.php?id=$albumID&redirect=allalbum' class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600' onclick='return confirm(\"Anda yakin ingin menghapus album ini?\");'>Hapus</a>
                            ";
                        }
                    }

                    echo "
                            </div>
                        </div>
                    </a>
                </div>
                ";
                }
            } else {
                echo "<p class='text-center'>Tidak ada album ditemukan.</p>";
            }

            // Menutup koneksi
            mysqli_close($koneksi);
            ?>
        </div>
    </div>
    <div class="fixed bottom-4 right-4 z-50">
        <a href="tambahalbum.php?redirect=allalbum">
            <button class="w-12 h-12 bg-gray-200 rounded-full shadow-lg flex items-center justify-center">
                <i class="text-3xl mb-2">+</i>
            </button>
        </a>
    </div>
</body>

</html>