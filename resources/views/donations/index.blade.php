<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Donasi
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Filters --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('donations.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label for="q" class="block text-xs font-medium text-gray-600 mb-1">Cari judul</label>
                    <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Nasi kotak, roti..."
                           class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                </div>

                <div class="md:col-span-1">
                    <label for="category_id" class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                    <select id="category_id" name="category_id" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600">
                        <option value="">Semua</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label for="location" class="block text-xs font-medium text-gray-600 mb-1">Kecamatan</label>
                    <input id="location" name="location" type="text" value="{{ $filters['location'] ?? '' }}" placeholder="Kebayoran..."
                           class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                </div>

                <div class="md:col-span-1">
                    <label for="status" class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select id="status" name="status" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600">
                        <option value="" @selected(($filters['status'] ?? '') === '')>Tersedia</option>
                        <option value="claimed" @selected(($filters['status'] ?? '') === 'claimed')>Diklaim</option>
                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Selesai</option>
                        <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Dibatalkan</option>
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label for="sort" class="block text-xs font-medium text-gray-600 mb-1">Urutkan</label>
                    <select id="sort" name="sort" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600">
                        <option value="" @selected(($filters['sort'] ?? '') === '')>Terbaru</option>
                        <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Terlama</option>
                        <option value="expiry_asc" @selected(($filters['sort'] ?? '') === 'expiry_asc')>Kadaluarsa Terdekat</option>
                        <option value="expiry_desc" @selected(($filters['sort'] ?? '') === 'expiry_desc')>Kadaluarsa Terjauh</option>
                    </select>
                </div>

                <div class="md:col-span-6 flex gap-2">
                    <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700">
                        Terapkan
                    </button>
                    <a href="{{ route('donations.index') }}" class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 shadow-sm transition-colors hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Donation Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($donations as $donation)
                <a href="{{ route('donations.show', $donation) }}" class="group card-hover block rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    {{-- Photo --}}
                    <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                        @if ($donation->photo_url)
                            <img src="{{ $donation->photo_url }}"
                                 alt="{{ $donation->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center text-gray-300">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900 line-clamp-1 group-hover:text-brand-700 transition-colors">
                                {{ $donation->title }}
                            </h3>
                            <x-status-badge :status="$donation->status" />
                        </div>

                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <svg class="h-3.5 w-3.5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $donation->location_district }}
                        </div>

                        <div class="flex items-center gap-1.5 text-xs {{ $donation->expiry_at && $donation->expiry_at->diffInHours(now(), false) > -2 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            <svg class="h-3.5 w-3.5 flex-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $donation->expiry_at ? $donation->expiry_at->diffForHumans() : '-' }}
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                            <span class="text-sm font-semibold text-gray-700">{{ $donation->qty_portions }} porsi</span>
                            <span class="text-xs font-semibold text-brand-700 group-hover:text-brand-600 transition-colors">Lihat Detail &rarr;</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-gray-100 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">Tidak ada donasi yang cocok dengan filter.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div>
            {{ $donations->links() }}
        </div>
    </div>
</x-app-layout>
