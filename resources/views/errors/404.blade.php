<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Pindang OI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/errors.css">
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-50 text-slate-800">
    
    <!-- Background Elements (Blobs) -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 blob" style="transform: translate(30%, -30%);"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-gray-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 blob" style="animation-delay: 2s; transform: translate(-30%, 30%);"></div>

    <div class="relative z-10 max-w-lg w-full px-8 py-14 bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/50 text-center mx-4">
        
        <!-- Animated Graphic -->
        <div class="relative w-40 h-40 mx-auto mb-8 float">
            <h1 class="text-9xl font-black text-slate-200 drop-shadow-md">404</h1>
            <!-- Radar/Search icon -->
            <svg class="absolute inset-0 m-auto w-20 h-20 text-brand-500 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">Oops! Kesasar ya?</h2>
        <p class="text-slate-500 mb-8 leading-relaxed">Halaman yang Anda cari sepertinya tidak ada, sudah dipindahkan, atau Anda salah mengetikkan URL.</p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-full text-sm font-semibold transition-all duration-200 shadow-lg shadow-brand-600/30 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="window.history.back()" class="px-6 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-full text-sm font-semibold transition-all duration-200 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </button>
        </div>
    </div>

    <!-- Pindang OI Branding -->
    <div class="absolute bottom-6 left-0 right-0 text-center">
        <p class="text-sm font-medium text-slate-400">&copy; {{ date('Y') }} <span class="text-slate-500 font-semibold">Pindang OI</span> - BPS Kabupaten Ogan Ilir</p>
    </div>
</body>
</html>
