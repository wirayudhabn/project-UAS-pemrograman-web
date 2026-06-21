<?php
require_once '../middlewares/auth.php';
requireRole('recruiter');

require_once '../controllers/RecruiterController.php';

$userId = $_SESSION['user_id'];
$message = '';
$status = '';

// Handle POST request untuk update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateProfileRecruiter($userId, $_POST);
    $message = $result['message'];
    $status = $result['status'];
}

// Mengambil data recruiter
$recruiter = getRecruiter($userId);

$logoPath = !empty($recruiter['logo']) ? '../public/' . $recruiter['logo'] : "https://ui-avatars.com/api/?name=" . urlencode($recruiter['nama_perusahaan'] ?? 'Company') . "&background=0D8ABC&color=fff&rounded=true";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - InternGo</title>
  <link rel="stylesheet" href="../public/css/output.css">

</head>
<body class="bg-[#FAFAFA] text-gray-800 overflow-hidden flex h-screen">

  <!-- Sidebar Component -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
    
    <!-- Navbar Component -->
    <?php include '../components/navbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-8 lg:p-12 scroll-smooth">
      <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="mb-10">
          <h2 class="text-3xl font-serif mb-2 text-gray-900 tracking-tight font-bold">Profil Recruiter</h2>
          <p class="text-gray-500">Kelola informasi pribadi dan kontak profesionalmu di sini.</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="<?= $status === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-xl mb-8" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Container (Glassmorphism / Premium Card) -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden max-w-3xl">
          <div class="bg-gray-50 p-8 border-b border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($recruiter['nama'] ?? '') ?></h3>
            <p class="text-sm font-medium text-gray-600 mb-3"><?= htmlspecialchars($recruiter['jabatan'] ?? '') ?></p>
            <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 shadow-sm">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
              <?= htmlspecialchars($recruiter['nama_perusahaan'] ?? '') ?>
            </span>
          </div>

          <form action="" method="POST" class="p-8 lg:p-10">
            
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Informasi Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($recruiter['nama'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($recruiter['email'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
            </div>

            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 pt-4 border-t border-gray-100">Kontak & Pekerjaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan (Job Title)</label>
                <input type="text" name="jabatan" value="<?= htmlspecialchars($recruiter['jabatan'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                <input type="text" name="no_telp" value="<?= htmlspecialchars($recruiter['no_telp'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-6 border-t border-gray-100 mt-2">
              <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-semibold hover:bg-gray-800 transition-colors shadow-lg shadow-black/10">
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </main>
</body>
</html>
