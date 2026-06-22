<?php
require_once '../middlewares/auth.php';
requireLogin();

if ($_SESSION['role'] !== 'recruiter') {
    header("Location: dashboard_mahasiswa.php");
    exit;
}

require_once '../controllers/LamaranController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_lamaran']) && isset($_POST['status']) && isset($_POST['lowongan_id'])) {
    $id_lamaran = (int) $_POST['id_lamaran'];
    $status = $_POST['status'];
    $lowongan_id = (int) $_POST['lowongan_id'];
    $pesan = isset($_POST['pesan']) ? trim($_POST['pesan']) : null;

    if (in_array($status, ['diterima', 'ditolak', 'pending'])) {
        $result = updateStatusLamaran($id_lamaran, $status, $pesan);
        
        if ($result['status'] === 'success') {
            $_SESSION['success_message'] = "Status lamaran berhasil diubah menjadi " . ucfirst($status) . ".";
        } else {
            $_SESSION['error_message'] = $result['message'];
        }
    } else {
        $_SESSION['error_message'] = "Status tidak valid.";
    }
    
    header("Location: detail_lowongan.php?id=" . $lowongan_id);
    exit;
} else {
    header("Location: dashboard_recruiter.php");
    exit;
}
?>
