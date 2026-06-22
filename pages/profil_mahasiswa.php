<?php
require_once '../middlewares/auth.php';
requireRole('mahasiswa');

require_once '../controllers/MahasiswaController.php';

$userId = $_SESSION['user_id'];
$message = '';
$status = '';

// Handle POST request untuk update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateProfileMahasiswa($userId, $_POST, $_FILES['foto'] ?? [], $_FILES['cv_file'] ?? []);
    $message = $result['message'];
    $status = $result['status'];
}

// Mengambil data mahasiswa terbaru dari database
$mahasiswa = getMahasiswa($userId);

// Mengolah string skill menjadi array agar mudah dirender
$skills = !empty($mahasiswa['skill']) ? array_map('trim', explode(',', $mahasiswa['skill'])) : [];
$cvPath = !empty($mahasiswa['cv_file']) ? '../public/' . $mahasiswa['cv_file'] : '';
$fotoPath = !empty($mahasiswa['foto']) ? '../public/' . $mahasiswa['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($mahasiswa['nama']) . "&background=0D8ABC&color=fff&rounded=true";

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
          <h2 class="text-3xl font-serif mb-2 text-gray-900 tracking-tight">Profil Mahasiswa</h2>
          <p class="text-gray-500">Kelola informasi pribadi, pendidikan, dan keahlianmu di sini.</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="<?= $status === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-xl mb-8" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Container (Glassmorphism / Premium Card) -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
          <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row">
            
            <!-- Left Side: Avatar & Summary -->
            <div class="w-full lg:w-1/3 bg-gray-50 p-8 lg:p-12 border-b lg:border-b-0 lg:border-r border-gray-100 flex flex-col items-center text-center">
              
              <!-- Editable Avatar -->
              <div class="relative group cursor-pointer mb-6">
                <img id="avatarPreview" src="<?= htmlspecialchars($fotoPath) ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover transition-transform group-hover:scale-105">
                <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <!-- Input file overlay -->
                <input type="file" name="foto" id="fotoInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($mahasiswa['nama'] ?? '') ?></h3>
              <p class="text-sm text-gray-500 mb-4"><?= htmlspecialchars($mahasiswa['universitas'] ?? 'Belum ada universitas') ?></p>
              
              <?php if (!empty($cvPath)): ?>
                <a href="<?= htmlspecialchars($cvPath) ?>" target="_blank" class="w-full bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors mb-4 shadow-sm block text-center">
                  Buka CV di Tab Baru
                </a>
              <?php endif; ?>
              
              <div class="mt-2 w-full text-left">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Upload CV Baru</label>
                <input type="file" name="cv_file" id="cvInput" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-white file:text-gray-700 file:border file:border-gray-200 hover:file:bg-gray-50 transition-colors">
              </div>

              <!-- CV Preview Container -->
              <?php 
                $isPdf = !empty($cvPath) && strtolower(pathinfo($cvPath, PATHINFO_EXTENSION)) === 'pdf';
              ?>
              <div id="cvPreviewContainer" class="<?= $isPdf ? 'block' : 'hidden' ?> mt-20 w-full h-96 border border-gray-200 rounded-xl overflow-hidden bg-gray-50 shadow-inner">
                <div class="bg-gray-100 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                  <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Preview CV</span>
                </div>
                <iframe id="cvPreview" src="<?= $isPdf ? htmlspecialchars($cvPath) : '' ?>" class="w-full h-[calc(100%-37px)]" frameborder="0"></iframe>
              </div>
              <p id="cvNotPdfMessage" class="hidden mt-4 text-sm text-yellow-600 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                Preview hanya tersedia untuk file PDF. File Doc/Docx tidak dapat dipreview secara langsung.
              </p>
            </div>

            <!-- Right Side: Form Details -->
            <div class="w-full lg:w-2/3 p-8 lg:p-12">
              <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Informasi Dasar</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                  <input type="text" name="nama" value="<?= htmlspecialchars($mahasiswa['nama'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input type="email" name="email" value="<?= htmlspecialchars($mahasiswa['email'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
                </div>
              </div>

              <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Pendidikan & Keahlian</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Universitas</label>
                  <input type="text" name="universitas" value="<?= htmlspecialchars($mahasiswa['universitas'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <input type="text" name="jurusan" value="<?= htmlspecialchars($mahasiswa['jurusan'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <input type="number" name="semester" value="<?= htmlspecialchars($mahasiswa['semester'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                  </div>
                </div>
              </div>

              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keahlian (Skills)</label>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                  <div class="flex flex-wrap gap-2 mb-3">
                    <?php 
                    $predefinedSkills = ['PHP', 'JavaScript', 'Python', 'Java', 'UI/UX Design', 'Data Analysis', 'Marketing', 'Figma', 'React'];
                    foreach($predefinedSkills as $s): 
                        $isChecked = in_array($s, $skills);
                    ?>
                    <label class="cursor-pointer">
                      <input type="checkbox" name="skill[]" value="<?= $s ?>" class="peer hidden" <?= $isChecked ? 'checked' : '' ?>>
                      <span class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 bg-white text-gray-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-colors block">
                        <?= $s ?>
                      </span>
                    </label>
                    <?php endforeach; ?>
                  </div>
                  <!-- Input custom skill -->
                  <input type="text" name="custom_skills" placeholder="Keahlian lain? (pisahkan dengan koma)" value="<?= htmlspecialchars(implode(', ', array_diff($skills, $predefinedSkills))) ?>" class="w-full px-4 py-2.5 rounded-lg bg-white border border-gray-200 focus:border-gray-300 focus:ring-0 text-sm transition-colors outline-none">
                </div>
              </div>

              <!-- Submit -->
              <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl font-medium hover:bg-gray-800 transition-colors shadow-lg shadow-black/20">
                  Simpan Perubahan
                </button>
              </div>
            </div>

          </form>
        </div>

      </div>
    </div>
  </main>
  
  <script>
    document.getElementById('fotoInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    document.getElementById('cvInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const previewContainer = document.getElementById('cvPreviewContainer');
      const previewIframe = document.getElementById('cvPreview');
      const notPdfMessage = document.getElementById('cvNotPdfMessage');

      if (file) {
        if (file.type === 'application/pdf') {
          const fileURL = URL.createObjectURL(file);
          previewIframe.src = fileURL;
          previewContainer.classList.remove('hidden');
          previewContainer.classList.add('block');
          notPdfMessage.classList.add('hidden');
        } else {
          // It's a doc or docx, we can't preview it directly in browser iframe easily
          previewContainer.classList.remove('block');
          previewContainer.classList.add('hidden');
          previewIframe.src = '';
          notPdfMessage.classList.remove('hidden');
        }
      } else {
        // Reset if no file selected (optional: could revert to original)
        // If there was an original PDF, it's safer to just hide the preview if they cancel
      }
    });
  </script>
</body>
</html>
