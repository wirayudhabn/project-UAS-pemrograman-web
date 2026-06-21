<?php
require_once '../controllers/CompanyController.php';
require_once '../controllers/RecruiterController.php';
require_once '../middlewares/auth.php';
requireGuest();

// Panggil fungsi dari controller
$perusahaanList = getAllCompanies();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createRecruiter($_POST, $_FILES['logo'] ?? []);
    if ($result['status'] === 'success') {
        echo "<script>alert('{$result['message']}'); window.location.href='login.php';</script>";
        exit;
    } else {
        $message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Recruiter - InternGo</title>
  <link rel="stylesheet" href="../public/css/output.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-white font-sans text-gray-800">
  <div class="flex h-screen w-full">
    
    <!-- Left Side (Image & Quote) -->
    <div class="hidden lg:flex w-1/2 p-4">
      <div class="relative w-full h-full rounded-[2rem] overflow-hidden">
        <img src="../public/images/student_register.png" alt="Recruiter Register" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        
        <!-- Text Overlay -->
        <div class="absolute inset-0 p-12 flex flex-col justify-between">
          <div>
            <span class="text-white text-xs font-semibold tracking-widest uppercase flex items-center gap-4">
              <span class="w-12 h-px bg-white/50 block"></span>
            </span>
          </div>
          <div class="text-white pb-8">
            <h1 class="text-5xl font-bold mb-4 leading-tight">Temukan<br>Kandidat<br>Terbaik</h1>
            <p class="text-white/80 text-sm max-w-sm leading-relaxed">
              Bergabunglah dengan komunitas kami dan temukan mahasiswa berbakat yang siap berinovasi di perusahaan Anda.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side (Form) -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-24 relative overflow-y-auto">
      
      <!-- Logo -->
      <div class="absolute top-8 lg:top-12 flex items-center gap-2 font-bold text-xl">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <span>InternGo</span>
      </div>

      <div class="w-full max-w-md mt-16 lg:mt-0">
        <!-- Header -->
        <div class="text-center mb-8">
          <h2 class="text-4xl font-bold mb-3">Buat Akun Recruiter</h2>
          <p class="text-gray-500 text-sm">Silakan lengkapi data diri Anda untuk mendaftar</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="POST" enctype="multipart/form-data" id="registerForm" class="space-y-4">
          
          <input type="hidden" name="role" value="recruiter">

          <!-- Step Progress Indicator -->
          <div id="stepIndicator" class="flex items-center justify-center space-x-2 mb-6">
            <div class="h-2 w-8 bg-black rounded-full transition-all duration-300" id="dot1"></div>
            <div class="h-2 w-2 bg-gray-200 rounded-full transition-all duration-300" id="dot2"></div>
          </div>

          <!-- STEP 1: Profil Recruiter -->
          <div id="step1" class="space-y-4 block animate-fade-in">
            <div>
              <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" placeholder="Alamat email" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                <input type="text" id="jabatan" name="jabatan" placeholder="Cth: HR Manager" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
              <div>
                <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" id="no_telp" name="no_telp" placeholder="08xxxxxxxx" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
              </div>
            </div>

            <div class="pt-4">
              <button type="button" onclick="nextStep(2)" class="w-full bg-black text-white rounded-xl py-3 text-sm font-medium hover:bg-gray-800 transition-colors">
                Selanjutnya
              </button>
            </div>
          </div>

          <!-- STEP 2: Company Affiliation -->
          <div id="step2" class="space-y-4 hidden animate-fade-in">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Afiliasi Perusahaan</h3>
            <p class="text-sm text-gray-500 mb-4">Pilih perusahaan Anda, atau daftarkan perusahaan baru jika belum terdaftar.</p>
            
            <!-- Existing Company Select -->
            <div id="existingCompanyDiv" class="transition-opacity duration-300">
              <label for="perusahaan_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Perusahaan</label>
              <select id="perusahaan_id" name="perusahaan_id" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                <option value="">-- Pilih perusahaan --</option>
                <?php foreach($perusahaanList as $p): ?>
                  <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_perusahaan']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Toggle New Company -->
            <label class="flex items-center mt-3 cursor-pointer">
              <input type="checkbox" id="is_new_company" name="is_new_company" value="1" onchange="toggleNewCompany()" class="rounded text-black focus:ring-black border-gray-300 mr-2">
              <span class="text-sm font-medium text-gray-700">Perusahaan saya belum terdaftar di sini</span>
            </label>

            <!-- New Company Form (Hidden by default) -->
            <div id="newCompanyForm" class="hidden space-y-4 pt-4 border-t border-gray-200">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" placeholder="PT XYZ Technology" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Industri</label>
                  <input type="text" name="industri" placeholder="Cth: Teknologi" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Situs Web</label>
                  <input type="url" name="website" placeholder="https://..." class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none"></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none"></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Perusahaan</label>
                <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:border-gray-300 focus:bg-white focus:ring-0 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-black file:text-white hover:file:bg-gray-800 transition-all cursor-pointer">
              </div>
            </div>

            <div class="flex gap-4 pt-4">
              <button type="button" onclick="nextStep(1)" class="w-1/3 bg-white border border-gray-200 text-gray-700 rounded-xl py-3 text-sm font-medium hover:bg-gray-50 transition-colors">
                Kembali
              </button>
              <button type="submit" class="w-2/3 bg-black text-white rounded-xl py-3 text-sm font-medium hover:bg-gray-800 transition-colors">
                Selesaikan Pendaftaran
              </button>
            </div>
          </div>
          
        </form>

        <!-- Sign In Link -->
        <p class="text-center text-sm text-gray-500 mt-8">
          Sudah punya akun? <a href="login.php" class="font-medium text-black hover:underline">Masuk</a>
        </p>

      </div>
    </div>
  </div>

  <style>
    .animate-fade-in {
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>

  <script>
    let currentStep = 1;

    function nextStep(step) {
      if (step > currentStep) {
        if (currentStep === 1) {
          const nama = document.getElementById('nama').value;
          const email = document.getElementById('email').value;
          const password = document.getElementById('password').value;
          const jabatan = document.getElementById('jabatan').value;
          const no_telp = document.getElementById('no_telp').value;
          
          if (!nama || !email || !password || !jabatan || !no_telp) {
            alert("Harap isi semua field di Step 1 terlebih dahulu.");
            return;
          }
        }
      }

      document.getElementById('step1').classList.add('hidden');
      document.getElementById('step2').classList.add('hidden');

      document.getElementById('step' + step).classList.remove('hidden');
      currentStep = step;
      updateIndicator(step);
    }

    function updateIndicator(step) {
      for (let i = 1; i <= 2; i++) {
        const dot = document.getElementById('dot' + i);
        if (i === step) {
          dot.className = "h-2 w-8 bg-black rounded-full transition-all duration-300";
        } else {
          dot.className = "h-2 w-2 bg-gray-200 rounded-full transition-all duration-300";
        }
      }
    }

    function toggleNewCompany() {
      const isNew = document.getElementById('is_new_company').checked;
      const existingDiv = document.getElementById('existingCompanyDiv');
      const newForm = document.getElementById('newCompanyForm');
      const selectBox = document.getElementById('perusahaan_id');
      const namaPerusahaan = document.getElementById('nama_perusahaan');

      if (isNew) {
        // Tampilkan form baru, matikan select
        newForm.classList.remove('hidden');
        selectBox.value = '';
        selectBox.disabled = true;
        existingDiv.classList.add('opacity-50', 'pointer-events-none');
        namaPerusahaan.required = true;
      } else {
        // Sembunyikan form baru, nyalakan select
        newForm.classList.add('hidden');
        selectBox.disabled = false;
        existingDiv.classList.remove('opacity-50', 'pointer-events-none');
        namaPerusahaan.required = false;
      }
    }
  </script>
</body>
</html>
