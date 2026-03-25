<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload Bukti
            </h2>
            <a href="{{ route('receiver.claims') }}" class="text-sm font-semibold text-brand-800 hover:text-brand-600">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Donasi</div>
                        <div class="font-semibold text-gray-900">{{ $claim->donation?->title }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $claim->donation?->category?->name }} · {{ $claim->donation?->location_district }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('receiver.claims.proof.upload', $claim) }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="proof_photo" :value="'Foto Bukti'" />
                            <input id="proof_photo" name="proof_photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700" required />
                            <x-input-error class="mt-2" :messages="$errors->get('proof_photo')" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('receiver.claims') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">Batal</a>
                            <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

