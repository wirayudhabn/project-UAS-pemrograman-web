<?php
class LowonganModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createLowongan($data) {
        $stmt = $this->conn->prepare("INSERT INTO lowongan (perusahaan_id, judul, deskripsi, lokasi, durasi, kuota, batas_pendaftaran, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $status = 'Aktif';
        $stmt->bind_param("issssiss", 
            $data['perusahaan_id'], 
            $data['judul'], 
            $data['deskripsi'], 
            $data['lokasi'], 
            $data['durasi'], 
            $data['kuota'], 
            $data['batas_pendaftaran'],
            $status
        );
        return $stmt->execute();
    }

    public function getLowonganByPerusahaan($perusahaanId, $limit = 6, $offset = 0) {
        $stmt = $this->conn->prepare("SELECT l.*, COUNT(la.id) as jumlah_pelamar FROM lowongan l LEFT JOIN lamaran la ON l.id = la.lowongan_id WHERE l.perusahaan_id = ? GROUP BY l.id ORDER BY l.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("iii", $perusahaanId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $lowongan = [];
        while ($row = $result->fetch_assoc()) {
            $lowongan[] = $row;
        }
        return $lowongan;
    }

    public function getCountLowonganByPerusahaan($perusahaanId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lowongan WHERE perusahaan_id = ?");
        $stmt->bind_param("i", $perusahaanId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getLowonganById($id) {
        $stmt = $this->conn->prepare("SELECT l.*, p.nama_perusahaan, p.logo, p.industri, p.website FROM lowongan l JOIN perusahaan p ON l.perusahaan_id = p.id WHERE l.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllActiveLowongan($limit = 6, $offset = 0, $search = '') {
        if ($search !== '') {
            $stmt = $this->conn->prepare("SELECT l.*, p.nama_perusahaan, p.logo FROM lowongan l JOIN perusahaan p ON l.perusahaan_id = p.id WHERE l.status = 'Aktif' AND (l.judul LIKE ? OR p.nama_perusahaan LIKE ?) ORDER BY l.created_at DESC LIMIT ? OFFSET ?");
            $searchTerm = "%$search%";
            $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
        } else {
            $stmt = $this->conn->prepare("SELECT l.*, p.nama_perusahaan, p.logo FROM lowongan l JOIN perusahaan p ON l.perusahaan_id = p.id WHERE l.status = 'Aktif' ORDER BY l.created_at DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $lowongan = [];
        while ($row = $result->fetch_assoc()) {
            $lowongan[] = $row;
        }
        return $lowongan;
    }

    public function getCountActiveLowongan($search = '') {
        if ($search !== '') {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lowongan l JOIN perusahaan p ON l.perusahaan_id = p.id WHERE l.status = 'Aktif' AND (l.judul LIKE ? OR p.nama_perusahaan LIKE ?)");
            $searchTerm = "%$search%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lowongan l WHERE l.status = 'Aktif'");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function updateLowongan($id, $perusahaanId, $data) {
        $stmt = $this->conn->prepare("UPDATE lowongan SET judul = ?, deskripsi = ?, lokasi = ?, durasi = ?, kuota = ?, batas_pendaftaran = ?, status = ? WHERE id = ? AND perusahaan_id = ?");
        $stmt->bind_param("ssssissii", 
            $data['judul'], 
            $data['deskripsi'], 
            $data['lokasi'], 
            $data['durasi'], 
            $data['kuota'], 
            $data['batas_pendaftaran'],
            $data['status'],
            $id,
            $perusahaanId
        );
        return $stmt->execute();
    }

    public function deleteLowongan($id, $perusahaanId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM lowongan WHERE id = ? AND perusahaan_id = ?");
            $stmt->bind_param("ii", $id, $perusahaanId);
            $stmt->execute();
            return ['status' => 'success', 'affected_rows' => $stmt->affected_rows];
        } catch (\mysqli_sql_exception $e) {
            // MySQL error code 1451 is equivalent to SQLSTATE 23000 (Cannot delete or update a parent row: a foreign key constraint fails)
            if ($e->getCode() == 1451 || $e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'Lowongan tidak bisa dihapus karena sudah ada mahasiswa yang melamar. Silakan tolak pelamar terlebih dahulu atau ubah batas pendaftaran.'];
            }
            return ['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }
}
?>
