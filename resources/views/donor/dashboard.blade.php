<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Donatur
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Hero Banner --}}
        <x-dashboard-banner />

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Total Donasi</div>
                <div class="text-2xl font-bold text-brand-700" data-countup="{{ $totalDonations }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Donasi Aktif</div>
                <div class="text-2xl font-bold text-brand-600" data-countup="{{ $activeDonations }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Diklaim</div>
                <div class="text-2xl font-bold text-blue-600" data-countup="{{ $claimedDonations }}">0</div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Selesai</div>
                <div class="text-2xl font-bold text-emerald-600" data-countup="{{ $completedDonations }}">0</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('donor.donations.create') }}" class="card-hover flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    @include('partials.icons.plus', ['class'=>'w-6 h-6'])
                </div>
                <div>
                    <div class="font-semibold text-gray-900">Buat Donasi Baru</div>
                    <div class="text-sm text-gray-500">Bagikan makanan berlebih Anda</div>
                </div>
            </a>
            <a href="{{ route('donor.claims.index') }}" class="card-hover flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    @include('partials.icons.check', ['class'=>'w-6 h-6'])
                </div>
                <div>
                    <div class="font-semibold text-gray-900">Lihat Klaim Masuk</div>
                    <div class="text-sm text-gray-500">Verifikasi pengambilan donasi</div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
