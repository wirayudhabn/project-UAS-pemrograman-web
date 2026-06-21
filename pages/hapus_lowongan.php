<?php
require_once '../middlewares/auth.php';
requireRole('recruiter');
require_once '../controllers/LowonganController.php';

$id = $_GET['id'] ?? 0;
$userId = $_SESSION['user_id'];

if ($id) {
    $result = deleteLowonganAction($id, $userId);
    $_SESSION['flash_message'] = $result['message'];
    $_SESSION['flash_status'] = $result['status'];
}

header("Location: dashboard_recruiter.php");
exit;
?>
