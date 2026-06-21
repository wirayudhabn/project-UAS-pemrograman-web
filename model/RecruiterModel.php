<?php
class RecruiterModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Fungsi utama pendaftaran
    public function createRecruiter($userData, $recruiterData, $companyData, $logoPath) {
        $this->conn->begin_transaction();
        try {
            // 1. Jika ada companyData, berarti perusahaan baru
            $perusahaanId = $recruiterData['perusahaan_id'];
            if (!empty($companyData)) {
                $stmtCompany = $this->conn->prepare("INSERT INTO perusahaan (nama_perusahaan, industri, alamat, deskripsi, website, logo) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtCompany->bind_param("ssssss", $companyData['nama_perusahaan'], $companyData['industri'], $companyData['alamat'], $companyData['deskripsi'], $companyData['website'], $logoPath);
                $stmtCompany->execute();
                $perusahaanId = $stmtCompany->insert_id;
            }

            // 2. Insert ke users
            $stmtUser = $this->conn->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (?, ?, ?, 'recruiter', NOW())");
            $stmtUser->bind_param("sss", $userData['nama'], $userData['email'], $userData['password']);
            $stmtUser->execute();
            $userId = $stmtUser->insert_id;

            // 3. Insert ke recruiter
            $stmtRecruiter = $this->conn->prepare("INSERT INTO recruiter (user_id, perusahaan_id, jabatan, no_telp) VALUES (?, ?, ?, ?)");
            $stmtRecruiter->bind_param("iiss", $userId, $perusahaanId, $recruiterData['jabatan'], $recruiterData['no_telp']);
            $stmtRecruiter->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getRecruiterByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT u.nama, u.email, r.id as recruiter_id, r.jabatan, r.no_telp, p.id as perusahaan_id, p.nama_perusahaan, p.industri, p.alamat, p.website, p.logo, p.deskripsi FROM users u JOIN recruiter r ON u.id = r.user_id JOIN perusahaan p ON r.perusahaan_id = p.id WHERE u.id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateRecruiter($userId, $userData, $recruiterData) {
        $this->conn->begin_transaction();
        try {
            $stmtUser = $this->conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
            $stmtUser->bind_param("ssi", $userData['nama'], $userData['email'], $userId);
            $stmtUser->execute();

            $stmtRec = $this->conn->prepare("UPDATE recruiter SET jabatan = ?, no_telp = ? WHERE user_id = ?");
            $stmtRec->bind_param("ssi", $recruiterData['jabatan'], $recruiterData['no_telp'], $userId);
            $stmtRec->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function updateCompany($perusahaanId, $companyData, $logoPath) {
        if ($logoPath) {
            $stmt = $this->conn->prepare("UPDATE perusahaan SET nama_perusahaan = ?, industri = ?, alamat = ?, website = ?, deskripsi = ?, logo = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $companyData['nama_perusahaan'], $companyData['industri'], $companyData['alamat'], $companyData['website'], $companyData['deskripsi'], $logoPath, $perusahaanId);
        } else {
            $stmt = $this->conn->prepare("UPDATE perusahaan SET nama_perusahaan = ?, industri = ?, alamat = ?, website = ?, deskripsi = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $companyData['nama_perusahaan'], $companyData['industri'], $companyData['alamat'], $companyData['website'], $companyData['deskripsi'], $perusahaanId);
        }
        return $stmt->execute();
    }

    public function deleteRecruiter($userId) {
        $this->conn->begin_transaction();
        try {
            $stmtRec = $this->conn->prepare("DELETE FROM recruiter WHERE user_id = ?");
            $stmtRec->bind_param("i", $userId);
            $stmtRec->execute();

            $stmtUser = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmtUser->bind_param("i", $userId);
            $stmtUser->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>
