<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        // Jika bukan role yang tepat, redirect ke dashboard masing-masing
        if ($_SESSION['role'] === 'mahasiswa') {
            header("Location: dashboard_mahasiswa.php");
        } else {
            header("Location: dashboard_recruiter.php");
        }
        exit;
    }
}

function requireGuest() {
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'mahasiswa') {
            header("Location: dashboard_mahasiswa.php");
        } else {
            header("Location: dashboard_recruiter.php");
        }
        exit;
    }
}
?>
