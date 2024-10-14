<?php
include 'koneksi.php';
session_start();

// Pastikan FotoID diambil dari URL
if (isset($_GET['FotoID'])) {
    $FotoID = $_GET['FotoID'];
} else {
    die("FotoID tidak diberikan.");
}

// Menangani pengisian komentar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['UserID']; // Ambil ID pengguna dari sesi
    $isiKomentar = $_POST['isiKomentar'];
    $tanggalKomentar = date('Y-m-d H:i:s'); // Menambahkan waktu ke tanggal

    // Cek apakah FotoID valid
    $sqlCheck = "SELECT * FROM foto WHERE FotoID = '$FotoID'";
    $resultCheck = $koneksi->query($sqlCheck);

    if ($resultCheck->num_rows > 0) {
        // Masukkan komentar ke dalam tabel
        $sql = "INSERT INTO komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES ('$FotoID', '$userID', '$isiKomentar', '$tanggalKomentar')";
        if ($koneksi->query($sql) === TRUE) {
            echo "Komentar berhasil ditambahkan.";
        } else {
            echo "Error: " . $sql . "<br>" . $koneksi->error;
        }
    } else {
        echo "FotoID tidak valid.";
    }
}

// Mengambil komentar dari database
$sql = "SELECT k.IsiKomentar, k.TanggalKomentar, u.Username 
        FROM komentarfoto k 
        JOIN user u ON k.UserID = u.UserID 
        WHERE k.FotoID = '$FotoID' 
        ORDER BY k.TanggalKomentar DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komentar Foto</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .komentar {
        border-bottom: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
    }

    .komentar .username {
        font-weight: bold;
    }

    .komentar .tanggal {
        font-size: 0.9em;
        color: #777;
    }
    </style>
</head>

<body>

    <h2>Komentar</h2>

    <!-- Form untuk menambahkan komentar -->
    <form method="POST">
        <input type="hidden" name="userID" value="<?php echo htmlspecialchars($userID); ?>">
        <textarea name="isiKomentar" required placeholder="Tulis komentar..."></textarea>
        <button type="submit">Kirim</button>
    </form>

    <!-- Menampilkan komentar -->
    <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="komentar">
        <span class="username"><?php echo htmlspecialchars($row['Username']); ?></span>
        <span class="tanggal"><?php echo htmlspecialchars($row['TanggalKomentar']); ?></span>
        <p><?php echo nl2br(htmlspecialchars($row['IsiKomentar'])); ?></p>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <p>Belum ada komentar.</p>
    <?php endif; ?>

</body>

</html>

<?php
$koneksi->close();
?>