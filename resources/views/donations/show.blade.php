<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Donasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-600">
                                {{ $donation->category?->name }} · {{ $donation->location_district }}
                            </div>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                {{ $donation->title }}
                            </h1>
                        </div>
                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                            {{ $donation->status }}
                        </span>
                    </div>

                    @if ($photoUrl)
                        <div class="aspect-[16/9] bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ $photoUrl }}" alt="{{ $donation->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Jumlah porsi</div>
                            <div class="text-lg font-semibold">{{ $donation->qty_portions }}</div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm text-gray-600">Kadaluarsa</div>
                            <div class="text-lg font-semibold">{{ optional($donation->expiry_at)->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    @if ($donation->description)
                        <div class="space-y-2">
                            <div class="text-sm font-semibold text-gray-900">Deskripsi</div>
                            <div class="text-gray-700 whitespace-pre-line">{{ $donation->description }}</div>
                        </div>
                    @endif

                    @if (Auth::check() && Auth::user()->role === 'receiver' && $donation->status === 'available' && optional($donation->expiry_at)->isFuture())
                        <form method="POST" action="{{ route('receiver.donations.claim', $donation) }}">
                            @csrf
                            <x-primary-button type="submit">Klaim Donasi</x-primary-button>
                        </form>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('donations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
