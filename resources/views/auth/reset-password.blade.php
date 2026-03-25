<x-auth-layout>
    <div class="min-h-screen bg-warm-white px-6 py-12">
        <div class="mx-auto w-full max-w-[420px]">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <div class="text-2xl font-semibold tracking-tight">Reset Kata Sandi</div>
                    <div class="mt-1 text-sm text-gray-600">Buat kata sandi baru untuk akun Anda.</div>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                        Simpan Kata Sandi Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-auth-layout>
