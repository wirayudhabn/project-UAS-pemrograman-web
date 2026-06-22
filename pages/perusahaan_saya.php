<?php
require_once '../middlewares/auth.php';
requireRole('recruiter');

require_once '../controllers/RecruiterController.php';

$userId = $_SESSION['user_id'];
$message = '';
$status = '';

// Handle POST request untuk update profil perusahaan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateProfilePerusahaan($userId, $_POST, $_FILES['logo'] ?? null);
    $message = $result['message'];
    $status = $result['status'];
}

// Mengambil data recruiter & perusahaan
$recruiter = getRecruiter($userId);

$logoPath = !empty($recruiter['logo']) ? '../public/' . $recruiter['logo'] : "https://ui-avatars.com/api/?name=" . urlencode($recruiter['nama_perusahaan'] ?? 'Company') . "&background=0D8ABC&color=fff&rounded=true";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perusahaan Saya - InternGo</title>
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
      <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="mb-10">
          <h2 class="text-3xl font-bold mb-2 text-gray-900 tracking-tight">Perusahaan Saya</h2>
          <p class="text-gray-500">Kelola identitas dan informasi publik perusahaan Anda di sini.</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="<?= $status === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-xl mb-8" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Container -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
          <form action="" method="POST" enctype="multipart/form-data" class="p-8 lg:p-10">
            
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Identitas Perusahaan</h3>
            
            <!-- Logo Upload -->
            <div class="mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6">
              <div class="relative group">
                <img id="logoPreview" src="<?= htmlspecialchars($logoPath) ?>" alt="Logo Perusahaan" class="w-24 h-24 rounded-2xl border-2 border-gray-100 shadow-sm object-cover transition-all bg-gray-50">
                <label for="logoInput" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white opacity-0 group-hover:opacity-100 rounded-2xl cursor-pointer transition-opacity backdrop-blur-sm">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </label>
                <input type="file" id="logoInput" name="logo" class="hidden" accept="image/*">
              </div>
              <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Logo Perusahaan</h4>
                <p class="text-xs text-gray-500 mb-3">Format yang disarankan: JPG, PNG, atau SVG (Rasio 1:1, Max 2MB).</p>
                <label for="logoInput" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">Pilih File Baru</label>
              </div>
            </div>

            <!-- Identitas Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" value="<?= htmlspecialchars($recruiter['nama_perusahaan'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Industri</label>
                <input type="text" name="industri" value="<?= htmlspecialchars($recruiter['industri'] ?? '') ?>" placeholder="Misal: Teknologi, Keuangan, Kesehatan" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>
            </div>

            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 pt-4 border-t border-gray-100">Informasi Lanjutan</h3>
            
            <div class="grid grid-cols-1 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Situs Web</label>
                <input type="url" name="website" value="<?= htmlspecialchars($recruiter['website'] ?? '') ?>" placeholder="https://www.contoh.com" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none resize-none"><?= htmlspecialchars($recruiter['alamat'] ?? '') ?></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="4" placeholder="Ceritakan tentang visi, misi, atau budaya perusahaan Anda..." class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none resize-none"><?= htmlspecialchars($recruiter['deskripsi'] ?? '') ?></textarea>
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

  <script>
    // Live Preview Logo
    document.getElementById('logoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
  </script>
</body>
</html>
