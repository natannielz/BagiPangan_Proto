<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Moderasi Donasi
            </h2>
            <form method="GET" action="{{ route('admin.donations') }}" class="flex items-center gap-2">
                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                    <option value="" @selected(($statusFilter ?? '') === '')>Semua</option>
                    <option value="pending" @selected(($statusFilter ?? '') === 'pending')>pending</option>
                    <option value="approved" @selected(($statusFilter ?? '') === 'approved')>approved</option>
                    <option value="rejected" @selected(($statusFilter ?? '') === 'rejected')>rejected</option>
                </select>
                <button type="submit" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                    Filter
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if (session('success'))
                        <div class="rounded-md bg-green-50 p-4 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="rounded-md bg-red-50 p-4 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600 border-b">
                                    <th class="py-2 pr-4">Judul</th>
                                    <th class="py-2 pr-4">Donor</th>
                                    <th class="py-2 pr-4">Kategori</th>
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
                                            <div class="text-xs text-gray-600">
                                                {{ $donation->location_district }} · {{ optional($donation->expiry_at)->format('Y-m-d H:i') }}
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            {{ $donation->donor?->name }}
                                            <div class="text-xs text-gray-600">{{ $donation->donor?->email }}</div>
                                        </td>
                                        <td class="py-3 pr-4">{{ $donation->category?->name }}</td>
                                        <td class="py-3 pr-4">
                                            <x-status-badge :status="$donation->status" />
                                        </td>
                                        <td class="py-3 pr-4">
                                            <x-status-badge :status="$donation->moderation_status" />
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                @if ($donation->moderation_status === 'pending')
                                                    <form method="POST" action="{{ route('admin.donations.approve', $donation) }}" id="approve-form-{{ $donation->id }}">
                                                        @csrf
                                                        <button type="submit" class="text-sm font-semibold text-brand-800 hover:text-brand-600">
                                                            Setujui
                                                        </button>
                                                    </form>

                                                    @if (!in_array($donation->status, ['claimed','picked_up','completed']))
                                                        <form method="POST" action="{{ route('admin.donations.reject', $donation) }}" id="reject-form-{{ $donation->id }}">
                                                            @csrf
                                                            <input type="hidden" name="rejection_reason" value="" id="rejection-reason-{{ $donation->id }}">
                                                            <button
                                                                type="button"
                                                                class="text-sm font-semibold text-red-700 hover:text-red-600"
                                                                @click.prevent="$dispatch('confirm-action', {formId: 'reject-form-{{ $donation->id }}', donationId: {{ $donation->id }}})"
                                                            >
                                                                Tolak
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-gray-600">
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

    {{-- Reject confirm modal with rejection reason --}}
    <div
        x-data="{ open: false, formId: '', donationId: null, reason: '' }"
        @confirm-action.window="open = true; formId = $event.detail.formId; donationId = $event.detail.donationId; reason = ''"
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none"
    >
        <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <p class="text-gray-900 font-semibold mb-4">Tolak donasi ini?</p>
            <label class="block text-sm text-gray-700 mb-1">Alasan penolakan (opsional)</label>
            <textarea
                x-model="reason"
                class="w-full border border-gray-300 rounded-md text-sm p-2 mb-4"
                rows="3"
                placeholder="Tulis alasan penolakan..."
            ></textarea>
            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    @click="open = false"
                    class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="
                        open = false;
                        document.getElementById('rejection-reason-' + donationId).value = reason;
                        document.getElementById(formId).submit()
                    "
                    class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700"
                >
                    Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
