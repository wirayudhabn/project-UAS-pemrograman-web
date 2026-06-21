<?php

$host = "localhost";
$username = "root";
$password = "AbiSQL2007";
$database = "magang";

// koneksi ke database
$conn = mysqli_connect($host, $username, $password, $database);

// Mengecek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
