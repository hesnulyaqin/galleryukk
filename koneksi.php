<?php
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'gallery';

    $koneksi = new mysqli($host, $user, $password,$database);

    if($koneksi->connect_error)
    die ("gagal" . $koneksi->connect_error);

?>