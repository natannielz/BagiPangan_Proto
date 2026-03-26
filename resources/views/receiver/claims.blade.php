<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Klaim Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if (session('status') === 'claim-created')
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            Donasi berhasil diklaim.
                        </div>
                    @endif
                    @if (session('status') === 'proof-uploaded')
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            Bukti berhasil diunggah.
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">Donasi</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Diklaim</th>
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
                                            <x-status-badge :status="$claim->status" />
                                        </td>
                                        <td class="py-3 pr-4">{{ optional($claim->claimed_at)->format('Y-m-d H:i') }}</td>
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
                                            @if (in_array($claim->status, ['claimed', 'awaiting_confirmation'], true))
                                                <a href="{{ route('receiver.claims.proof.form', $claim) }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                                                    Upload Bukti
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-gray-600">
                                            Belum ada klaim.
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
</x-app-layout>
