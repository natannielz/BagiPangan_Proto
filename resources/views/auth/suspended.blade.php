<x-auth-layout>
    <div class="min-h-screen bg-warm-white px-6 py-12">
        <div class="mx-auto max-w-xl">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="mt-1 h-10 w-10 rounded-xl bg-red-50 p-2 text-red-700">
                        <svg viewBox="0 0 24 24" class="h-full w-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 9V13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M12 17H12.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            <path d="M10.2 4.8H13.8L21 19.2H3L10.2 4.8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-semibold tracking-tight">Akun Dinonaktifkan Sementara</div>
                        <div class="mt-2 text-sm text-gray-700">
                            Akun Anda sedang dinonaktifkan sementara oleh admin. Jika Anda merasa ini kesalahan, silakan hubungi tim BagiPangan.
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                                Kembali ke Halaman Masuk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
