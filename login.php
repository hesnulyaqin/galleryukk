<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengambil data user berdasarkan username
    $query = "SELECT * FROM user WHERE Username = '$username'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifikasi password
            if (password_verify($password, $row['Password'])) {
                // Jika password cocok, buat session
                $_SESSION['UserID'] = $row['UserID']; // Ganti 'id' sesuai dengan nama kolom id di tabel user
                $_SESSION['username'] = $row['Username'];
                $_SESSION['role'] = $row['role'];

            // Redirect ke halaman yang sesuai berdasarkan role
            if ($row['role'] === 'admin') {
                header('Location: dataalbum.php'); // Sesuaikan halaman admin
            } else {
                header('Location: beranda.php'); // Sesuaikan halaman user
            }
            exit;
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <form method="post" action="">
        <input type="text" name="username" placeholder="username" required><br>
        <input type="password" name="password" placeholder="password" required><br>
        <button type="submit">Login</button><br>
        <br>
        <a href="register.php">belum ada akun? Register disini</a>
    </form>
</body>

</html>