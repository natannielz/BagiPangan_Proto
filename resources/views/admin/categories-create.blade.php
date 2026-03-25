<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Kategori
            </h2>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="'Nama'" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="slug" :value="'Slug (Opsional)'" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug')" />
                            <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="'Deskripsi (Opsional)'" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="is_active" :value="'Status'" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600" required>
                                <option value="1" @selected(old('is_active', '1') === '1')>Aktif</option>
                                <option value="0" @selected(old('is_active') === '0')>Nonaktif</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">Batal</a>
                            <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
