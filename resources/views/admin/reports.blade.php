<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="text-sm text-gray-600">
                        Export data dalam format CSV. Gunakan filter untuk menyaring data.
                    </div>

                    <form method="GET" action="{{ route('admin.reports.donations.csv') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="from" value="{{ request('from') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="to" value="{{ request('to') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status Donasi</label>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="available"  @selected(request('status') === 'available')>Available</option>
                                    <option value="claimed"    @selected(request('status') === 'claimed')>Claimed</option>
                                    <option value="completed"  @selected(request('status') === 'completed')>Completed</option>
                                    <option value="cancelled"  @selected(request('status') === 'cancelled')>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status Moderasi</label>
                                <select name="moderation_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                                    <option value="">Semua Moderasi</option>
                                    <option value="pending"   @selected(request('moderation_status') === 'pending')>Pending</option>
                                    <option value="approved"  @selected(request('moderation_status') === 'approved')>Approved</option>
                                    <option value="rejected"  @selected(request('moderation_status') === 'rejected')>Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Donor</label>
                                <select name="donor_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                                    <option value="">Semua Donor</option>
                                    @foreach ($donors as $donor)
                                        <option value="{{ $donor->id }}" @selected(request('donor_id') == $donor->id)>
                                            {{ $donor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-brand-800">
                                Unduh CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
