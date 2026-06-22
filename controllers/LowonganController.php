<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../model/LowonganModel.php';
require_once __DIR__ . '/../model/RecruiterModel.php';

function createLowongan($userId, $postData) {
    global $conn;
    $model = new LowonganModel($conn);
    $recruiterModel = new RecruiterModel($conn);

    $recruiter = $recruiterModel->getRecruiterByUserId($userId);
    if (!$recruiter || empty($recruiter['perusahaan_id'])) {
        return ["status" => "error", "message" => "Anda harus mengatur Profil Perusahaan terlebih dahulu sebelum membuat lowongan."];
    }

    $judul = trim($postData['judul']);
    $deskripsi = trim($postData['deskripsi']);
    $lokasi = trim($postData['lokasi']);
    $durasi = trim($postData['durasi']);
    $kuota = (int) $postData['kuota'];
    $batas_pendaftaran = $postData['batas_pendaftaran'];

    if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($durasi) || empty($kuota) || empty($batas_pendaftaran)) {
        return ["status" => "error", "message" => "Semua field wajib diisi."];
    }

    // Validasi dari github teman user
    if ($kuota <= 0) {
        return ["status" => "error", "message" => "Kuota lowongan minimal 1 orang."];
    }

    $hari_ini = date('Y-m-d');
    if ($batas_pendaftaran < $hari_ini) {
        return ["status" => "error", "message" => "Batas pendaftaran tidak boleh menggunakan tanggal yang sudah lewat."];
    }

    $data = [
        'perusahaan_id' => $recruiter['perusahaan_id'],
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'lokasi' => $lokasi,
        'durasi' => $durasi,
        'kuota' => $kuota,
        'batas_pendaftaran' => $batas_pendaftaran
    ];

    if ($model->createLowongan($data)) {
        return ["status" => "success", "message" => "Lowongan berhasil diterbitkan!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan sistem saat menyimpan lowongan."];
    }
}

function getLowonganRecruiter($userId, $page = 1, $limit = 6) {
    global $conn;
    $model = new LowonganModel($conn);
    $recruiterModel = new RecruiterModel($conn);

    $recruiter = $recruiterModel->getRecruiterByUserId($userId);
    if (!$recruiter || empty($recruiter['perusahaan_id'])) {
        return ['data' => [], 'total_pages' => 0, 'current_page' => 1];
    }

    $offset = ($page - 1) * $limit;
    $data = $model->getLowonganByPerusahaan($recruiter['perusahaan_id'], $limit, $offset);
    $totalCount = $model->getCountLowonganByPerusahaan($recruiter['perusahaan_id']);
    $totalPages = ceil($totalCount / $limit);

    return [
        'data' => $data,
        'total_pages' => $totalPages,
        'current_page' => $page
    ];
}

function getDetailLowongan($id) {
    global $conn;
    $model = new LowonganModel($conn);
    return $model->getLowonganById($id);
}

function editLowongan($id, $userId, $postData) {
    global $conn;
    $model = new LowonganModel($conn);
    $recruiterModel = new RecruiterModel($conn);

    $recruiter = $recruiterModel->getRecruiterByUserId($userId);
    if (!$recruiter || empty($recruiter['perusahaan_id'])) {
        return ["status" => "error", "message" => "Akses ditolak."];
    }
    
    $perusahaan_id = $recruiter['perusahaan_id'];

    $judul = trim($postData['judul']);
    $deskripsi = trim($postData['deskripsi']);
    $lokasi = trim($postData['lokasi']);
    $durasi = trim($postData['durasi']);
    $kuota = (int) $postData['kuota'];
    $batas_pendaftaran = $postData['batas_pendaftaran'];
    $status = isset($postData['status']) ? $postData['status'] : 'Aktif';

    if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($durasi) || empty($kuota) || empty($batas_pendaftaran)) {
        return ["status" => "error", "message" => "Semua field wajib diisi."];
    }

    if ($kuota <= 0) {
        return ["status" => "error", "message" => "Kuota lowongan minimal 1 orang."];
    }

    $hari_ini = date('Y-m-d');
    if ($batas_pendaftaran < $hari_ini) {
        return ["status" => "error", "message" => "Pembaruan gagal. Batas pendaftaran yang baru tidak boleh menggunakan tanggal yang sudah lewat."];
    }

    $data = [
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'lokasi' => $lokasi,
        'durasi' => $durasi,
        'kuota' => $kuota,
        'batas_pendaftaran' => $batas_pendaftaran,
        'status' => $status
    ];

    if ($model->updateLowongan($id, $perusahaan_id, $data)) {
        return ["status" => "success", "message" => "Data lowongan berhasil diperbarui!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan sistem saat memperbarui lowongan."];
    }
}

function getActiveLowongan($page = 1, $limit = 6, $search = '') {
    global $conn;
    $model = new LowonganModel($conn);
    
    $offset = ($page - 1) * $limit;
    $data = $model->getAllActiveLowongan($limit, $offset, $search);
    $totalCount = $model->getCountActiveLowongan($search);
    $totalPages = ceil($totalCount / $limit);

    return [
        'data' => $data,
        'total_pages' => $totalPages,
        'current_page' => $page
    ];
}

function deleteLowonganAction($id, $userId) {
    global $conn;
    $model = new LowonganModel($conn);
    $recruiterModel = new RecruiterModel($conn);

    $recruiter = $recruiterModel->getRecruiterByUserId($userId);
    if (!$recruiter || empty($recruiter['perusahaan_id'])) {
        return ["status" => "error", "message" => "Akses ditolak."];
    }
    
    $perusahaan_id = $recruiter['perusahaan_id'];
    
    $result = $model->deleteLowongan($id, $perusahaan_id);
    
    if ($result['status'] === 'success') {
        if ($result['affected_rows'] > 0) {
            return ["status" => "success", "message" => "Lowongan berhasil dihapus."];
        } else {
            return ["status" => "error", "message" => "Data tidak ditemukan atau Anda tidak memiliki hak akses."];
        }
    } else {
        return ["status" => "error", "message" => $result['message']];
    }
}
?>
