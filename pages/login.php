<?php
require_once '../middlewares/auth.php';
requireGuest();

require_once '../controllers/AuthController.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    $result = loginUser($email, $password);
    if ($result['status'] === 'success') {
        if ($remember) {
            setcookie('login_email', $email, time() + (86400 * 30), "/");
            setcookie('login_password', $password, time() + (86400 * 30), "/");
        } else {
            if (isset($_COOKIE['login_email'])) setcookie('login_email', '', time() - 3600, "/");
            if (isset($_COOKIE['login_password'])) setcookie('login_password', '', time() - 3600, "/");
        }

        if ($_SESSION['role'] === 'mahasiswa') {
            echo "<script>window.location.href='./dashboard_mahasiswa.php';</script>";
        } else {
            echo "<script>window.location.href='./dashboard_recruiter.php';</script>";
        }
        exit;
    } else {
        $message = $result['message'];
    }
}

$savedEmail = $_COOKIE['login_email'] ?? '';
$savedPassword = $_COOKIE['login_password'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Login - InternGo</title>
  <link rel="stylesheet" href="../public/css/output.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-white font-sans text-gray-800">
  <div class="flex h-screen w-full">
    
    <!-- Left Side (Image & Quote) -->
    <div class="hidden lg:flex w-1/2 p-4">
      <div class="relative w-full h-full rounded-[2rem] overflow-hidden">
        <img src="../public/images/student_internship.png" alt="Student Internship" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        
        <!-- Text Overlay -->
        <div class="absolute inset-0 p-12 flex flex-col justify-between">
          <div>
            <span class="text-white text-xs font-semibold tracking-widest uppercase flex items-center gap-4">
              <span class="w-12 h-px bg-white/50 block"></span>
            </span>
          </div>
          <div class="text-white pb-8">
            <h1 class="text-5xl font-bold mb-4 leading-tight">Kejar<br>Mimpimu<br>Bersama Kami</h1>
            <p class="text-white/80 text-sm max-w-sm leading-relaxed">
              Kamu bisa mendapatkan apapun yang kamu inginkan jika kamu bekerja keras, mempercayai proses, dan tetap pada rencana.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side (Form) -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-24 relative">
      
      <!-- Logo -->
      <div class="absolute top-8 lg:top-12 flex items-center gap-2 font-bold text-xl">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <span>InternGo</span>
      </div>

      <div class="w-full max-w-md mt-12 lg:mt-0">
        <!-- Header -->
        <div class="text-center mb-10">
          <h2 class="text-4xl font-bold mb-3">Selamat Datang</h2>
          <p class="text-gray-500 text-sm">Masukkan email dan password untuk mengakses akun Anda</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="POST" class="space-y-5">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($savedEmail) ?>" placeholder="Masukkan email Anda" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
              <input type="password" id="password" name="password" value="<?= htmlspecialchars($savedPassword) ?>" placeholder="Masukkan password Anda" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none pr-10">
              <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Options -->
          <div class="flex items-center justify-between text-sm mt-4">
            <label class="flex items-center text-gray-600 cursor-pointer">
              <input type="checkbox" name="remember" class="rounded border-gray-300 text-black focus:ring-black mr-2" <?= !empty($savedEmail) ? 'checked' : '' ?>>
              Ingat saya
            </label>
            <a href="#" class="font-medium text-black hover:underline">Lupa Password</a>
          </div>

          <!-- Submit -->
          <button type="submit" class="w-full bg-black text-white rounded-xl py-3 text-sm font-medium hover:bg-gray-800 transition-colors mt-8">
            Masuk
          </button>
        </form>
        <!-- Sign Up Link -->
        <div class="text-center text-sm text-gray-500 mt-10 space-y-2">
          <p>Belum punya akun?</p>
          <div class="flex justify-center gap-4">
            <a href="register_mahasiswa.php" class="font-medium text-black hover:underline">Daftar sebagai Mahasiswa</a>
            <span class="text-gray-300">|</span>
            <a href="register_recruiter.php" class="font-medium text-black hover:underline">Daftar sebagai Recruiter</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      if (type === 'text') {
        // Eye off icon
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
      } else {
        // Eye on icon
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
      }
    });
  </script>
</body>
</html>
