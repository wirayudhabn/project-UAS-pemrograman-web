<?php
require_once '../middlewares/auth.php';
requireRole('mahasiswa');

require_once '../controllers/LowonganController.php';
require_once '../controllers/LamaranController.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$paginationResult = getActiveLowongan($page, 6, $search);
$activeJobs = $paginationResult['data'];
$totalPages = $paginationResult['total_pages'];
$currentPage = $paginationResult['current_page'];

$appliedJobs = getRiwayatLamaran($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Mahasiswa - InternGo</title>
  <link rel="stylesheet" href="../public/css/output.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-[#FAFAFA] text-gray-800 overflow-hidden flex h-screen">

  <!-- Sidebar Component -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content Wrapper -->
  <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
    
    <!-- Navbar Component -->
    <?php include '../components/navbar.php'; ?>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto p-8 lg:p-12 scroll-smooth">
      <div class="max-w-7xl mx-auto space-y-16">
        
        <!-- SECTION 1: AVAILABLE JOBS -->
        <div>
          <!-- Header Section -->
          <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
              <h2 class="text-3xl font-bold mb-2 text-gray-900 tracking-tight">Lowongan Tersedia</h2>
              <p class="text-gray-500">Pekerjaan terbaik yang dikurasi sesuai dengan profil dan keahlianmu.</p>
            </div>
            <div class="flex items-center gap-3">
              <!-- Search Form -->
              <form method="GET" action="" class="relative">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari posisi atau perusahaan..." class="pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:border-black focus:ring-0 outline-none transition-all w-full sm:w-72 shadow-sm hover:border-gray-300">
                <button type="submit" class="hidden"></button>
              </form>
            </div>
          </div>

          <!-- Job Cards Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php 
            if(count($activeJobs) > 0) {
                foreach($activeJobs as $row) {
                    $job = [
                        'id' => $row['id'],
                        'company_name' => $row['nama_perusahaan'],
                        'job_title' => $row['judul'],
                        'location' => $row['lokasi'],
                        'type' => $row['durasi'],
                        'salary' => 'Kuota: ' . $row['kuota'],
                        'description' => $row['deskripsi'],
                        'logo_url' => !empty($row['logo']) ? '../public/' . $row['logo'] : null
                    ];
                    include '../components/job_card.php';
                }
            } else {
                echo "<p class='col-span-full text-center py-12 text-gray-500'>Tidak ada lowongan yang ditemukan.</p>";
            }
            ?>
          </div>

          <!-- Pagination UI -->
          <?php if ($totalPages > 1): ?>
          <div class="mt-12 flex justify-center">
            <nav class="inline-flex items-center gap-1 bg-white p-1 rounded-xl border border-gray-200 shadow-sm">
              <!-- Prev Button -->
              <a href="?page=<?= max(1, $currentPage - 1) ?>&search=<?= urlencode($search) ?>" class="p-2 rounded-lg <?= $currentPage <= 1 ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-50 hover:text-black transition-colors' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              </a>
              
              <!-- Page Numbers -->
              <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i === $currentPage ? 'bg-black text-white shadow-md shadow-black/10' : 'text-gray-600 hover:bg-gray-50 hover:text-black' ?>">
                  <?= $i ?>
                </a>
              <?php endfor; ?>
              
              <!-- Next Button -->
              <a href="?page=<?= min($totalPages, $currentPage + 1) ?>&search=<?= urlencode($search) ?>" class="p-2 rounded-lg <?= $currentPage >= $totalPages ? 'text-gray-300 pointer-events-none' : 'text-gray-500 hover:bg-gray-50 hover:text-black transition-colors' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </a>
            </nav>
          </div>
          <?php endif; ?>
        </div>

        <!-- SECTION 2: APPLIED JOBS -->
        <div class="mt-10 border-t border-gray-200">
          <div class="mb-10 mt-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
              <h2 class="text-3xl font-bold mb-2 text-gray-900 tracking-tight">Lamaran Saya</h2>
              <p class="text-gray-500">Daftar lowongan yang sudah kamu lamar beserta statusnya.</p>
            </div>
            <a href="riwayat_lamaran.php" class="text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">Lihat Semua Riwayat &rarr;</a>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php 
            if(count($appliedJobs) > 0) {
                // Show only up to 3 most recent applied jobs for the dashboard view
                $recentApplied = array_slice($appliedJobs, 0, 3);
                foreach($recentApplied as $item) {
            ?>
                <div class="bg-white rounded-[2rem] border border-gray-100 p-6 sm:p-8 hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 flex flex-col relative group">
                  <div class="flex items-start justify-between gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100 overflow-hidden shrink-0">
                      <?php if (!empty($item['logo'])): ?>
                        <img src="../public/<?= htmlspecialchars($item['logo']) ?>" class="w-full h-full object-cover">
                      <?php else: ?>
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                      <?php endif; ?>
                    </div>
                    <?php if ($item['status'] === 'pending'): ?>
                      <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Pending</span>
                    <?php elseif ($item['status'] === 'diterima'): ?>
                      <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Diterima</span>
                    <?php else: ?>
                      <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Ditolak</span>
                    <?php endif; ?>
                  </div>
                  
                  <div class="mb-6 flex-1">
                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1"><?= htmlspecialchars($item['judul']) ?></h3>
                    <p class="text-gray-500 text-sm"><?= htmlspecialchars($item['nama_perusahaan']) ?></p>
                  </div>
                  
                  <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                    <div class="flex items-center gap-1.5">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      <?= date('d M Y', strtotime($item['tanggal_lamaran'])) ?>
                    </div>
                  </div>

                  <a href="detail_lowongan.php?id=<?= $item['lowongan_id'] ?>" class="block w-full py-2.5 px-4 text-center text-sm font-semibold text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors mt-auto">
                    Lihat Detail
                  </a>
                </div>
            <?php
                }
            } else {
                echo "<p class='col-span-full text-center py-8 text-gray-500'>Kamu belum pernah melamar pekerjaan.</p>";
            }
            ?>
          </div>
        </div>

      </div>
    </div>
  </main>

</body>
</html>
