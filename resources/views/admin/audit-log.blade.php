<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Audit Log
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-3 py-3">Waktu</th>
                                    <th class="px-3 py-3">Aksi</th>
                                    <th class="px-3 py-3">Subjek</th>
                                    <th class="px-3 py-3">User</th>
                                    <th class="px-3 py-3">IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($logs as $log)
                                    <tr class="text-sm text-gray-800">
                                        <td class="px-3 py-3 font-mono text-xs text-gray-600">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 font-mono text-xs text-gray-600">
                                            {{ class_basename($log->subject_type) }}#{{ $log->subject_id }}
                                        </td>
                                        <td class="px-3 py-3 font-mono text-xs text-gray-600">
                                            {{ $log->user_id ?? 'SYSTEM' }}
                                        </td>
                                        <td class="px-3 py-3 font-mono text-xs text-gray-600">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-10 text-center text-sm text-gray-600">
                                            Belum ada aktivitas yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
