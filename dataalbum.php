<?php
session_start();
require 'koneksi.php'; // Pastikan kamu sudah membuat file koneksi.php

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Inisialisasi variabel pencarian
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

// Query untuk mengambil data album dengan filter pencarian
$sql = "SELECT * FROM album WHERE 
        AlbumID LIKE '%$searchTerm%' OR 
        NamaAlbum LIKE '%$searchTerm%' OR 
        Deskripsi LIKE '%$searchTerm%' OR 
        UserID LIKE '%$searchTerm%'"; // Ambil data dari tabel album
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tampilkan Album</title>
    <link rel="stylesheet" href="style.css"> <!-- Tambahkan file CSS jika perlu -->
    <style>
        .data {
            position: relative;
            right: 10%;
            bottom: -120px;
        }

        a {
            text-decoration: none;
            margin-top: 10px;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .data,
            .data * {
                visibility: visible;
            }

            .data {
                position: absolute;
                left: 0;
                top: 0;
            }

            /* Sembunyikan kolom aksi pada saat print */
            table th:last-child,
            table td:last-child {
                display: none;
            }
        }
    </style>
    <script>
        function printTable() {
            window.print();
        }
    </script>
</head>

<body>

    <?php require 'dashboard.php'; ?>

    <div class="data">
        <h2>Data Album</h2>

        <!-- Form Pencarian -->
        <form method="POST" action="">
            <input type="text" name="search_term" placeholder="Cari Album ID, Nama Album, Deskripsi, atau User ID"
                value="<?= htmlspecialchars($searchTerm) ?>">
            <input type="submit" value="Cari">
        </form>

        <!-- Tambahkan tombol cetak -->
        <button onclick="printTable()"
            style="margin-bottom: 10px; padding: 10px; background-color: #007BFF; color: white; border: none; border-radius: 5px;">Cetak
            Tabel</button>

        <!-- Tampilkan pesan sukses atau error jika ada -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Hapus pesan setelah ditampilkan
                ?>
            </div>
        <?php endif; ?>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Album ID</th>
                    <th>Nama Album</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Dibuat</th>
                    <th>User ID</th>
                    <th>Aksi</th> <!-- Kolom Aksi -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Loop melalui setiap baris data
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['AlbumID'] . "</td>";
                        echo "<td>" . $row['NamaAlbum'] . "</td>";
                        echo "<td>" . $row['Deskripsi'] . "</td>";
                        echo "<td>" . $row['TanggalDibuat'] . "</td>";
                        echo "<td>" . $row['UserID'] . "</td>";

                        // Tambahkan kolom untuk aksi
                        echo "<td>
                                <a href='crudalbum/edit.php?id=" . $row['AlbumID'] . "&redirect=dataalbum'>Edit</a> |
                                <a href='crudalbum/hapus.php?id=" . $row['AlbumID'] . "&redirect=dataalbum' onclick='return confirm(\"Anda yakin ingin menghapus album ini?\")'>Hapus</a>
                              </td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada album yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="tambahalbum.php">Tambah Album</a> <!-- Link ke halaman tambah album -->
    </div>

</body>

</html>

<?php
$koneksi->close();
?>