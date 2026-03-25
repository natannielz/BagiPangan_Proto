<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kategori
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('status') === 'category-created')
                        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            Kategori berhasil dibuat.
                        </div>
                    @endif
                    @if (session('status') === 'category-updated')
                        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            Kategori berhasil diperbarui.
                        </div>
                    @endif
                    @if (session('status') === 'category-deleted')
                        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                            Kategori berhasil dihapus.
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-3 py-3">Nama</th>
                                    <th class="px-3 py-3">Slug</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($categories as $category)
                                    <tr class="text-sm text-gray-800">
                                        <td class="px-3 py-3 font-medium">{{ $category->name }}</td>
                                        <td class="px-3 py-3 font-mono text-xs text-gray-600">{{ $category->slug }}</td>
                                        <td class="px-3 py-3">
                                            @if ($category->is_active)
                                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-800">Aktif</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-10 text-center text-sm text-gray-600">
                                            Belum ada kategori. Klik “Tambah Kategori” untuk membuat yang pertama.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
