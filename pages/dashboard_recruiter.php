<?php
require_once '../middlewares/auth.php';
requireRole('recruiter');

require_once '../controllers/LowonganController.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$paginationResult = getLowonganRecruiter($_SESSION['user_id'], $page);
$lowonganList = $paginationResult['data'];
$totalPages = $paginationResult['total_pages'];
$currentPage = $paginationResult['current_page'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Recruiter - InternGo</title>
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
      <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
          <div>
            <h2 class="text-3xl font-bold mb-2 text-gray-900 tracking-tight">Lowongan Anda</h2>
            <p class="text-gray-500">Kelola semua lowongan pekerjaan dan pantau pelamar di sini.</p>
          </div>
          
          <div class="flex items-center gap-4">
            <!-- Search Bar placeholder if needed, otherwise empty -->
          </div>
          <a href="buat_lowongan.php" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3.5 rounded-xl font-bold hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 active:scale-95 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Lowongan Baru
          </a>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="<?= $_SESSION['flash_status'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-xl mb-8 flex items-center justify-between" role="alert">
          <span class="block sm:inline font-medium"><?= htmlspecialchars($_SESSION['flash_message']) ?></span>
          <button onclick="this.parentElement.remove()" class="text-current opacity-50 hover:opacity-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <?php 
          unset($_SESSION['flash_message']);
          unset($_SESSION['flash_status']);
        endif; ?>

        <!-- Job Cards List (Full-width) -->
        <div class="flex flex-col gap-4">
          <?php if (empty($lowonganList)): ?>
          <div class="text-center py-12 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p class="text-gray-500 font-medium">Belum ada lowongan yang diterbitkan.</p>
          </div>
          <?php else: ?>
          <?php foreach($lowonganList as $job): ?>
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all flex flex-col md:flex-row md:items-center justify-between gap-6 group">
            
            <!-- Job Info -->
            <div class="flex items-center gap-6">
              <div class="w-14 h-14 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:border-gray-200 transition-colors shrink-0">
                <svg class="w-7 h-7 text-gray-400 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <div>
                <div class="flex items-center gap-3 mb-1">
                  <h3 class="text-lg font-bold text-gray-900 group-hover:text-black transition-colors"><?= htmlspecialchars($job['judul']) ?></h3>
                  <?php if($job['status'] === 'Aktif'): ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Aktif</span>
                  <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">Ditutup</span>
                  <?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-500">
                  <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> <?= htmlspecialchars($job['durasi']) ?></span>
                  <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> <?= htmlspecialchars($job['lokasi']) ?></span>
                  <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Dibuat: <?= date('d M Y', strtotime($job['created_at'])) ?></span>
                </div>
              </div>
            </div>

            <!-- Stats & Action -->
            <div class="flex items-center justify-between md:justify-end gap-6 md:gap-8 pt-4 md:pt-0 border-t md:border-0 border-gray-100 w-full md:w-auto">
              <!-- Pelamar Stat -->
              <div class="text-center">
                <p class="text-2xl font-bold text-gray-900"><?= $job['jumlah_pelamar'] ?? 0 ?></p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pelamar</p>
              </div>
              
              <!-- Actions -->
              <div class="flex items-center gap-2">
                <a href="hapus_lowongan.php?id=<?= $job['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus lowongan ini? Tindakan ini tidak dapat dibatalkan.');" class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Lowongan">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </a>
                <a href="edit_lowongan.php?id=<?= $job['id'] ?>" class="p-2.5 text-gray-400 hover:text-black hover:bg-gray-50 rounded-xl transition-all" title="Edit Lowongan">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </a>
                <a href="detail_lowongan.php?id=<?= $job['id'] ?>" class="bg-black text-white px-4 py-2.5 text-sm font-medium rounded-xl hover:bg-gray-800 transition-colors shadow-md shadow-black/10">
                  Lihat Detail
                </a>
              </div>
            </div>

          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Pagination UI -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-12 flex justify-center">
          <nav class="inline-flex items-center gap-1 bg-white p-1 rounded-xl border border-gray-200 shadow-sm">
            <!-- Prev Button -->
            <a href="?page=<?= max(1, $currentPage - 1) ?>" class="p-2 rounded-lg <?= $currentPage <= 1 ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-50 hover:text-black transition-colors' ?>">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            
            <!-- Page Numbers -->
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
              <a href="?page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i === $currentPage ? 'bg-black text-white shadow-md shadow-black/10' : 'text-gray-600 hover:bg-gray-50 hover:text-black' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
            
            <!-- Next Button -->
            <a href="?page=<?= min($totalPages, $currentPage + 1) ?>" class="p-2 rounded-lg <?= $currentPage >= $totalPages ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-50 hover:text-black transition-colors' ?>">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
          </nav>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </main>
</body>
</html>
