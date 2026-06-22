<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../model/RecruiterModel.php';

function createRecruiter($postData, $fileLogo) {
    global $conn;
    $model = new RecruiterModel($conn);

    if (empty($postData['nama']) || empty($postData['email']) || empty($postData['password']) || empty($postData['jabatan']) || empty($postData['no_telp'])) {
        return ["status" => "error", "message" => "Harap lengkapi semua data profil."];
    }

    if ($model->emailExists($postData['email'])) {
        return ["status" => "error", "message" => "Email sudah terdaftar!"];
    }

    $userData = [
        'nama' => $postData['nama'],
        'email' => $postData['email'],
        'password' => password_hash($postData['password'], PASSWORD_DEFAULT)
    ];

    $recruiterData = [
        'jabatan' => $postData['jabatan'],
        'no_telp' => $postData['no_telp'],
        'perusahaan_id' => $postData['perusahaan_id'] ?? null
    ];

    $companyData = [];
    $logoPath = null;
    $isNewCompany = isset($postData['is_new_company']) && $postData['is_new_company'] == '1';

    if ($isNewCompany) {
        if (empty($postData['nama_perusahaan'])) {
            return ["status" => "error", "message" => "Nama perusahaan baru wajib diisi."];
        }

        $companyData = [
            'nama_perusahaan' => $postData['nama_perusahaan'],
            'industri' => $postData['industri'] ?? '',
            'alamat' => $postData['alamat'] ?? '',
            'deskripsi' => $postData['deskripsi'] ?? '',
            'website' => $postData['website'] ?? ''
        ];

        // Handle upload logo
        if (isset($fileLogo['name']) && $fileLogo['error'] == 0) {
            $uploadDir = __DIR__ . '/../public/uploads/logo/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $logoName = time() . '_' . basename($fileLogo['name']);
            if (move_uploaded_file($fileLogo['tmp_name'], $uploadDir . $logoName)) {
                $logoPath = 'uploads/logo/' . $logoName;
            } else {
                return ["status" => "error", "message" => "Gagal upload logo perusahaan."];
            }
        }
    } else {
        if (empty($recruiterData['perusahaan_id'])) {
            return ["status" => "error", "message" => "Harap pilih perusahaan atau buat perusahaan baru."];
        }
    }

    if ($model->createRecruiter($userData, $recruiterData, $companyData, $logoPath)) {
        return ["status" => "success", "message" => "Berhasil mendaftar sebagai Recruiter! Silakan login."];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan sistem saat menyimpan data."];
    }
}

function getRecruiter($userId) {
    global $conn;
    $model = new RecruiterModel($conn);
    return $model->getRecruiterByUserId($userId);
}

function updateProfileRecruiter($userId, $postData) {
    global $conn;
    $model = new RecruiterModel($conn);

    if (empty($postData['nama']) || empty($postData['email'])) {
        return ["status" => "error", "message" => "Nama dan Email harus diisi."];
    }

    $currentUser = $model->getRecruiterByUserId($userId);
    if ($currentUser['email'] !== $postData['email']) {
        if ($model->emailExists($postData['email'])) {
            return ["status" => "error", "message" => "Email sudah terdaftar oleh pengguna lain!"];
        }
    }

    $userData = ['nama' => $postData['nama'], 'email' => $postData['email']];
    $recruiterData = ['jabatan' => $postData['jabatan'], 'no_telp' => $postData['no_telp']];

    if ($model->updateRecruiter($userId, $userData, $recruiterData)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['nama'] = $postData['nama'];
        return ["status" => "success", "message" => "Profil berhasil diperbarui!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat memperbarui database."];
    }
}

function updateProfilePerusahaan($userId, $postData, $fileLogo) {
    global $conn;
    $model = new RecruiterModel($conn);
    
    $recruiter = $model->getRecruiterByUserId($userId);
    if (!$recruiter || empty($recruiter['perusahaan_id'])) {
        return ["status" => "error", "message" => "Anda tidak tertaut dengan perusahaan manapun."];
    }
    
    if (empty($postData['nama_perusahaan'])) {
        return ["status" => "error", "message" => "Nama Perusahaan tidak boleh kosong."];
    }

    $companyData = [
        'nama_perusahaan' => $postData['nama_perusahaan'],
        'industri' => $postData['industri'] ?? '',
        'alamat' => $postData['alamat'] ?? '',
        'website' => $postData['website'] ?? '',
        'deskripsi' => $postData['deskripsi'] ?? ''
    ];

    $logoPath = null;
    if (isset($fileLogo['name']) && $fileLogo['error'] == 0) {
        $uploadDir = __DIR__ . '/../public/uploads/logo/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $logoName = time() . '_' . basename($fileLogo['name']);
        if (move_uploaded_file($fileLogo['tmp_name'], $uploadDir . $logoName)) {
            $logoPath = 'uploads/logo/' . $logoName;
        } else {
            return ["status" => "error", "message" => "Gagal mengunggah logo perusahaan."];
        }
    }

    if ($model->updateCompany($recruiter['perusahaan_id'], $companyData, $logoPath)) {
        return ["status" => "success", "message" => "Profil perusahaan berhasil diperbarui!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat memperbarui data perusahaan."];
    }
}


function deleteRecruiter($userId) {
    global $conn;
    $model = new RecruiterModel($conn);
    if ($model->deleteRecruiter($userId)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        return ["status" => "success", "message" => "Akun recruiter berhasil dihapus."];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat menghapus akun."];
    }
}
?>