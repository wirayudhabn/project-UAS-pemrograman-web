<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0" onclick="toggleSidebar()"></div>

<aside id="main-sidebar" class="w-64 bg-white border-r border-gray-100 flex flex-col h-full shadow-[4px_0_24px_rgba(0,0,0,0.02)] fixed md:static inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0">
  <!-- Logo -->
  <div class="h-20 flex items-center justify-between px-8 border-b border-gray-50">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      </div>
      <span class="font-bold text-xl tracking-tight">InternGo</span>
    </div>
    <!-- Close button for mobile -->
    <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-black">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 mt-4">Menu</p>
    
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    $dashboardLink = (isset($_SESSION['role']) && $_SESSION['role'] === 'recruiter') ? 'dashboard_recruiter.php' : 'dashboard_mahasiswa.php';
    $isDashboardActive = ($currentPage === 'dashboard_mahasiswa.php' || $currentPage === 'dashboard_recruiter.php');
    $dashboardClasses = $isDashboardActive 
        ? 'bg-black text-white shadow-md shadow-black/10' 
        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50';
    ?>
    <a href="<?= htmlspecialchars($dashboardLink) ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all <?= $dashboardClasses ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
      Lowongan Pekerjaan
    </a>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa'): ?>
    <?php
    $isRiwayatActive = ($currentPage === 'riwayat_lamaran.php');
    $riwayatClasses = $isRiwayatActive 
        ? 'bg-black text-white shadow-md shadow-black/10' 
        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50';
    ?>
    <a href="riwayat_lamaran.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all <?= $riwayatClasses ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      Lamaran Saya
    </a>
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'recruiter'): ?>
    <?php
    $isPerusahaanActive = ($currentPage === 'perusahaan_saya.php');
    $perusahaanClasses = $isPerusahaanActive 
        ? 'bg-black text-white shadow-md shadow-black/10' 
        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50';
    ?>
    <a href="perusahaan_saya.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all <?= $perusahaanClasses ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
      Perusahaan Saya
    </a>
    <?php endif; ?>
    
    <?php
    $profileLink = '#';
    if (isset($_SESSION['role'])) {
        $profileLink = $_SESSION['role'] === 'mahasiswa' ? 'profil_mahasiswa.php' : 'profil_recruiter.php';
    }
    $isProfileActive = ($currentPage === 'profil_mahasiswa.php' || $currentPage === 'profil_recruiter.php');
    $profileClasses = $isProfileActive 
        ? 'bg-black text-white shadow-md shadow-black/10' 
        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50';
    ?>
    <a href="<?= htmlspecialchars($profileLink) ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all <?= $profileClasses ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
      Profil Saya
    </a>
  </nav>

  <!-- Bottom / Logout -->
  <div class="p-4 border-t border-gray-50">
    <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-red-500 hover:bg-red-50 transition-all">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
      Log Out
    </a>
  </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('main-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    // Toggle translate class
    if (sidebar.classList.contains('-translate-x-full')) {
        // Open
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            overlay.classList.add('opacity-100');
        }, 10);
    } else {
        // Close
        sidebar.classList.add('-translate-x-full');
        overlay.classList.remove('opacity-100');
        overlay.classList.add('opacity-0');
        // Wait for transition to finish before hiding
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300);
    }
}
</script>
