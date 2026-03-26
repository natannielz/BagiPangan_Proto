<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Donasi Saya
            </h2>
            <a href="{{ route('donor.donations.create') }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                Buat Donasi
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if (session('status') === 'donation-created')
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            Donasi berhasil dibuat.
                        </div>
                    @endif
                    @if (session('status') === 'donation-updated')
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            Donasi berhasil diperbarui.
                        </div>
                    @endif
                    @if (session('status') === 'donation-cancelled')
                        <div class="rounded-md bg-yellow-50 p-4 text-yellow-800">
                            Donasi dibatalkan.
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">Judul</th>
                                    <th class="py-2 pr-4">Kategori</th>
                                    <th class="py-2 pr-4">Porsi</th>
                                    <th class="py-2 pr-4">Kecamatan</th>
                                    <th class="py-2 pr-4">Exp</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Moderasi</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donations as $donation)
                                    <tr class="border-b">
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('donations.show', $donation) }}" class="font-semibold text-gray-900 hover:text-brand-700">
                                                {{ $donation->title }}
                                            </a>
                                        </td>
                                        <td class="py-3 pr-4">{{ $donation->category?->name }}</td>
                                        <td class="py-3 pr-4">{{ $donation->qty_portions }}</td>
                                        <td class="py-3 pr-4">{{ $donation->location_district }}</td>
                                        <td class="py-3 pr-4">{{ optional($donation->expiry_at)->format('Y-m-d H:i') }}</td>
                                        <td class="py-3 pr-4">
                                            <x-status-badge :status="$donation->status" />
                                        </td>
                                        <td class="py-3 pr-4">
                                            <x-status-badge :status="$donation->moderation_status" />
                                            @if($donation->moderation_status === 'rejected' && $donation->rejection_reason)
                                                <div x-data="{ open: false }" class="inline-block ml-1">
                                                    <button
                                                        @click="open = !open"
                                                        class="text-xs text-red-600 underline hover:text-red-800"
                                                    >Lihat alasan</button>
                                                    <div
                                                        x-show="open"
                                                        @click.outside="open = false"
                                                        class="absolute z-10 mt-1 p-3 bg-white border border-red-200 rounded-md shadow-lg max-w-xs text-xs text-gray-700"
                                                    >
                                                        {{ $donation->rejection_reason }}
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('donor.donations.edit', $donation) }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('donor.donations.cancel', $donation) }}" id="cancel-form-{{ $donation->id }}">
                                                    @csrf
                                                    <button
                                                        type="button"
                                                        class="text-sm font-semibold text-red-700 hover:text-red-600"
                                                        @click.prevent="$dispatch('confirm-action', {formId: 'cancel-form-{{ $donation->id }}'})"
                                                    >
                                                        Batalkan
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-6 text-gray-600">
                                            Belum ada donasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $donations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-modal message="Apakah Anda yakin ingin membatalkan donasi ini?" />
</x-app-layout>
