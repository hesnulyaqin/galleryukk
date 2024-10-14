<?php
session_start();
include 'koneksi.php'; // file koneksi ke database

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['role'])) {
    header('Location: login.php'); // Redirect ke halaman login jika belum login
    exit;
}

// Jika role pengguna adalah 'user', tampilkan pesan dan kembali ke halaman sebelumnya
if ($_SESSION['role'] == 'user') {
    echo "<script>alert('Maaf, Anda bukan admin!'); window.history.back();</script>";
    exit;
}

// Inisialisasi variabel pencarian
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

// Query untuk mengambil data user dengan filter pencarian
$sql = "SELECT UserID, Username, Email, NamaLengkap, role FROM user WHERE 
        Username LIKE '%$searchTerm%' OR 
        Email LIKE '%$searchTerm%' OR 
        NamaLengkap LIKE '%$searchTerm%' OR 
        role LIKE '%$searchTerm%'"; // Ambil data dari tabel user
$result = mysqli_query($koneksi, $sql);

// Proses update role
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    // Ambil username pengguna yang diupdate
    $sql_user = "SELECT Username FROM user WHERE UserID = $user_id";
    $result_user = mysqli_query($koneksi, $sql_user);
    $row_user = mysqli_fetch_assoc($result_user);
    $username_updated = $row_user['Username'];

    // Query untuk update role
    $sql = "UPDATE user SET role = '$new_role' WHERE UserID = $user_id";
    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil mengupdate role dan username yang diupdate sama dengan username pengguna yang sedang login
        if ($_SESSION['username'] == $username_updated) {
            session_destroy(); // Hancurkan sesi
            header('Location: login.php'); // Redirect ke halaman login
            exit;
        } else {
            echo "Role berhasil diupdate.";
        }
    } else {
        echo "Gagal mengupdate role: " . mysqli_error($koneksi);
    }
}

// Proses hapus akun
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Query untuk menghapus user
    $sql = "DELETE FROM user WHERE UserID = $user_id";
    if (mysqli_query($koneksi, $sql)) {
        echo "Akun berhasil dihapus.";
    } else {
        echo "Gagal menghapus akun: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        table,
        table * {
            visibility: visible;
        }

        table {
            position: absolute;
            left: 0;
            top: 0;
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
    <div class="mb-6">
        <a href="dataalbum.php" style="text-decoration: none;">
            <button
                style="padding: 10px 15px; background-color: #007BFF; color: white; border: none; border-radius: 5px;">
                Kembali ke Data Album
            </button>
        </a>
        <button onclick="printTable()"
            style="padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            Cetak Tabel
        </button>
    </div>

    <h2>Manajemen User</h2>

    <!-- Form Pencarian -->
    <form method="POST" action="">
        <input type="text" name="search_term" placeholder="Cari Username, Email, Nama Lengkap, atau Role"
            value="<?= htmlspecialchars($searchTerm) ?>">
        <input type="submit" value="Cari">
    </form>

    <table>
        <thead>
            <tr>
                <th>UserID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['UserID'] . "</td>";
                    echo "<td>" . $row['Username'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['NamaLengkap'] . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "<td>";
                    echo "<form action='' method='post' style='display:inline;'>"; // Style inline untuk tombol
                    echo "<input type='hidden' name='user_id' value='" . $row['UserID'] . "'>";
                    echo "<select name='new_role'>";
                    echo "<option value='user'" . ($row['role'] == 'user' ? ' selected' : '') . ">User</option>";
                    echo "<option value='admin'" . ($row['role'] == 'admin' ? ' selected' : '') . ">Admin</option>";
                    echo "</select>";
                    echo "<button type='submit' name='update_role'>Update Role</button>";
                    echo "</form>";

                    // Form untuk menghapus akun
                    echo "<form action='' method='post' style='display:inline;'>"; // Style inline untuk tombol
                    echo "<input type='hidden' name='user_id' value='" . $row['UserID'] . "'>";
                    echo "<button type='submit' name='delete_user' onclick=\"return confirm('Apakah Anda yakin ingin menghapus akun ini?');\">Hapus</button>";
                    echo "</form>";

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada data user</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>

</html>

<?php
mysqli_close($koneksi);
?>