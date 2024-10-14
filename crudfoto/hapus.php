<?php
include '../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM foto WHERE FotoID = $id";

    if (mysqli_query($koneksi, $query)) {
        header('Location: ../datafoto.php');
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}
?>