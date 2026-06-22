<?php
// File: Lamaran.php
require_once 'Database.php';

class Lamaran extends Database {
    
    public function __construct() {
        parent::__construct(); // Memanggil koneksi dari Class Database
    }

    // 1. CREATE Lamaran 
    public function createLamaran($mahasiswa_id, $lowongan_id, $tanggal_lamaran) {
        // Status default adalah 'pending' berdasarkan skema database [cite: 63]
        $query = "INSERT INTO lamaran (mahasiswa_id, lowongan_id, tanggal_lamaran, status) 
                  VALUES ('$mahasiswa_id', '$lowongan_id', '$tanggal_lamaran', 'pending')";
        return $this->conn->query($query);
    }

    // 2. READ Daftar Lamaran (Menampilkan pelamar pada setiap lowongan) 
    public function getPelamarByLowongan($lowongan_id) {
        $query = "SELECT l.*, m.universitas, m.jurusan, u.nama 
                  FROM lamaran l
                  JOIN mahasiswa m ON l.mahasiswa_id = m.id
                  JOIN users u ON m.user_id = u.id
                  WHERE l.lowongan_id = '$lowongan_id'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 3. READ Riwayat Lamaran Mahasiswa 
    public function getRiwayatLamaranMahasiswa($mahasiswa_id) {
        $query = "SELECT l.*, low.judul, p.nama_perusahaan 
                  FROM lamaran l
                  JOIN lowongan low ON l.lowongan_id = low.id
                  JOIN perusahaan p ON low.perusahaan_id = p.id
                  WHERE l.mahasiswa_id = '$mahasiswa_id'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 4. UPDATE Status Lamaran 
    public function updateStatus($id_lamaran, $status_baru) {
        // status_baru bisa berupa 'pending', 'diterima', atau 'ditolak' [cite: 63]
        $query = "UPDATE lamaran SET status = '$status_baru' WHERE id = '$id_lamaran'";
        return $this->conn->query($query);
    }

    // 5. DELETE Lamaran 
    public function deleteLamaran($id_lamaran) {
        $query = "DELETE FROM lamaran WHERE id = '$id_lamaran'";
        return $this->conn->query($query);
    }

    // 6. FILTER Berdasarkan Status Lamaran 
    public function filterByStatus($lowongan_id, $status) {
        $query = "SELECT * FROM lamaran WHERE lowongan_id = '$lowongan_id' AND status = '$status'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>