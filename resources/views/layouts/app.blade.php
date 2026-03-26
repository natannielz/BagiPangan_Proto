<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BagiPangan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600|jetbrains-mono:400,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        @php
            $navItems = match(auth()->user()->role ?? '') {
                'admin' => [
                    ['route'=>'admin.dashboard',      'icon'=>'home',      'label'=>'Dashboard'],
                    ['route'=>'admin.users',          'icon'=>'users',     'label'=>'Pengguna'],
                    ['route'=>'admin.donations',      'icon'=>'clipboard', 'label'=>'Donasi'],
                    ['route'=>'admin.reports',        'icon'=>'chart',     'label'=>'Laporan'],
                    ['route'=>'admin.categories.index','icon'=>'tag',      'label'=>'Kategori'],
                ],
                'donor' => [
                    ['route'=>'donor.dashboard',        'icon'=>'home',  'label'=>'Dashboard'],
                    ['route'=>'donor.donations.create',  'icon'=>'plus',  'label'=>'Buat Donasi'],
                    ['route'=>'donor.donations.index',   'icon'=>'list',  'label'=>'Donasi Saya'],
                    ['route'=>'donor.claims.index',      'icon'=>'check', 'label'=>'Klaim Masuk'],
                ],
                'receiver' => [
                    ['route'=>'receiver.dashboard',    'icon'=>'home',   'label'=>'Dashboard'],
                    ['route'=>'donations.index',       'icon'=>'search', 'label'=>'Cari Donasi'],
                    ['route'=>'receiver.claims',       'icon'=>'list',   'label'=>'Klaim Saya'],
                ],
                default => [],
            };
        @endphp

        {{-- ═══ Desktop Sidebar (64px icon-only) ═══ --}}
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-16 flex-col items-center bg-sidebar py-4 lg:flex">
            {{-- Logo --}}
            <a href="{{ route(match(auth()->user()->role ?? '') { 'admin'=>'admin.dashboard', 'donor'=>'donor.dashboard', 'receiver'=>'donations.index', default=>'donations.index' }) }}" class="mb-6 flex h-10 w-10 items-center justify-center">
                <img src="{{ asset('img/logo-mark.svg') }}" alt="BagiPangan" class="h-8 w-8">
            </a>

            {{-- Nav Items --}}
            <nav class="flex flex-1 flex-col items-center gap-1">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="group relative flex h-10 w-10 items-center justify-center rounded-xl transition-colors
                              {{ request()->routeIs($item['route'].'*') ? 'bg-brand-600/20 text-accent' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
                       title="{{ $item['label'] }}">
                        @include('partials.icons.'.$item['icon'], ['class'=>'w-5 h-5'])
                        {{-- Tooltip --}}
                        <span class="pointer-events-none absolute left-14 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                            {{ $item['label'] }}
                        </span>
                        {{-- Active indicator --}}
                        @if(request()->routeIs($item['route'].'*'))
                            <span class="absolute left-0 h-6 w-1 rounded-r-full bg-accent"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Bottom Actions --}}
            <div class="flex flex-col items-center gap-1">
                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}"
                   class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-white/10 hover:text-white"
                   title="Notifikasi">
                    @include('partials.icons.bell', ['class'=>'w-5 h-5'])
                    @if(auth()->user()?->unreadNotifications?->count())
                        <span class="absolute right-1.5 top-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                    <span class="pointer-events-none absolute left-14 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">Notifikasi</span>
                </a>

                {{-- Settings / Profile --}}
                <a href="{{ route('profile.edit') }}"
                   class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-white/10 hover:text-white"
                   title="Pengaturan">
                    @include('partials.icons.settings', ['class'=>'w-5 h-5'])
                    <span class="pointer-events-none absolute left-14 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">Pengaturan</span>
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="group relative flex h-10 w-10 items-center justify-center rounded-xl text-gray-400 transition-colors hover:bg-white/10 hover:text-red-400"
                            title="Keluar">
                        @include('partials.icons.logout', ['class'=>'w-5 h-5'])
                        <span class="pointer-events-none absolute left-14 whitespace-nowrap rounded-lg bg-gray-900 px-2.5 py-1 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ═══ Main Content Area ═══ --}}
        <div class="min-h-screen bg-brand-50 lg:ml-16">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-gray-100 bg-white/80 px-4 py-3 backdrop-blur-md sm:px-6">
                <div class="text-lg font-semibold text-gray-800">
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    {{-- Role badge --}}
                    <span class="hidden rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 sm:inline-flex">
                        {{ ucfirst(auth()->user()->role ?? '') }}
                    </span>
                    {{-- Avatar --}}
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? '', 0, 2)) }}
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-4 pb-20 sm:p-6 lg:pb-6">
                {{ $slot }}
            </main>
        </div>

        {{-- ═══ Mobile Bottom Tab Bar ═══ --}}
        <nav class="fixed inset-x-0 bottom-0 z-40 flex items-center justify-around border-t border-gray-200 bg-white px-2 py-2 lg:hidden">
            @foreach(array_slice($navItems, 0, 4) as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] font-medium transition-colors
                          {{ request()->routeIs($item['route'].'*') ? 'text-brand-600' : 'text-gray-400' }}">
                    @include('partials.icons.'.$item['icon'], ['class'=>'w-5 h-5'])
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Toast Container --}}
        @if(session('success') || session('error') || session('status'))
            <x-toast-container />
        @endif
    </body>
</html>
