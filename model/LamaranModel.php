<?php
class LamaranModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 1. CREATE Lamaran 
    public function createLamaran($mahasiswa_id, $lowongan_id, $tanggal_lamaran) {
        // Status default adalah 'pending'
        $stmt = $this->conn->prepare("INSERT INTO lamaran (mahasiswa_id, lowongan_id, tanggal_lamaran, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $mahasiswa_id, $lowongan_id, $tanggal_lamaran);
        return $stmt->execute();
    }

    // 2. READ Daftar Lamaran (Menampilkan pelamar pada setiap lowongan) 
    public function getPelamarByLowongan($lowongan_id) {
        $query = "SELECT l.*, m.universitas, m.jurusan, m.skill, m.cv_file, m.foto, u.nama, u.email 
                  FROM lamaran l
                  JOIN mahasiswa m ON l.mahasiswa_id = m.id
                  JOIN users u ON m.user_id = u.id
                  WHERE l.lowongan_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $lowongan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // 3. READ Riwayat Lamaran Mahasiswa 
    public function getRiwayatLamaranMahasiswa($mahasiswa_id) {
        $query = "SELECT l.*, low.judul, low.lokasi, low.durasi, low.status as status_lowongan, p.nama_perusahaan, p.logo 
                  FROM lamaran l
                  JOIN lowongan low ON l.lowongan_id = low.id
                  JOIN perusahaan p ON low.perusahaan_id = p.id
                  WHERE l.mahasiswa_id = ?
                  ORDER BY l.tanggal_lamaran DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $mahasiswa_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // 4. UPDATE Status Lamaran 
    public function updateStatus($id_lamaran, $status_baru, $pesan = null) {
        // status_baru bisa berupa 'pending', 'diterima', atau 'ditolak'
        $stmt = $this->conn->prepare("UPDATE lamaran SET status = ?, pesan = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status_baru, $pesan, $id_lamaran);
        return $stmt->execute();
    }

    // 5. DELETE Lamaran 
    public function deleteLamaran($id_lamaran) {
        $stmt = $this->conn->prepare("DELETE FROM lamaran WHERE id = ?");
        $stmt->bind_param("i", $id_lamaran);
        return $stmt->execute();
    }

    // Cek apakah mahasiswa sudah melamar lowongan ini
    public function cekSudahMelamar($mahasiswa_id, $lowongan_id) {
        $stmt = $this->conn->prepare("SELECT id FROM lamaran WHERE mahasiswa_id = ? AND lowongan_id = ?");
        $stmt->bind_param("ii", $mahasiswa_id, $lowongan_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>
