<?php
require_once '../middlewares/auth.php';
requireLogin();

if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: dashboard_recruiter.php");
    exit;
}

require_once '../controllers/LamaranController.php';
$riwayat = getRiwayatLamaran($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Lamaran - InternGo</title>
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
      <div class="max-w-5xl mx-auto">
        <div class="mb-10">
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Riwayat Lamaran Saya</h1>
          <p class="text-gray-500 mt-2 text-lg">Pantau status lamaran magang yang telah Anda ajukan</p>
        </div>

        <?php if (count($riwayat) > 0): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($riwayat as $item): ?>
              <?php 
                $logoPath = !empty($item['logo']) ? '../public/' . $item['logo'] : "https://ui-avatars.com/api/?name=" . urlencode($item['nama_perusahaan']) . "&background=0D8ABC&color=fff&rounded=true";
              ?>
              <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300">
                <div class="flex items-start gap-4 mb-4">
                  <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" class="w-14 h-14 rounded-xl border border-gray-100 object-cover shrink-0">
                  <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-1"><?= htmlspecialchars($item['judul']) ?></h3>
                    <p class="text-gray-500 text-sm"><?= htmlspecialchars($item['nama_perusahaan']) ?></p>
                  </div>
                  <?php if ($item['status'] === 'pending'): ?>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Pending</span>
                  <?php elseif ($item['status'] === 'diterima'): ?>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Diterima</span>
                  <?php else: ?>
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider rounded-full shrink-0">Ditolak</span>
                  <?php endif; ?>
                </div>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <?= htmlspecialchars($item['lokasi']) ?>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Dilamar pada: <?= date('d M Y', strtotime($item['tanggal_lamaran'])) ?>
                  </div>
                </div>

                <?php if ($item['status'] !== 'pending' && !empty($item['pesan'])): ?>
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100 text-sm">
                  <span class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Pesan dari Recruiter:</span>
                  <p class="text-gray-700"><?= nl2br(htmlspecialchars($item['pesan'])) ?></p>
                </div>
                <?php endif; ?>

                <a href="detail_lowongan.php?id=<?= $item['lowongan_id'] ?>" class="block w-full py-2.5 px-4 text-center text-sm font-semibold text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                  Lihat Detail Lowongan
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-20 px-6 sm:px-8 bg-white rounded-[2rem] border border-gray-100">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada lamaran</h3>
            <p class="text-gray-500 mb-6">Anda belum pernah mengajukan lamaran untuk lowongan apapun.</p>
            <a href="dashboard_mahasiswa.php" class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white rounded-xl font-medium hover:bg-gray-800 transition-colors">
              Cari Lowongan Magang
            </a>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </main>
</body>
</html>
