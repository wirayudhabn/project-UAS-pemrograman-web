<?php
$userName = $_SESSION['nama'] ?? 'User';
$roleName = ucfirst($_SESSION['role'] ?? 'Mahasiswa');
$profileImg = "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=0D8ABC&color=fff&rounded=true";

if (isset($_SESSION['user_id'])) {
    if (!isset($conn)) {
        require_once __DIR__ . '/../config/koneksi.php';
    }
    
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa') {
        $stmtImg = $conn->prepare("SELECT foto FROM mahasiswa WHERE user_id = ?");
        $stmtImg->bind_param("i", $_SESSION['user_id']);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        if ($resImg->num_rows > 0) {
            $rowImg = $resImg->fetch_assoc();
            if (!empty($rowImg['foto'])) {
                $profileImg = '../public/' . $rowImg['foto'];
            }
        }
    } else if (isset($_SESSION['role']) && $_SESSION['role'] === 'recruiter') {
        $stmtImg = $conn->prepare("SELECT p.logo FROM perusahaan p JOIN recruiter r ON p.id = r.perusahaan_id WHERE r.user_id = ?");
        $stmtImg->bind_param("i", $_SESSION['user_id']);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        if ($resImg->num_rows > 0) {
            $rowImg = $resImg->fetch_assoc();
            if (!empty($rowImg['logo'])) {
                $profileImg = '../public/' . $rowImg['logo'];
            }
        }
    }
}
?>
<header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 md:px-8 sticky top-0 z-20 shrink-0">
  <div class="flex items-center gap-4">
    <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>
    <h1 class="text-xl md:text-2xl font-serif text-gray-900 tracking-tight hidden sm:block">Temukan Karir Impianmu</h1>
  </div>
  <div class="flex items-center gap-6">
    <!-- Profile -->
    <div class="flex items-center gap-3">
      <div class="text-right hidden sm:block">
        <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($userName) ?></p>
        <p class="text-xs text-gray-500"><?= htmlspecialchars($roleName) ?></p>
      </div>
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] !== 'recruiter'): ?>
      <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-100 shadow-sm object-cover">
      <?php endif; ?>
    </div>
  </div>
</header>
