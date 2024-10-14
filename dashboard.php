<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Example</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
        /* Menetapkan tinggi body 100% dari viewport */
    }

    /* Style untuk sidebar */
    .sidebar {
        width: 150px;
        background-color: #f8f9fa;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        margin: 5px 0;
        color: #007bff;
        text-decoration: none;
        border-radius: 5px;
    }

    .sidebar a:hover {
        background-color: #007bff;
        color: white;
    }

    /* Style untuk konten utama */
    .main {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        /* Membuat konten scrollable jika melebihi tinggi layar */
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="beranda.php">Beranda</a>
        <a href="datafoto.php">Data foto</a>
        <a href="dataakun.php">Data Akun</a>
        <a href="dataalbum.php">Album</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <!-- Konten utama di sini -->
    </div>
</body>

</html>