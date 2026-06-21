<?php
// Menerima variabel $job yang berisi data pekerjaan. Jika tidak ada, gunakan default.
$companyName = $job['company_name'] ?? 'Tech Corp';
$jobTitle = $job['job_title'] ?? 'Software Engineer Intern';
$location = $job['location'] ?? 'Jakarta, Indonesia';
$type = $job['type'] ?? 'Full-time';
$salary = $job['salary'] ?? 'Rp 3.000.000 - Rp 5.000.000';
$description = $job['description'] ?? 'Kami mencari intern yang bersemangat untuk belajar dan berkembang bersama tim kami.';
$logoUrl = $job['logo_url'] ?? "https://ui-avatars.com/api/?name=" . urlencode($companyName) . "&background=random";
?>
<div class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-gray-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">
  <div class="flex items-start justify-between mb-4">
    <div class="flex items-center gap-4">
      <div class="w-14 h-14 rounded-xl border border-gray-100 overflow-hidden bg-gray-50 flex items-center justify-center p-2 shrink-0">
        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" class="w-full h-full object-contain mix-blend-multiply">
      </div>
      <div>
        <h3 class="font-bold text-lg text-gray-900 group-hover:text-black transition-colors leading-tight mb-1"><?= htmlspecialchars($jobTitle) ?></h3>
        <p class="text-sm text-gray-500 font-medium"><?= htmlspecialchars($companyName) ?></p>
      </div>
    </div>
    <!-- Bookmark Button -->
    <button class="text-gray-300 hover:text-black transition-colors shrink-0">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
    </button>
  </div>
  
  <div class="flex flex-wrap gap-2 mb-4">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 tracking-wide uppercase">
      <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      <?= htmlspecialchars($location) ?>
    </span>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 tracking-wide uppercase">
      <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
      <?= htmlspecialchars($type) ?>
    </span>
  </div>
  
  <p class="text-sm text-gray-600 line-clamp-2 mb-6 leading-relaxed flex-1">
    <?= htmlspecialchars($description) ?>
  </p>
  
  <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
    <span class="text-sm font-bold text-gray-900"><?= htmlspecialchars($salary) ?></span>
    <?php if(isset($job['id'])): ?>
    <a href="detail_lowongan.php?id=<?= $job['id'] ?>" class="px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-black/10 transition-all active:scale-95 inline-block text-center">
      Detail
    </a>
    <?php else: ?>
    <button class="px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-xl hover:bg-gray-800 hover:shadow-lg hover:shadow-black/10 transition-all active:scale-95">
      Detail
    </button>
    <?php endif; ?>
  </div>
</div>
