<?php
session_start();
require 'koneksi.php'; // Pastikan koneksi.php sudah ada dan benar

// Cek apakah ada ID foto ya    ng diberikan di URL
if (isset($_GET['id'])) {
    $foto_id = $_GET['id'];

    // Ambil data foto berdasarkan FotoID
    $sql = "SELECT f.FotoID, f.LokasiFile, f.JudulFoto, u.Username, f.TanggalUnggah 
            FROM foto f 
            JOIN user u ON f.UserID = u.UserID 
            WHERE f.FotoID = '$foto_id'";
    $result = $koneksi->query($sql);

    // Cek apakah foto ditemukan
    if ($result->num_rows > 0) {
        $foto = $result->fetch_assoc(); // Ambil data foto
    } else {
        echo "Foto tidak ditemukan.";
        exit;
    }
} else {
    echo "ID foto tidak diberikan.";
    exit;
}

// Menangani pengisian komentar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek apakah pengguna sudah login
    if (!isset($_SESSION['UserID'])) {
        echo "Anda harus login untuk mengirim komentar.";
    } else {
        $FotoID = $foto_id; // Ambil FotoID dari foto yang ditampilkan
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
}

// Mengambil komentar dari database
$sql = "SELECT k.IsiKomentar, k.TanggalKomentar, u.Username 
        FROM komentarfoto k 
        JOIN user u ON k.UserID = u.UserID 
        WHERE k.FotoID = '$foto_id' 
        ORDER BY k.TanggalKomentar DESC";
$resultKomentar = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Foto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css">
    <style>
    body {
        margin: 0;
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 20px;
    }

    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 20px;
    }

    .gallery-item {
        margin: 10px;
        padding: 15px;
        text-align: center;
        border-radius: 5px;
        background-color: #f8f9fa;
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

    <div class="container">
        <div class="gallery">
            <div class="gallery-item">
                <h2 class="text-center">Detail Foto</h2>
                <div class="text-center">
                    <img src="<?= $foto['LokasiFile'] ?>" alt="<?= $foto['JudulFoto'] ?>" class="img-fluid"
                        style="max-width: 100%; height: auto; border: 2px solid #007BFF; border-radius: 5px;">
                </div>
                <h3 class="mt-3"><?= $foto['JudulFoto'] ?></h3>
                <p>Diunggah oleh: <strong><?= $foto['Username'] ?></strong></p>
                <p>Tanggal Unggah: <strong><?= date('d M Y', strtotime($foto['TanggalUnggah'])) ?></strong></p>
            </div>
        </div>

        <h2>Komentar</h2>

        <!-- Menampilkan komentar -->
        <?php if ($resultKomentar->num_rows > 0): ?>
        <?php while ($row = $resultKomentar->fetch_assoc()): ?>
        <div class="komentar">
            <span class="username"><?php echo htmlspecialchars($row['Username']); ?></span>
            <span class="tanggal"><?php echo htmlspecialchars($row['TanggalKomentar']); ?></span>
            <p><?php echo nl2br(htmlspecialchars($row['IsiKomentar'])); ?></p>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>Belum ada komentar.</p>
        <?php endif; ?>

        <!-- Form untuk menambahkan komentar -->
        <?php if (isset($_SESSION['UserID'])): ?>
        <form method="POST">
            <textarea name="isiKomentar" required placeholder="Tulis komentar..." class="form-control mb-2"></textarea>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
        <?php else: ?>
        <p>Anda harus <a href="login.php">login</a> untuk mengirim komentar.</p>
        <?php endif; ?>
    </div>

</body>

</html>

<?php
$koneksi->close(); // Tutup koneksi
?>