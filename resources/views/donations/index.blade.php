<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Donasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <form method="GET" action="{{ route('donations.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                        <div class="md:col-span-2">
                            <x-input-label for="q" value="Cari judul" />
                            <x-text-input id="q" name="q" type="text" class="mt-1 block w-full" :value="$filters['q'] ?? ''" />
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="category_id" value="Kategori" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="location" value="Kecamatan" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="$filters['location'] ?? ''" />
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" @selected(($filters['status'] ?? '') === '')>available</option>
                                <option value="claimed" @selected(($filters['status'] ?? '') === 'claimed')>claimed</option>
                                <option value="picked_up" @selected(($filters['status'] ?? '') === 'picked_up')>picked_up</option>
                                <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>completed</option>
                                <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>cancelled</option>
                            </select>
                        </div>

                        <div class="md:col-span-1">
                            <x-input-label for="sort" value="Urutkan" />
                            <select id="sort" name="sort" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" @selected(($filters['sort'] ?? '') === '')>Terbaru</option>
                                <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Terlama</option>
                                <option value="expiry_asc" @selected(($filters['sort'] ?? '') === 'expiry_asc')>Kadaluarsa Terdekat</option>
                                <option value="expiry_desc" @selected(($filters['sort'] ?? '') === 'expiry_desc')>Kadaluarsa Terjauh</option>
                            </select>
                        </div>

                        <div class="md:col-span-6 flex gap-2">
                            <x-primary-button type="submit">Terapkan</x-primary-button>
                            <a href="{{ route('donations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200">
                                Reset
                            </a>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($donations as $donation)
                            <a href="{{ route('donations.show', $donation) }}" class="block border border-gray-200 rounded-lg overflow-hidden hover:border-indigo-300">
                                <div class="aspect-[16/9] bg-gray-100">
                                    @if ($donation->photo_url)
                                        <img src="{{ $donation->photo_url }}" alt="{{ $donation->title }}" class="w-full h-full object-cover" loading="lazy">
                                    @endif
                                </div>
                                <div class="p-4 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="font-semibold text-gray-900">
                                            {{ $donation->title }}
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                            {{ $donation->status }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $donation->category?->name }} · {{ $donation->location_district }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Porsi: {{ $donation->qty_portions }} · Exp: {{ optional($donation->expiry_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="md:col-span-6 text-gray-600">
                                Tidak ada donasi yang cocok dengan filter.
                            </div>
                        @endforelse
                    </div>

                    <div>
                        {{ $donations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
