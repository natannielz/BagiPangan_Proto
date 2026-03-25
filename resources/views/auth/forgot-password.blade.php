<x-auth-layout>
    <div class="min-h-screen bg-warm-white px-6 py-12">
        <div class="mx-auto w-full max-w-[420px]">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-2 text-2xl font-semibold tracking-tight">Lupa Kata Sandi</div>
                <div class="mb-6 text-sm text-gray-600">
                    Masukkan email Anda. Kami akan mengirim tautan untuk reset kata sandi.
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                        Kirim Tautan Reset
                    </button>
                </form>
            </div>

            <div class="mt-5 text-center text-sm text-gray-700">
                <a href="{{ route('login') }}" class="font-semibold text-brand-800 hover:text-brand-600">Kembali ke Masuk</a>
            </div>
        </div>
    </div>
</x-auth-layout>
