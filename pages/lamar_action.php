<?php
require_once '../middlewares/auth.php';
requireLogin();

if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: dashboard_recruiter.php");
    exit;
}

require_once '../controllers/LamaranController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lowongan_id'])) {
    $lowongan_id = (int) $_POST['lowongan_id'];
    $mahasiswa_user_id = $_SESSION['user_id'];

    $result = lamarLowongan($mahasiswa_user_id, $lowongan_id);
    
    if ($result['status'] === 'success') {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    
    header("Location: detail_lowongan.php?id=" . $lowongan_id);
    exit;
} else {
    header("Location: dashboard_mahasiswa.php");
    exit;
}
?>
