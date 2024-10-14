    <?php
    session_start(); // Memulai session
    include 'koneksi.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $namalengkap = $_POST['namalengkap'];
        $alamat = $_POST['alamat'];
        $role = $_POST['role'];

        // Cek apakah username sudah digunakan
        $checkUsername = "SELECT * FROM user WHERE Username = '$username'";
        $resultUsername = $koneksi->query($checkUsername);

        // Cek apakah email sudah digunakan
        $checkEmail = "SELECT * FROM user WHERE Email = '$email'";
        $resultEmail = $koneksi->query($checkEmail);

        if ($resultUsername->num_rows > 0) {
            $_SESSION['message'] = "Username sudah digunakan!"; // Simpan pesan di session
        } elseif ($resultEmail->num_rows > 0) {
            $_SESSION['message'] = "Email sudah digunakan!"; // Simpan pesan di session
        } else {
            // Jika username dan email belum digunakan, lakukan insert data
            $query = "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat, Role) VALUES ('$username', '$password', '$email', '$namalengkap', '$alamat', '$role')";

            if ($koneksi->query($query) === true) {
                $_SESSION['message'] = "Register berhasil"; // Simpan pesan di session
                header('location: login.php');
                exit(); // Tambahkan exit setelah header untuk menghentikan eksekusi skrip
            } else {
                $_SESSION['message'] = "Gagal: " . $koneksi->error; // Simpan pesan di session
            }
        }
    }

    // Menampilkan pesan jika ada
    if (isset($_SESSION['message'])) {
        echo "<p>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']); // Menghapus pesan setelah ditampilkan
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <style>
.hidden {
    display: none;
}
    </style>

    <body>
        <form method="post" action="">
            <input type="text" name="username" placeholder="username" required><br>
            <input type="password" name="password" placeholder="password" required><br>
            <input type="email" name="email" placeholder="email" required><br>
            <input type="text" name="namalengkap" placeholder="Nama Lengkap" required><br>
            <input type="text" name="alamat" placeholder="Alamat" required><br>
            <select class="hidden" name="role" required>
                <option value="user">User</option>
            </select><br>
            <button type="submit">Simpan</button><br>
            <br>

            <a href="login.php">sudah punya akun? Login disini</a>
        </form>
    </body>

    </html>