<x-auth-layout>
    <div class="min-h-screen bg-warm-white">
        @if (session('toast'))
            <div class="fixed inset-x-0 top-4 z-50 px-4" x-data="{ open: true }" x-show="open" x-transition>
                <div class="mx-auto max-w-xl">
                    <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-white px-4 py-3 shadow-sm">
                        <div class="mt-1 h-2.5 w-2.5 flex-none rounded-full bg-red-500"></div>
                        <div class="flex-1 text-sm text-gray-800">{{ session('toast') }}</div>
                        <button type="button" class="text-sm font-medium text-gray-500 hover:text-gray-800" @click="open=false">Tutup</button>
                    </div>
                </div>
            </div>
        @endif

        <div class="mx-auto grid min-h-screen max-w-6xl grid-cols-1 lg:grid-cols-2">
            {{-- Left Panel — Illustrated --}}
            <div class="relative hidden overflow-hidden lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-600 to-brand-800"></div>
                <div class="relative flex h-full flex-col justify-between p-12 text-white">
                    {{-- Logo + Brand --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-white/15 p-2">
                                <img src="{{ asset('img/logo-mark.svg') }}" alt="" class="h-full w-full brightness-0 invert">
                            </div>
                            <div class="text-xl font-semibold tracking-tight">BagiPangan</div>
                        </div>
                        <div class="text-3xl font-semibold leading-tight">Bagi Makanan, Kurangi Pemborosan</div>
                        <div class="max-w-md text-white/85">
                            Platform distribusi makanan berlebih yang menghubungkan donatur dengan penerima di Indonesia.
                        </div>
                    </div>

                    {{-- Food Illustration --}}
                    <div class="relative mt-8 flex-1 flex items-center justify-center">
                        <img src="{{ asset('img/food-illustration.svg') }}" alt="" class="w-full max-w-sm opacity-80">
                    </div>

                    {{-- Decorative leaves --}}
                    <img src="{{ asset('img/leaf-deco.svg') }}" alt="" class="absolute -right-10 top-1/4 h-48 opacity-30">

                    {{-- Quote --}}
                    <div class="rounded-2xl bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-sm italic text-white/90">"Bersama kita kurangi pemborosan pangan dan bantu sesama."</p>
                    </div>
                </div>
            </div>

            {{-- Right Panel — Login Form --}}
            <div class="flex items-center justify-center px-6 py-10" x-data="loginForm()">
                <div class="w-full max-w-[400px]">
                    {{-- Mobile logo --}}
                    <div class="mb-8 flex justify-center lg:hidden">
                        <img src="{{ asset('img/logo.svg') }}" alt="BagiPangan" class="h-10">
                    </div>

                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Masuk</h1>
                    <p class="mt-1 text-sm text-gray-600">Selamat datang kembali di BagiPangan.</p>

                    {{-- Session Status --}}
                    @if(session('status'))
                        <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4" @submit="loading = true">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600"
                            />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="text-sm font-medium text-gray-700">Kata Sandi</label>
                            <div class="relative mt-1">
                                <input
                                    id="password"
                                    name="password"
                                    :type="showPass ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full rounded-xl border-gray-200 pr-12 shadow-sm focus:border-brand-600 focus:ring-brand-600"
                                />
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600" @click="showPass = !showPass">
                                    {{-- Eye open --}}
                                    <svg x-show="!showPass" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    {{-- Eye closed --}}
                                    <svg x-show="showPass" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-700">
                                <input id="remember_me" name="remember" type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-600" />
                                Ingat saya
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-800 hover:text-brand-600">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2 disabled:opacity-50"
                                :disabled="loading">
                            <svg x-show="loading" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"/>
                            </svg>
                            <span x-text="loading ? 'Memproses...' : 'Masuk'">Masuk</span>
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-gray-700">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-brand-800 hover:text-brand-600">Daftar sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function loginForm() {
        return { showPass: false, loading: false };
    }
    </script>
</x-auth-layout>
