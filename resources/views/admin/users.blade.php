<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Pengguna
            </h2>
            <form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-2">
                <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 text-sm">
                    <option value="" @selected(($roleFilter ?? '') === '')>Semua Peran</option>
                    <option value="admin" @selected(($roleFilter ?? '') === 'admin')>Admin</option>
                    <option value="donor" @selected(($roleFilter ?? '') === 'donor')>Donatur</option>
                    <option value="receiver" @selected(($roleFilter ?? '') === 'receiver')>Penerima</option>
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
                                    <th class="py-2 pr-4">Nama</th>
                                    <th class="py-2 pr-4">Email</th>
                                    <th class="py-2 pr-4">Peran</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="border-b">
                                        <td class="py-3 pr-4 font-semibold text-gray-900">{{ $user->name }}</td>
                                        <td class="py-3 pr-4 text-gray-600">{{ $user->email }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($user->suspended_at)
                                                <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-800">Ditangguhkan</span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">Aktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            @if ($user->id !== auth()->id())
                                                @if ($user->suspended_at)
                                                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" id="unsuspend-form-{{ $user->id }}">
                                                        @csrf
                                                        <button
                                                            type="button"
                                                            class="text-sm font-semibold text-brand-800 hover:text-brand-600"
                                                            @click.prevent="$dispatch('confirm-action', {formId: 'unsuspend-form-{{ $user->id }}'})"
                                                        >
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" id="suspend-form-{{ $user->id }}">
                                                        @csrf
                                                        <button
                                                            type="button"
                                                            class="text-sm font-semibold text-red-700 hover:text-red-600"
                                                            @click.prevent="$dispatch('confirm-action', {formId: 'suspend-form-{{ $user->id }}'})"
                                                        >
                                                            Tangguhkan
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-gray-600">
                                            Belum ada pengguna.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-modal message="Apakah Anda yakin ingin melanjutkan tindakan ini?" />
</x-app-layout>
