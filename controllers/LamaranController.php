<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../model/LamaranModel.php';
require_once __DIR__ . '/../model/MahasiswaModel.php';
require_once __DIR__ . '/../model/LowonganModel.php';

function lamarLowongan($mahasiswa_user_id, $lowongan_id) {
    global $conn;
    $lamaranModel = new LamaranModel($conn);
    $mahasiswaModel = new MahasiswaModel($conn);
    
    // Get mahasiswa ID by user ID
    $mahasiswa = $mahasiswaModel->getMahasiswaByUserId($mahasiswa_user_id);
    if (!$mahasiswa) {
        return ["status" => "error", "message" => "Data mahasiswa tidak ditemukan."];
    }
    
    $mahasiswa_id = $mahasiswa['mahasiswa_id'];

    // Cek apakah sudah melamar
    if ($lamaranModel->cekSudahMelamar($mahasiswa_id, $lowongan_id)) {
        return ["status" => "error", "message" => "Anda sudah melamar lowongan ini."];
    }

    $tanggal_lamaran = date('Y-m-d');
    
    if ($lamaranModel->createLamaran($mahasiswa_id, $lowongan_id, $tanggal_lamaran)) {
        return ["status" => "success", "message" => "Berhasil melamar lowongan!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat melamar."];
    }
}

function updateStatusLamaran($id_lamaran, $status_baru, $pesan = null) {
    global $conn;
    $lamaranModel = new LamaranModel($conn);
    
    if ($lamaranModel->updateStatus($id_lamaran, $status_baru, $pesan)) {
        return ["status" => "success", "message" => "Status lamaran berhasil diubah."];
    } else {
        return ["status" => "error", "message" => "Gagal mengubah status lamaran."];
    }
}

function getPelamar($lowongan_id) {
    global $conn;
    $lamaranModel = new LamaranModel($conn);
    return $lamaranModel->getPelamarByLowongan($lowongan_id);
}

function getRiwayatLamaran($mahasiswa_user_id) {
    global $conn;
    $lamaranModel = new LamaranModel($conn);
    $mahasiswaModel = new MahasiswaModel($conn);
    
    // Get mahasiswa ID by user ID
    $mahasiswa = $mahasiswaModel->getMahasiswaByUserId($mahasiswa_user_id);
    if (!$mahasiswa) {
        return [];
    }
    
    return $lamaranModel->getRiwayatLamaranMahasiswa($mahasiswa['mahasiswa_id']);
}
?>
