<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Donasi
            </h2>
            <a href="{{ route('donor.donations.index') }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('donor.donations.store') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="category_id" :value="'Kategori'" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600" required>
                                <option value="">Pilih</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div>
                            <x-input-label for="title" :value="'Judul'" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="'Deskripsi (Opsional)'" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="qty_portions" :value="'Jumlah Porsi'" />
                                <x-text-input id="qty_portions" name="qty_portions" type="number" class="mt-1 block w-full" :value="old('qty_portions')" required min="1" max="10000" />
                                <x-input-error class="mt-2" :messages="$errors->get('qty_portions')" />
                            </div>

                            <div>
                                <x-input-label for="location_district" :value="'Kecamatan'" />
                                <x-text-input id="location_district" name="location_district" type="text" class="mt-1 block w-full" :value="old('location_district')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('location_district')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="expiry_at" :value="'Kadaluarsa'" />
                                <x-text-input id="expiry_at" name="expiry_at" type="datetime-local" class="mt-1 block w-full" :value="old('expiry_at')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('expiry_at')" />
                            </div>

                            <div>
                                <x-input-label for="photo" :value="'Foto (Opsional)'" />
                                <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700" />
                                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('donor.donations.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">Batal</a>
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

