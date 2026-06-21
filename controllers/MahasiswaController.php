<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../model/MahasiswaModel.php';

// function create mahasiswa
function createMahasiswa($dataPost, $fileFoto, $fileCv) {
  global $conn;
  $model = new MahasiswaModel($conn);

  // Validasi
  if (empty($dataPost['nama']) || empty($dataPost['email']) || empty($dataPost['password'])) {
      return ["status" => "error", "message" => "Harap isi Nama, Email, dan Password."];
  }

  // Cek email
  if ($model->emailExists($dataPost['email'])) {
      return ["status" => "error", "message" => "Email sudah terdaftar!"];
  }

  // Hash password
  $dataPost['password'] = password_hash($dataPost['password'], PASSWORD_DEFAULT);

  // Upload Files
  $uploadDirCv = __DIR__ . '/../public/uploads/cv/';
  $uploadDirFoto = __DIR__ . '/../public/uploads/foto/';
  if (!is_dir($uploadDirCv)) mkdir($uploadDirCv, 0777, true);
  if (!is_dir($uploadDirFoto)) mkdir($uploadDirFoto, 0777, true);

  $cvPath = '';
  if (isset($fileCv['name']) && $fileCv['error'] == 0) {
      $cvName = time() . '_' . basename($fileCv['name']);
      if (move_uploaded_file($fileCv['tmp_name'], $uploadDirCv . $cvName)) {
          $cvPath = 'uploads/cv/' . $cvName;
      } else {
          return ["status" => "error", "message" => "Gagal upload CV."];
      }
  }

  $fotoPath = '';
  if (isset($fileFoto['name']) && $fileFoto['error'] == 0) {
      $fotoName = time() . '_' . basename($fileFoto['name']);
      if (move_uploaded_file($fileFoto['tmp_name'], $uploadDirFoto . $fotoName)) {
          $fotoPath = 'uploads/foto/' . $fotoName;
      } else {
          return ["status" => "error", "message" => "Gagal upload Foto."];
      }
  } 

  // Skill
  $skills = isset($dataPost['skill']) ? $dataPost['skill'] : [];
  if (!empty($dataPost['custom_skills'])) {
      $skills[] = $dataPost['custom_skills'];
  }
  $dataPost['skill'] = implode(', ', $skills);

  //  Create via Model
  if ($model->createMahasiswa($dataPost, $cvPath, $fotoPath)) {
      return ["status" => "success", "message" => "Berhasil mendaftar! Silakan login."];
  } else {
      return ["status" => "error", "message" => "Terjadi kesalahan pada database saat menyimpan data."];
  }
}

// function get mahasiswa by id
function getMahasiswa($userId) {
    global $conn;
    $model = new MahasiswaModel($conn);
    return $model->getMahasiswaByUserId($userId);
}

// function update profile mahasiswa by id
function updateProfileMahasiswa($userId, $dataPost, $fileFoto, $fileCv) {
    global $conn;
    $model = new MahasiswaModel($conn);

    if (empty($dataPost['nama']) || empty($dataPost['email'])) {
        return ["status" => "error", "message" => "Nama dan Email harus diisi."];
    }

    $currentUser = $model->getMahasiswaByUserId($userId);
    if ($currentUser['email'] !== $dataPost['email']) {
        if ($model->emailExists($dataPost['email'])) {
            return ["status" => "error", "message" => "Email sudah terdaftar oleh pengguna lain!"];
        }
    }

    $uploadDirCv = __DIR__ . '/../public/uploads/cv/';
    $uploadDirFoto = __DIR__ . '/../public/uploads/foto/';
    
    $cvPath = null;
    if (isset($fileCv['name']) && $fileCv['error'] == 0) {
        if (!is_dir($uploadDirCv)) mkdir($uploadDirCv, 0777, true);
        $cvName = time() . '_' . basename($fileCv['name']);
        if (move_uploaded_file($fileCv['tmp_name'], $uploadDirCv . $cvName)) {
            $cvPath = 'uploads/cv/' . $cvName;
        } else {
            return ["status" => "error", "message" => "Gagal upload CV baru."];
        }
    }

    $fotoPath = null;
    if (isset($fileFoto['name']) && $fileFoto['error'] == 0) {
        if (!is_dir($uploadDirFoto)) mkdir($uploadDirFoto, 0777, true);
        $fotoName = time() . '_' . basename($fileFoto['name']);
        if (move_uploaded_file($fileFoto['tmp_name'], $uploadDirFoto . $fotoName)) {
            $fotoPath = 'uploads/foto/' . $fotoName;
        } else {
            return ["status" => "error", "message" => "Gagal upload Foto baru."];
        }
    }

    $skills = isset($dataPost['skill']) ? $dataPost['skill'] : [];
    if (!is_array($skills)) {
        $skills = array_map('trim', explode(',', $skills));
    }
    if (!empty($dataPost['custom_skills'])) {
        $skills[] = trim($dataPost['custom_skills']);
    }
    $dataPost['skill'] = implode(', ', array_filter($skills));

    if ($model->updateMahasiswa($userId, $dataPost, $cvPath, $fotoPath)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['nama'] = $dataPost['nama'];
        return ["status" => "success", "message" => "Profil berhasil diperbarui!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat memperbarui database."];
    }
}

// function delete mahasiswa profile
function deleteMahasiswa($userId) {
    global $conn;
    $model = new MahasiswaModel($conn);
    if ($model->deleteMahasiswa($userId)) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        return ["status" => "success", "message" => "Akun berhasil dihapus!"];
    } else {
        return ["status" => "error", "message" => "Terjadi kesalahan saat menghapus akun."];
    }
}

?>