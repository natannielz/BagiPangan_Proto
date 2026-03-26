<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Verifikasi Klaim
        </h2>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if (session('status') === 'claim-verified')
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            Klaim selesai diverifikasi.
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">Donasi</th>
                                    <th class="py-2 pr-4">Penerima</th>
                                    <th class="py-2 pr-4">Bukti</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($claims as $claim)
                                    <tr class="border-b">
                                        <td class="py-3 pr-4">
                                            <a href="{{ route('donations.show', $claim->donation) }}" class="font-semibold text-gray-900 hover:text-brand-700">
                                                {{ $claim->donation?->title }}
                                            </a>
                                            <div class="text-xs text-gray-600">
                                                {{ $claim->donation?->category?->name }} · {{ $claim->donation?->location_district }}
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            {{ $claim->claimer?->name }}
                                            <div class="text-xs text-gray-600">{{ $claim->claimer?->email }}</div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($claim->proof_photo_path)
                                                <a href="{{ URL::temporarySignedRoute('claims.proof', now()->addHour(), ['claim' => $claim->id]) }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600" target="_blank" rel="noreferrer">
                                                    Lihat
                                                </a>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <form method="POST" action="{{ route('donor.claims.verify', $claim) }}" id="verify-form-{{ $claim->id }}">
                                                @csrf
                                                <button
                                                    type="button"
                                                    class="text-sm font-semibold text-brand-800 hover:text-brand-600"
                                                    @click.prevent="$dispatch('confirm-action', {formId: 'verify-form-{{ $claim->id }}'})"
                                                >
                                                    Verifikasi
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-gray-600">
                                            Tidak ada klaim yang menunggu verifikasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $claims->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-modal message="Apakah Anda yakin ingin memverifikasi klaim ini?" confirm-text="Ya, Verifikasi" />
</x-app-layout>

