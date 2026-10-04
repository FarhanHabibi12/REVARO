<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Biodata Akun — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0B4A8F', // Deep Blue
                        secondary: '#FF7F00', // Vibrant Orange
                        accent: '#E6EFFF', // Light Blue Accent
                        textDark: '#1E293B',
                        textLight: '#64748B',
                        brandBlue: '#0B4A8F',
                        brandOrange: '#FF7F00',
                    },
                    boxShadow: {
                        'form': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                        'btn': '4px 4px 0px 0px rgba(11, 74, 143, 1)',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .revaro-card {
            background-color: #ffffff;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #0B4A8F;
            border-radius: 1.25rem;
        }
        .revaro-btn {
            background-color: #0B4A8F;
            color: white;
            border: 2px solid #0B4A8F;
            box-shadow: 4px 4px 0px 0px #FF7F00;
            transition: all 0.15s ease;
        }
        .revaro-btn:hover {
            box-shadow: 2px 2px 0px 0px #FF7F00;
            transform: translate(2px, 2px);
        }
        .revaro-input {
            width: 100%;
            border: 2px solid #0B4A8F;
            box-shadow: 2px 2px 0px 0px #0B4A8F;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #1E293B;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .revaro-input:focus {
            outline: none;
            box-shadow: 1px 1px 0px 0px #FF7F00;
            border-color: #FF7F00;
            transform: translate(1px, 1px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-textDark flex items-center justify-center p-4">
    <div class="revaro-card w-full max-w-lg p-6 sm:p-8 space-y-6">
        <!-- Header Brand -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-accent">
            <a href="/" class="flex items-center space-x-2">
                <span class="text-2xl font-extrabold text-primary tracking-tighter">REVA<span class="text-secondary">RO</span></span>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border-2 border-emerald-600 shadow-sm">
                <i class="fas fa-check-circle text-emerald-600"></i>
                Google Terverifikasi
            </span>
        </div>

        <!-- Google User Card Info -->
        <div class="bg-accent/40 border-2 border-primary/20 rounded-2xl p-4 flex items-center space-x-4">
            @if(!empty($googleData['avatar']))
                <img src="{{ $googleData['avatar'] }}" alt="Foto Google" class="w-12 h-12 rounded-xl object-cover border-2 border-primary shadow-sm">
            @else
                <div class="w-12 h-12 rounded-xl bg-primary text-white font-black text-xl flex items-center justify-center border-2 border-primary shadow-sm">
                    {{ strtoupper(substr($googleData['nama'] ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-1.5">
                    <i class="fab fa-google text-secondary text-xs"></i>
                    <span class="text-[11px] font-bold text-textLight">Google Single Sign-On</span>
                </div>
                <h4 class="font-extrabold text-primary text-sm truncate">{{ $googleData['nama'] ?? 'Pengguna Google' }}</h4>
                <p class="text-xs text-textLight truncate font-mono">{{ $googleData['email'] }}</p>
            </div>
        </div>

        <!-- Instruksi -->
        <div class="space-y-1">
            <h3 class="text-lg font-black text-primary">Lengkapi Biodata Akun Anda</h3>
            <p class="text-xs text-textLight">
                Satu langkah lagi! Mohon lengkapi nomor telepon dan alamat rumah Anda agar teknisi dapat berkoordinasi dan menjangkau lokasi servis unit Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border-2 border-red-500 text-red-700 text-xs rounded-xl space-y-1 font-semibold">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <span>•</span> {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Form Biodata Lengkap -->
        <form action="{{ route('auth.google.complete.save') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-black text-primary mb-1 uppercase">Nama Lengkap</label>
                <input required type="text" name="nama" value="{{ old('nama', $googleData['nama'] ?? '') }}"
                       class="revaro-input font-bold"
                       placeholder="Contoh: Budi Santoso">
            </div>

            <!-- Email (Read-Only) -->
            <div>
                <label class="block text-xs font-black text-primary mb-1 uppercase">Alamat Email Terdaftar</label>
                <input type="email" value="{{ $googleData['email'] }}" disabled
                       class="w-full text-xs bg-gray-100 border-2 border-gray-300 rounded-xl p-3 text-gray-500 cursor-not-allowed font-mono">
            </div>

            <!-- Nomor HP / WhatsApp -->
            <div>
                <label class="block text-xs font-black text-primary mb-1 uppercase">
                    Nomor WhatsApp / HP Aktif <span class="text-secondary">*</span>
                </label>
                <input required type="text" name="hp" value="{{ old('hp') }}"
                       class="revaro-input font-mono"
                       placeholder="Contoh: 081234567890">
                <span class="text-[10px] text-textLight mt-1 block">Digunakan oleh teknisi untuk konfirmasi jadwal kunjungan dan estimasi kedatangan.</span>
            </div>

            <!-- Alamat Rumah Lengkap -->
            <div>
                <label class="block text-xs font-black text-primary mb-1 uppercase">
                    Alamat Lengkap Servis <span class="text-secondary">*</span>
                </label>
                <textarea required name="alamat" rows="3"
                          class="revaro-input"
                          placeholder="Contoh: Jl. Sudirman No. 45 RT 02/05, Kel. Melati, Kec. Sukajadi">{{ old('alamat') }}</textarea>
                <span class="text-[10px] text-textLight mt-1 block">Alamat tempat penjemputan unit atau perbaikan di lokasi pelanggan.</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full revaro-btn font-extrabold text-xs py-3.5 rounded-xl transition flex items-center justify-center space-x-2">
                <span>Simpan Biodata &amp; Masuk ke Layanan</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="text-center pt-1">
            <a href="/" class="text-xs text-textLight hover:text-primary font-bold hover:underline">
                Batalkan &amp; Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
