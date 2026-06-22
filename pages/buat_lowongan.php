<?php
require_once '../middlewares/auth.php';
requireRole('recruiter');

require_once '../controllers/LowonganController.php';

$userId = $_SESSION['user_id'];
$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createLowongan($userId, $_POST);
    $message = $result['message'];
    $status = $result['status'];
    
    // Jika sukses, mungkin ingin redirect kembali ke dashboard, atau tetap di sini melihat pesan
    if ($status === 'success') {
        header("refresh:2;url=dashboard_recruiter.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Lowongan - InternGo</title>
  <link rel="stylesheet" href="../public/css/output.css">
</head>
<body class="bg-[#FAFAFA] text-gray-800 overflow-hidden flex h-screen">

  <!-- Sidebar -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
    
    <!-- Navbar -->
    <?php include '../components/navbar.php'; ?>

    <div class="flex-1 overflow-y-auto p-8 lg:p-12 scroll-smooth">
      <div class="max-w-4xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-10 flex items-center gap-4">
          <a href="dashboard_recruiter.php" class="p-2 bg-white rounded-full border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </a>
          <div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Buat Lowongan Baru</h2>
            <p class="text-gray-500 text-sm mt-1">Publikasikan kesempatan magang untuk menemukan talenta terbaik.</p>
          </div>
        </div>

        <?php if (!empty($message)): ?>
        <div class="<?= $status === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-xl mb-8 flex items-center justify-between" role="alert">
          <span class="block sm:inline font-medium"><?= htmlspecialchars($message) ?></span>
          <?php if ($status === 'success'): ?>
          <svg class="animate-spin h-5 w-5 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Form Container -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
          <form action="" method="POST" class="p-8 lg:p-10">
            
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Informasi Utama</h3>
            
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Lowongan</label>
              <input type="text" name="judul" placeholder="Contoh: Frontend Developer Intern" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
            </div>

            <div class="mb-8">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Pekerjaan & Persyaratan</label>
              <textarea name="deskripsi" rows="6" placeholder="Jelaskan secara detail mengenai pekerjaan, tanggung jawab, dan kualifikasi yang dibutuhkan..." class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none resize-none" required></textarea>
            </div>

            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 pt-4 border-t border-gray-100">Detail Spesifik</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Penempatan</label>
                <input type="text" name="lokasi" placeholder="Contoh: Jakarta Selatan / WFH" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Durasi Magang</label>
                <input type="text" name="durasi" placeholder="Contoh: 3 Bulan / 6 Bulan" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kuota Penerimaan</label>
                <input type="number" name="kuota" min="1" placeholder="Berapa banyak posisi yang tersedia?" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Akhir Pendaftaran</label>
                <input type="date" name="batas_pendaftaran" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-black focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-6 border-t border-gray-100 mt-2">
              <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-semibold hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Terbitkan Lowongan
              </button>
            </div>
            
          </form>
        </div>

      </div>
    </div>
  </main>
</body>
</html>
