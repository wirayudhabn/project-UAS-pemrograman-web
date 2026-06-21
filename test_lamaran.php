<?php
// File: test_lamaran.php
require_once 'Lamaran.php';

$lamaranObj = new Lamaran();

// CONTOH TES CREATE (Pastikan ID mahasiswa dan lowongan ini ada di databasemu!)
// Uncomment baris di bawah ini untuk mengetes insert data
// $lamaranObj->createLamaran(1, 1, date('Y-m-d'));
// echo "Data berhasil ditambahkan!";

// CONTOH TES READ (Menampilkan riwayat lamaran mahasiswa dengan ID 1)
$data_riwayat = $lamaranObj->getRiwayatLamaranMahasiswa(1);

echo "<h3>Test Riwayat Lamaran Mahasiswa (ID: 1)</h3>";
echo "<table border='1'>
        <tr>
            <th>ID Lamaran</th>
            <th>Posisi/Lowongan</th>
            <th>Perusahaan</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>";

foreach ($data_riwayat as $row) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['judul']}</td>
            <td>{$row['nama_perusahaan']}</td>
            <td>{$row['tanggal_lamaran']}</td>
            <td>{$row['status']}</td>
          </tr>";
}
echo "</table>";
?>