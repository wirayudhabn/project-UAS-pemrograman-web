<?php
require_once __DIR__ . '/../config/koneksi.php';

function getAllCompanies() {
    global $conn;
    $perusahaanList = [];
    
    $checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'perusahaan'");
    if ($checkTable && mysqli_num_rows($checkTable) > 0) {
        $query = mysqli_query($conn, "SELECT id, nama_perusahaan FROM perusahaan ORDER BY nama_perusahaan ASC");
        if ($query) {
            while($row = mysqli_fetch_assoc($query)) {
                $perusahaanList[] = $row;
            }
        }
    }
    
    return $perusahaanList;
}
?>
