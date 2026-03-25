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
            <div class="relative hidden overflow-hidden lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-600 to-brand-800"></div>
                <div class="relative flex h-full flex-col justify-between p-12 text-white">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-white/15 p-2">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="h-full w-full" fill="none">
                                    <path d="M6 10.5C6 7.46243 8.46243 5 11.5 5C14.5376 5 17 7.46243 17 10.5V12" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
                                    <path d="M4.5 12H19.5L18.2 18.2C18.0355 18.9569 17.3659 19.5 16.5904 19.5H7.40958C6.63412 19.5 5.96447 18.9569 5.8 18.2L4.5 12Z" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                                    <path d="M9 8.2C9.6 7.5 10.5 7 11.5 7C12.5 7 13.4 7.5 14 8.2" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="text-xl font-semibold tracking-tight">BagiPangan</div>
                        </div>
                        <div class="text-3xl font-semibold leading-tight">Bagi Makanan, Kurangi Pemborosan</div>
                        <div class="max-w-md text-white/85">
                            Platform distribusi makanan berlebih yang menghubungkan donatur dengan penerima di Indonesia.
                        </div>
                    </div>

                    <div class="opacity-75">
                        <svg viewBox="0 0 520 260" xmlns="http://www.w3.org/2000/svg" class="w-full" fill="none">
                            <path d="M85 130C85 79.1898 126.19 38 177 38H343C393.81 38 435 79.1898 435 130V156C435 200.183 399.183 236 355 236H165C120.817 236 85 200.183 85 156V130Z" stroke="white" stroke-width="4"/>
                            <path d="M116 110C135 94 162 84 191 84" stroke="white" stroke-width="4" stroke-linecap="round"/>
                            <path d="M404 110C385 94 358 84 329 84" stroke="white" stroke-width="4" stroke-linecap="round"/>
                            <path d="M210 70C210 52 228 42 240 30" stroke="white" stroke-width="4" stroke-linecap="round"/>
                            <path d="M260 70C260 52 278 42 290 30" stroke="white" stroke-width="4" stroke-linecap="round"/>
                            <path d="M310 70C310 52 328 42 340 30" stroke="white" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center px-6 py-10">
                <div class="w-full max-w-[400px]">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-6">
                            <div class="text-2xl font-semibold tracking-tight">Masuk</div>
                            <div class="mt-1 text-sm text-gray-600">Gunakan email dan kata sandi Anda.</div>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

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
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div x-data="{ show: false }">
                                <label for="password" class="text-sm font-medium text-gray-700">Kata Sandi</label>
                                <div class="relative mt-1">
                                    <input
                                        id="password"
                                        name="password"
                                        :type="show ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        required
                                        class="block w-full rounded-xl border-gray-200 pr-12 shadow-sm focus:border-brand-600 focus:ring-brand-600"
                                    />
                                    <button type="button" class="absolute inset-y-0 right-0 flex items-center px-3 text-sm font-medium text-gray-500 hover:text-gray-800" @click="show = !show">
                                        <span x-show="!show">Lihat</span>
                                        <span x-show="show">Sembunyi</span>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-700">
                                    <input id="remember_me" name="remember" type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-600" />
                                    Ingat saya
                                </label>

                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-800 hover:text-brand-600">
                                    Lupa kata sandi?
                                </a>
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                                Masuk
                            </button>
                        </form>
                    </div>

                    <div class="mt-5 text-center text-sm text-gray-700">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-brand-800 hover:text-brand-600">Daftar sekarang →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
