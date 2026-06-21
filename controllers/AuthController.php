<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';

function loginUser($email, $password) {
    global $conn;
    
    // Validasi input
    if (empty($email) || empty($password)) {
        return ["status" => "error", "message" => "Email dan password tidak boleh kosong."];
    }
    
    // Cek user berdasarkan email
    $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ["status" => "error", "message" => "Email tidak terdaftar."];
    }
    
    $user = $result->fetch_assoc();
    
    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Set session user utama
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        // Cek role untuk fetch ID spesifik (Mahasiswa / Recruiter)
        if ($user['role'] === 'mahasiswa') {
            $stmtMhs = $conn->prepare("SELECT id FROM mahasiswa WHERE user_id = ?");
            $stmtMhs->bind_param("i", $user['id']);
            $stmtMhs->execute();
            $mhsResult = $stmtMhs->get_result();
            if ($mhsResult->num_rows > 0) {
                $mhsData = $mhsResult->fetch_assoc();
                $_SESSION['mahasiswa_id'] = $mhsData['id'];
            }
        } else if ($user['role'] === 'recruiter') {
            $stmtRec = $conn->prepare("SELECT id, perusahaan_id FROM recruiter WHERE user_id = ?");
            $stmtRec->bind_param("i", $user['id']);
            $stmtRec->execute();
            $recResult = $stmtRec->get_result();
            if ($recResult->num_rows > 0) {
                $recData = $recResult->fetch_assoc();
                $_SESSION['recruiter_id'] = $recData['id'];
                $_SESSION['perusahaan_id'] = $recData['perusahaan_id'];
            }
        }
        
        return ["status" => "success", "message" => "Login berhasil."];
    } else {
        return ["status" => "error", "message" => "Password salah."];
    }
}

function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    // Redirect ke halaman login
    header("Location: login.php");
    exit;
}
?>
