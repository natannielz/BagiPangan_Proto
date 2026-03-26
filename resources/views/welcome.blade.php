<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BagiPangan — Bagi Makanan, Kurangi Pemborosan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">

{{-- ═══ Glassmorphism Navbar ═══ --}}
<nav x-data="{ scrolled: false }"
     @scroll.window="scrolled = (window.scrollY > 80)"
     :class="scrolled ? 'bg-white/95 shadow-sm border-b border-gray-100' : 'bg-white/10 backdrop-blur-lg'"
     class="fixed inset-x-0 top-0 z-50 transition-all duration-300">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="/" class="flex items-center gap-2">
            <img :src="scrolled ? '{{ asset('img/logo.svg') }}' : '{{ asset('img/logo-white.svg') }}'" alt="BagiPangan" class="h-8 transition-all">
        </a>
        <div class="flex items-center gap-6">
            <a href="{{ route('donations.index') }}" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Donasi</a>
            <a href="#cara-kerja" class="hidden text-sm font-medium transition-colors sm:inline" :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Cara Kerja</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route(match(auth()->user()->role) { 'admin'=>'admin.dashboard', 'donor'=>'donor.dashboard', 'receiver'=>'donations.index', default=>'donations.index' }) }}"
                   class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors"
                   :class="scrolled ? 'bg-brand-600 text-white hover:bg-brand-700' : 'bg-white text-brand-800 hover:bg-brand-50'">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium transition-colors" :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Masuk</a>
                <a href="{{ route('register') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition-colors" :class="scrolled ? 'bg-brand-600 text-white hover:bg-brand-700' : 'bg-white text-brand-800 hover:bg-brand-50'">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ═══ Hero Section ═══ --}}
<section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-brand-900">
    {{-- Background illustration --}}
    <img src="{{ asset('img/hero-food.svg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-br from-brand-900/90 via-brand-800/80 to-sidebar/90"></div>

    <div class="relative z-10 mx-auto max-w-4xl px-6 py-32 text-center text-white">
        <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
            <span class="relative inline-block">
                BagiPangan
                {{-- SVG Ellipse draw-on --}}
                <svg class="absolute -inset-x-4 -inset-y-2 h-[calc(100%+16px)] w-[calc(100%+32px)]" viewBox="0 0 300 80" fill="none" preserveAspectRatio="none">
                    <ellipse cx="150" cy="40" rx="145" ry="35" stroke="#a3e635" stroke-width="3" class="draw-ellipse" />
                </svg>
            </span>
            <br class="sm:hidden">
            <span class="mt-2 inline-block">untuk Semua</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-lg text-white/80 sm:text-xl">
            Platform distribusi makanan berlebih dari donatur ke penerima.
            Bersama kita kurangi food waste dan bantu sesama.
        </p>
        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a href="{{ route('register') }}" class="inline-flex items-center rounded-2xl bg-accent px-8 py-3.5 text-base font-bold text-brand-900 shadow-lg transition-all hover:bg-accent/90 hover:shadow-xl">
                Mulai Berbagi
            </a>
            <a href="{{ route('donations.index') }}" class="inline-flex items-center rounded-2xl border-2 border-white/30 px-8 py-3.5 text-base font-semibold text-white transition-all hover:border-white/60 hover:bg-white/10">
                Lihat Donasi
            </a>
        </div>
    </div>
</section>

{{-- ═══ Stats Section (Count-up) ═══ --}}
<section class="relative z-10 -mt-12 px-6">
    <div class="mx-auto grid max-w-5xl grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach([['1200','Donasi Dibuat'],['15000','Porsi Tersalurkan'],['800','Pengguna Aktif'],['20','Kota']] as [$num,$label])
        <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-lg">
            <div class="text-3xl font-bold text-brand-700">
                <span data-countup="{{ $num }}">0</span>+
            </div>
            <div class="mt-1 text-sm font-medium text-gray-500">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ═══ Features Grid ═══ --}}
