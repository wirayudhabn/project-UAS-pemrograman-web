<?php
require_once '../middlewares/auth.php';
requireLogin();

require_once '../controllers/LowonganController.php';
require_once '../controllers/LamaranController.php';
require_once '../model/LamaranModel.php';
require_once '../model/MahasiswaModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard_recruiter.php");
    exit;
}

$lowonganId = (int) $_GET['id'];
$job = getDetailLowongan($lowonganId);

if (!$job) {
    die("Lowongan tidak ditemukan.");
}

$logoPath = !empty($job['logo']) ? '../public/' . $job['logo'] : "https://ui-avatars.com/api/?name=" . urlencode($job['nama_perusahaan']) . "&background=0D8ABC&color=fff&rounded=true";

$sudahMelamar = false;
$lamaranModel = new LamaranModel($conn);

if ($_SESSION['role'] === 'mahasiswa') {
    $mahasiswaModel = new MahasiswaModel($conn);
    $mahasiswa = $mahasiswaModel->getMahasiswaByUserId($_SESSION['user_id']);
    if ($mahasiswa) {
        $sudahMelamar = $lamaranModel->cekSudahMelamar($mahasiswa['mahasiswa_id'], $lowonganId);
    }
}

$pelamar = [];
if ($_SESSION['role'] === 'recruiter') {
    $pelamar = getPelamar($lowonganId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($job['judul']) ?> - InternGo</title>
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

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert">
                <p><?= $_SESSION['success_message']; ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                <p><?= $_SESSION['error_message']; ?></p>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- Back Button -->
        <a href="<?= $_SESSION['role'] === 'recruiter' ? 'perusahaan_saya.php' : 'dashboard_mahasiswa.php' ?>" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-black mb-8 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Kembali ke Daftar
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <!-- Main Detail -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Header Card -->
            <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
              <div class="flex flex-col md:flex-row md:items-start gap-6">
                <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" class="w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-gray-100 object-cover shrink-0">
                <div class="flex-1">
                  <div class="flex flex-wrap items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight"><?= htmlspecialchars($job['judul']) ?></h1>
                    <?php if($job['status'] === 'Aktif'): ?>
                      <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold uppercase tracking-wider">Aktif</span>
                    <?php else: ?>
                      <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider">Ditutup</span>
                    <?php endif; ?>
                  </div>
                  <p class="text-lg text-gray-600 mb-6 font-medium"><?= htmlspecialchars($job['nama_perusahaan']) ?></p>
                  
                  <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                      <?= htmlspecialchars($job['lokasi']) ?>
                    </div>
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      <?= htmlspecialchars($job['durasi']) ?>
                    </div>
                    <div class="flex items-center gap-2">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                      <?= $job['kuota'] ?> Posisi
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Job Description -->
            <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
              <h3 class="text-xl font-bold text-gray-900 mb-6">Deskripsi Pekerjaan & Persyaratan</h3>
              <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($job['deskripsi']) ?></div>
            </div>

            <?php if ($_SESSION['role'] === 'recruiter'): ?>
            <!-- Applicants List -->
            <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
              <h3 class="text-xl font-bold text-gray-900 mb-6">Daftar Pelamar (<?= count($pelamar) ?>)</h3>
              
              <?php if (count($pelamar) > 0): ?>
                <div class="space-y-6">
                  <?php foreach ($pelamar as $p): ?>
                    <div class="border rounded-2xl p-6 flex flex-col gap-6">
                      <!-- Top Info Section -->
                      <div class="flex flex-col md:flex-row gap-6 items-start md:items-center justify-between w-full">
                        <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
                          <?php 
                            $fotoPelamar = !empty($p['foto']) ? '../public/' . $p['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($p['nama']) . "&background=random"; 
                          ?>
                          <img src="<?= htmlspecialchars($fotoPelamar) ?>" class="w-16 h-16 rounded-full object-cover">
                          <div>
                            <h4 class="font-bold text-lg text-gray-900"><?= htmlspecialchars($p['nama']) ?></h4>
                            <p class="text-gray-500 text-sm"><?= htmlspecialchars($p['universitas']) ?> - <?= htmlspecialchars($p['jurusan']) ?></p>
                            <p class="text-gray-500 text-sm mt-1">Tanggal Lamar: <?= date('d M Y', strtotime($p['tanggal_lamaran'])) ?></p>
                            
                            <div class="mt-2 text-sm font-semibold">
                               Status: 
                               <?php if ($p['status'] === 'diterima'): ?>
                                 <span class="text-green-600 uppercase">Diterima</span>
                               <?php else: ?>
                                 <span class="text-red-600 uppercase">Ditolak</span>
                               <?php endif; ?>
                            </div>
                          </div>
                        </div>
                        
                        <?php if (!empty($p['cv_file'])): ?>
                          <div class="w-full md:w-auto">
                            <a href="../public/<?= htmlspecialchars($p['cv_file']) ?>" target="_blank" class="block w-full md:w-auto px-6 py-2.5 bg-blue-50 text-blue-600 text-sm font-bold rounded-xl text-center hover:bg-blue-100 transition-colors">Lihat CV</a>
                          </div>
                        <?php endif; ?>
                      </div>
                      
                      <!-- Bottom Action Section (Centered) -->
                      <div class="border-t border-gray-100 pt-6 flex justify-center w-full">
                        <form method="POST" action="update_status_lamaran.php" class="flex flex-col gap-4 w-full max-w-2xl">
                          <input type="hidden" name="id_lamaran" value="<?= $p['id'] ?>">
                          <input type="hidden" name="lowongan_id" value="<?= $lowonganId ?>">
                          <textarea name="pesan" rows="2" placeholder="Pesan untuk pelamar (opsional)..." class="w-full px-4 py-3 text-sm rounded-xl border border-gray-200 focus:border-black outline-none resize-none bg-gray-50 focus:bg-white transition-colors"><?= htmlspecialchars($p['pesan'] ?? '') ?></textarea>
                          <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button type="submit" name="status" value="diterima" <?= $p['status'] === 'diterima' ? 'disabled' : '' ?> class="px-8 py-2.5 <?= $p['status'] === 'diterima' ? 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none' : 'bg-green-600 text-white hover:bg-green-700 shadow-lg shadow-green-600/20' ?> text-sm font-bold rounded-xl text-center flex-1 transition-all">Terima</button>
                            <button type="submit" name="status" value="ditolak" <?= $p['status'] === 'ditolak' ? 'disabled' : '' ?> class="px-8 py-2.5 <?= $p['status'] === 'ditolak' ? 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none' : 'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-600/20' ?> text-sm font-bold rounded-xl text-center flex-1 transition-all">Tolak</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="text-center py-8">
                  <p class="text-gray-500">Belum ada pelamar untuk lowongan ini.</p>
                </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>

          </div>

          <!-- Sidebar Detail -->
          <div class="space-y-8">
            <!-- Apply Card -->
            <div class="bg-black rounded-[2rem] p-8 text-white shadow-xl shadow-black/10 relative overflow-hidden group">
              <!-- Decorative background element -->
              <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl group-hover:bg-white/20 transition-colors"></div>
              
              <h3 class="text-lg font-bold mb-2">Batas Pendaftaran</h3>
              <p class="text-3xl font-light tracking-tight mb-6"><?= date('d M Y', strtotime($job['batas_pendaftaran'])) ?></p>
              
              <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <?php if ($job['status'] !== 'Aktif'): ?>
                  <button class="w-full bg-gray-600 text-white py-3.5 rounded-xl font-bold cursor-not-allowed">
                    Lowongan Ditutup
                  </button>
                <?php elseif ($sudahMelamar): ?>
                  <button class="w-full bg-green-600 text-white py-3.5 rounded-xl font-bold cursor-not-allowed">
                    Sudah Dilamar
                  </button>
                <?php else: ?>
                  <form method="POST" action="lamar_action.php">
                    <input type="hidden" name="lowongan_id" value="<?= $lowonganId ?>">
                    <button type="submit" class="w-full bg-white text-black py-3.5 rounded-xl font-bold hover:bg-gray-100 transition-colors shadow-lg">
                      Lamar Sekarang
                    </button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
              <button class="w-full bg-gray-800 text-white py-3.5 rounded-xl font-medium cursor-not-allowed">
                Hanya Mahasiswa
              </button>
              <?php endif; ?>
            </div>

            <!-- Company Info Card -->
            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
              <h3 class="text-lg font-bold text-gray-900 mb-6">Tentang Perusahaan</h3>
              <div class="space-y-4">
                <?php if(!empty($job['industri'])): ?>
                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Industri</p>
                  <p class="text-sm text-gray-900 font-medium"><?= htmlspecialchars($job['industri']) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($job['website'])): ?>
                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Situs Web</p>
                  <a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" class="text-sm text-blue-600 font-medium hover:underline break-all"><?= htmlspecialchars($job['website']) ?></a>
                </div>
                <?php endif; ?>
                
                <div>
                  <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Diterbitkan Pada</p>
                  <p class="text-sm text-gray-900 font-medium"><?= date('d F Y', strtotime($job['created_at'])) ?></p>
                </div>
              </div>
            </div>
          </div>
          
        </div>

      </div>
    </div>
  </main>
</body>
</html>
