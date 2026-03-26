<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Penerima
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Hero Banner --}}
        <x-dashboard-banner />

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Total Klaim</div>
                <div class="text-2xl font-bold text-brand-700" data-countup="{{ $totalClaims }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Klaim Aktif</div>
                <div class="text-2xl font-bold text-brand-600" data-countup="{{ $activeClaims }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Menunggu Konfirmasi</div>
                <div class="text-2xl font-bold text-yellow-600" data-countup="{{ $pendingClaims }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Selesai</div>
                <div class="text-2xl font-bold text-emerald-600" data-countup="{{ $completedClaims }}">0</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('donations.index') }}" class="card-hover flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    @include('partials.icons.search', ['class'=>'w-6 h-6'])
                </div>
                <div>
                    <div class="font-semibold text-gray-900">Cari Donasi</div>
                    <div class="text-sm text-gray-500">Temukan makanan yang tersedia</div>
                </div>
            </a>
            <a href="{{ route('receiver.claims') }}" class="card-hover flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    @include('partials.icons.list', ['class'=>'w-6 h-6'])
                </div>
                <div>
                    <div class="font-semibold text-gray-900">Klaim Saya</div>
                    <div class="text-sm text-gray-500">Lihat status klaim Anda</div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
