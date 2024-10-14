<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi ke database sudah benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Inisialisasi variabel pencarian
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

// Query untuk mengambil data foto dengan filter pencarian
$sql = "SELECT * FROM foto WHERE 
        JudulFoto LIKE '%$searchTerm%' OR 
        FotoID LIKE '%$searchTerm%' OR 
        DeskripsiFoto LIKE '%$searchTerm%'"; // Ambil data dari tabel foto
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampilkan Foto</title>
    <link rel="stylesheet" href="style.css"> <!-- Tambahkan file CSS jika perlu -->
    <style>
    .data {
        position: relative;
        right: 5%;
        bottom: -100px;
    }
    </style>
    <!-- <script>
    function printImage(imageSrc) {
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Cetak Gambar</title></head><body style="margin:0;">');
        // Gambar akan memenuhi lebar halaman cetak
        printWindow.document.write('<img src="' + imageSrc + '" style="width:100vw; height:100vh; object-fit:cover;">');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    }
    </script> -->
</head>

<body>

    <?php
    require 'dashboard.php'; // Mengambil dashboard
    ?>
    <div class="data">
        <h2>Daftar Foto</h2>

        <!-- Form Pencarian -->
        <form method="POST" action="">
            <input type="text" name="search_term" placeholder="Cari Judul, ID, atau Deskripsi"
                value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Cari">
        </form>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Foto ID</th>
                    <th>Judul Foto</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Unggah</th>
                    <th>Album ID</th>
                    <th>User ID</th>
                    <th>Gambar</th>
                    <th>Aksi</th> <!-- Kolom Aksi -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Loop melalui setiap baris data
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['FotoID'] . "</td>";
                        echo "<td>" . $row['JudulFoto'] . "</td>";
                        echo "<td>" . $row['DeskripsiFoto'] . "</td>";
                        echo "<td>" . $row['TanggalUnggah'] . "</td>";
                        echo "<td>" . $row['AlbumID'] . "</td>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td><img src='" . $row['LokasiFile'] . "' alt='" . $row['JudulFoto'] . "' style='width:100px; height:auto;'></td>"; // Menampilkan gambar thumbnail
                
                        // Tambahkan kolom untuk aksi termasuk cetak gambar
                        echo "<td>
                                <a href='crudfoto/view.php?id=" . $row['FotoID'] . "'>View</a> |
                                <a href='crudfoto/edit.php?id=" . $row['FotoID'] . "'>Edit</a> |
                                <a href='crudfoto/hapus.php?id=" . $row['FotoID'] . "' onclick='return confirm(\"Anda yakin ingin menghapus foto ini?\")'>Hapus</a> |
                                <a href='download.php?image=" . urlencode(basename($row['LokasiFile'])) . "' class='action-button cetak-button'>unduh</a>
                              </td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada foto yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="tambahfoto.php?redirect_id=datafoto.php">Tambah Foto</a> <!-- Link ke halaman tambah foto -->
    </div>
</body>

</html>

<?php
$koneksi->close();
?>