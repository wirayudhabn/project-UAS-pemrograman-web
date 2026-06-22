<?php
require_once '../controllers/MahasiswaController.php';
require_once '../middlewares/auth.php';
requireGuest();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // name attributes in html: foto, cv_file
    $result = createMahasiswa($_POST, $_FILES['foto'] ?? [], $_FILES['cv_file'] ?? []);
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
  <title>Registrasi Mahasiswa - InternGo</title>
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
        <img src="../public/images/student_register.png" alt="Student Register" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        
        <!-- Text Overlay -->
        <div class="absolute inset-0 p-12 flex flex-col justify-between">
          <div>
            <span class="text-white text-xs font-semibold tracking-widest uppercase flex items-center gap-4">
              <span class="w-12 h-px bg-white/50 block"></span>
            </span>
          </div>
          <div class="text-white pb-8">
            <h1 class="text-5xl font-bold mb-4 leading-tight">Buka<br>Potensi<br>Penuhmu</h1>
            <p class="text-white/80 text-sm max-w-sm leading-relaxed">
              Bergabunglah dengan komunitas mahasiswa berbakat dan perusahaan inovatif. Masa depanmu dimulai di sini.
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
          <h2 class="text-4xl font-bold mb-3">Buat Akun Mahasiswa</h2>
          <p class="text-gray-500 text-sm">Silakan lengkapi data diri Anda untuk mendaftar</p>
        </div>

        <?php if (!empty($message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4" role="alert">
          <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="POST" enctype="multipart/form-data" id="registerForm" class="space-y-4">
          
          <input type="hidden" name="role" value="mahasiswa">

          <!-- Step Progress Indicator -->
          <div id="stepIndicator" class="flex items-center justify-center space-x-2 mb-6">
            <div class="h-2 w-8 bg-black rounded-full transition-all duration-300" id="dot1"></div>
            <div class="h-2 w-2 bg-gray-200 rounded-full transition-all duration-300" id="dot2"></div>
            <div class="h-2 w-2 bg-gray-200 rounded-full transition-all duration-300" id="dot3"></div>
          </div>

          <!-- STEP 1: General & Academic Data -->
          <div id="step1" class="space-y-4 block animate-fade-in">
            <!-- Full Name -->
            <div>
              <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none" required>
            </div>

            <!-- Email & Password in one row -->
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

            <!-- Academic Data -->
            <div id="academicFields" class="space-y-4 transition-all duration-300">
              <div>
                <label for="universitas" class="block text-sm font-medium text-gray-700 mb-1">Universitas</label>
                <input type="text" id="universitas" name="universitas" placeholder="Masukkan nama universitas Anda" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
              </div>

              <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                  <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                  <input type="text" id="jurusan" name="jurusan" placeholder="Cth: Teknik Informatika" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                </div>
                <div>
                  <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                  <input type="number" id="semester" name="semester" placeholder="1-8" min="1" max="14" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
                </div>
              </div>
            </div>

            <div class="pt-4">
              <button type="button" id="btnNext1" onclick="nextStep(2)" class="w-full bg-black text-white rounded-xl py-3 text-sm font-medium hover:bg-gray-800 transition-colors">
                Selanjutnya
              </button>
            </div>
          </div>

          <!-- STEP 2: Skills Selection -->
          <div id="step2" class="space-y-4 hidden animate-fade-in">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Apa keahlian Anda?</h3>
            <p class="text-sm text-gray-500 mb-4">Pilih keahlian yang Anda miliki. Ini akan membantu perekrut menemukan Anda.</p>
            
            <div class="grid grid-cols-2 gap-3">
              <!-- Skill Checkboxes -->
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="UI/UX Design" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">UI/UX Design</span>
              </label>
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="Web Development" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">Web Dev</span>
              </label>
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="Mobile Development" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">Mobile Dev</span>
              </label>
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="Data Science" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">Data Science</span>
              </label>
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="Digital Marketing" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">Digital Marketing</span>
              </label>
              <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="skill[]" value="Copywriting" class="rounded text-black focus:ring-black border-gray-300 mr-3">
                <span class="text-sm font-medium text-gray-700">Copywriting</span>
              </label>
            </div>

            <!-- Custom Skill Input -->
            <div class="mt-1">
              <input type="text" id="custom_skills" name="custom_skills" placeholder="Keahlian lain? cth: Python, Figma (pisahkan dengan koma)" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-gray-300 focus:bg-white focus:ring-0 text-sm transition-colors outline-none">
            </div>

            <div class="flex gap-4 pt-4">
              <button type="button" onclick="nextStep(1)" class="w-1/3 bg-white border border-gray-200 text-gray-700 rounded-xl py-3 text-sm font-medium hover:bg-gray-50 transition-colors">
                Kembali
              </button>
              <button type="button" onclick="nextStep(3)" class="w-2/3 bg-black text-white rounded-xl py-3 text-sm font-medium hover:bg-gray-800 transition-colors">
                Selanjutnya
              </button>
            </div>
          </div>

          <!-- STEP 3: Uploads -->
          <div id="step3" class="space-y-4 hidden animate-fade-in">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Langkah Terakhir: Unggah File</h3>
            <p class="text-sm text-gray-500 mb-4">Unggah CV dan foto profil Anda.</p>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-gray-400 transition-colors bg-gray-50">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="flex text-sm text-gray-600 justify-center mt-2">
                    <label for="foto" class="relative cursor-pointer bg-transparent rounded-md font-medium text-black hover:underline focus-within:outline-none">
                      <span>Unggah foto</span>
                      <input id="foto" name="foto" type="file" class="sr-only" accept="image/*">
                    </label>
                  </div>
                  <p class="text-xs text-gray-500">PNG, JPG, GIF maksimal 2MB</p>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">CV / Resume (PDF)</label>
              <input type="file" id="cv_file" name="cv_file" accept=".pdf" class="w-full px-4 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:border-gray-300 focus:bg-white focus:ring-0 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-black file:text-white hover:file:bg-gray-800 transition-all cursor-pointer">
            </div>

            <div class="flex gap-4 pt-4">
              <button type="button" onclick="nextStep(2)" class="w-1/3 bg-white border border-gray-200 text-gray-700 rounded-xl py-3 text-sm font-medium hover:bg-gray-50 transition-colors">
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
          if (!nama || !email || !password) {
            alert("Harap isi Nama, Email, dan Password terlebih dahulu.");
            return;
          }
        }
      }

      document.getElementById('step1').classList.add('hidden');
      document.getElementById('step2').classList.add('hidden');
      document.getElementById('step3').classList.add('hidden');

      document.getElementById('step' + step).classList.remove('hidden');
      currentStep = step;
      updateIndicator(step);
    }

    function updateIndicator(step) {
      for (let i = 1; i <= 3; i++) {
        const dot = document.getElementById('dot' + i);
        if (i === step) {
          dot.className = "h-2 w-8 bg-black rounded-full transition-all duration-300";
        } else {
          dot.className = "h-2 w-2 bg-gray-200 rounded-full transition-all duration-300";
        }
      }
    }
  </script>
</body>
</html>
