<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifikasi
            </h2>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                    Tandai semua dibaca
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="text-sm text-gray-600">
                        Belum dibaca: <span class="font-semibold">{{ $unreadCount }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($notifications as $notification)
                            @php
                                $data = $notification->data ?? [];
                                $type = $data['type'] ?? '';
                                $title = $data['title'] ?? null;
                                $message = match ($type) {
                                    'donation_claimed' => 'Donasi Anda diklaim',
                                    'donation_completed' => 'Donasi selesai',
                                    'donation_cancelled' => 'Donasi dibatalkan',
                                    default => 'Notifikasi',
                                };
                            @endphp

                            <div class="border border-gray-200 rounded-lg p-4 flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="font-semibold text-gray-900">{{ $message }}</div>
                                        @if (is_null($notification->read_at))
                                            <span class="text-xs px-2 py-1 rounded bg-yellow-50 text-yellow-800">baru</span>
                                        @endif
                                    </div>
                                    @if ($title)
                                        <div class="text-sm text-gray-700">{{ $title }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500">
                                        {{ optional($notification->created_at)->format('Y-m-d H:i') }}
                                    </div>
                                </div>

                                @if (is_null($notification->read_at))
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                        @csrf
                                        <button type="submit" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="text-gray-600">
                                Belum ada notifikasi.
                            </div>
                        @endforelse
                    </div>

                    <div>
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