<section id="cara-kerja" class="px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="mb-14 text-center">
            <span class="inline-block rounded-full bg-brand-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-brand-700">Fitur</span>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Semua yang Anda butuhkan</h2>
            <p class="mx-auto mt-3 max-w-xl text-gray-500">Platform sederhana, transparan, dan terdokumentasi.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['Posting Donasi','Unggah info makanan berlebih dalam hitungan menit.','plus'],
                ['Klaim & Ambil','Penerima klaim, lalu ambil langsung secara offline.','check'],
                ['Konfirmasi Foto','Upload bukti pengambilan untuk transparansi penuh.','clipboard'],
                ['Dashboard Real-time','Pantau semua aktivitas donasi Anda kapan saja.','chart'],
                ['Aman & Terverifikasi','Akun admin memastikan semua listing valid.','settings'],
                ['Tanpa Biaya','100% gratis untuk donatur dan penerima.','tag'],
            ] as [$title,$desc,$icon])
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    @include('partials.icons.'.$icon, ['class'=>'w-6 h-6'])
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ Dashboard Mockup Section ═══ --}}
<section class="bg-brand-50 px-6 py-24">
    <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
        <div>
            <span class="inline-block rounded-full bg-brand-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-brand-700">Platform</span>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Dashboard lengkap untuk donatur dan admin
            </h2>
            <p class="mt-4 text-lg text-gray-500">
                Pantau donasi, status klaim, dan laporan penyaluran dalam satu tampilan yang bersih.
            </p>
            <a href="{{ route('register') }}" class="mt-6 inline-flex items-center rounded-2xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:bg-brand-700 hover:shadow-md">
                Coba Sekarang
            </a>
        </div>
        <div class="flex justify-center">
            <img src="{{ asset('img/dashboard-preview.svg') }}" alt="Dashboard Preview" class="mockup-tilt w-full max-w-lg">
        </div>
    </div>
</section>

{{-- ═══ CTA Section ═══ --}}
<section class="bg-gradient-to-br from-brand-700 to-brand-900 px-6 py-24 text-center text-white">
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Siap Mengurangi Food Waste?</h2>
    <p class="mx-auto mt-4 max-w-xl text-lg text-white/80">Bergabung bersama ratusan donatur dan penerima di seluruh Indonesia.</p>
    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
        <a href="{{ route('register') }}" class="inline-flex items-center rounded-2xl bg-accent px-8 py-3.5 text-base font-bold text-brand-900 shadow-lg transition-all hover:bg-accent/90 hover:shadow-xl">
            Daftar Gratis
        </a>
        <a href="{{ route('donations.index') }}" class="inline-flex items-center rounded-2xl border-2 border-white/30 px-8 py-3.5 text-base font-semibold text-white transition-all hover:border-white/60 hover:bg-white/10">
            Lihat Donasi
        </a>
    </div>
</section>

{{-- ═══ Footer ═══ --}}
<footer class="border-t border-gray-100 bg-white px-6 py-16">
    <div class="mx-auto grid max-w-6xl gap-10 sm:grid-cols-4">
        <div>
            <img src="{{ asset('img/logo.svg') }}" alt="BagiPangan" class="h-8">
            <p class="mt-3 text-sm text-gray-500">Platform distribusi makanan berlebih di Indonesia.</p>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-gray-900">Tautan</h4>
            <ul class="mt-3 space-y-2 text-sm text-gray-500">
                <li><a href="{{ route('donations.index') }}" class="hover:text-brand-600">Donasi</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-brand-600">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-brand-600">Daftar</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-gray-900">Kontak</h4>
            <p class="mt-3 text-sm text-gray-500">hello@bagipangan.id</p>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-gray-900">Sosial</h4>
            <p class="mt-3 text-sm text-gray-500">Instagram &middot; Twitter &middot; LinkedIn</p>
        </div>
    </div>
    <div class="mx-auto mt-12 max-w-6xl border-t border-gray-100 pt-6 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} BagiPangan. Dibuat dengan cinta untuk Indonesia.
    </div>
</footer>

</body>
</html>
