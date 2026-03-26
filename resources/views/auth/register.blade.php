<x-auth-layout>
    <div class="min-h-screen bg-warm-white px-6 py-10">
        <div class="mx-auto w-full max-w-3xl">
            <div class="mb-8 flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm font-medium text-brand-800 hover:text-brand-600">← Kembali ke Masuk</a>
                <div class="text-sm text-gray-600">Pendaftaran BagiPangan</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" x-data="{
                step: {{ old('role') ? 2 : 1 }},
                role: @js(old('role')),
                emailTaken: false,
                _emailTimeout: null,
                checkEmail(e) {
                    clearTimeout(this._emailTimeout);
                    this._emailTimeout = setTimeout(async () => {
                        const email = e.target.value.trim();
                        if (!email || !email.includes('@')) { this.emailTaken = false; return; }
                        try {
                            const res = await fetch('/api/check-email', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''},
                                body: JSON.stringify({email})
                            });
                            const data = await res.json();
                            this.emailTaken = !data.available;
                        } catch { this.emailTaken = false; }
                    }, 400);
                }
            }">
                <div class="mb-6">
                    <div class="text-2xl font-semibold tracking-tight">Daftar Akun</div>
                    <div class="mt-1 text-sm text-gray-600">Lengkapi data berikut untuk mulai berbagi.</div>
                </div>

                <div class="mb-6 flex items-center gap-2 text-sm">
                    <div class="flex items-center gap-2">
                        <div :class="step === 1 ? 'bg-brand-600 text-white' : 'bg-gray-200 text-gray-700'" class="flex h-7 w-7 items-center justify-center rounded-full font-semibold">1</div>
                        <div class="font-medium text-gray-800">Pilih Peran</div>
                    </div>
                    <div class="h-px flex-1 bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div :class="step === 2 ? 'bg-brand-600 text-white' : 'bg-gray-200 text-gray-700'" class="flex h-7 w-7 items-center justify-center rounded-full font-semibold">2</div>
                        <div class="font-medium text-gray-800">Data Akun</div>
                    </div>
                </div>

                <div x-show="step === 1" x-transition>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <button type="button" dusk="role-donor" class="relative rounded-2xl border bg-white p-5 text-left shadow-sm transition hover:shadow-md" :class="role === 'donor' ? 'border-brand-600 ring-2 ring-brand-600/20' : 'border-gray-200'" @click="role='donor'">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="h-10 w-10 rounded-xl bg-brand-50 p-2 text-brand-800">
                                    <svg viewBox="0 0 24 24" class="h-full w-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 12H20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        <path d="M6.5 12L8.2 18.8C8.35 19.4 8.9 19.8 9.52 19.8H14.48C15.1 19.8 15.65 19.4 15.8 18.8L17.5 12" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                        <path d="M8 10.2C8 7.9 9.9 6 12.2 6C14.5 6 16.4 7.9 16.4 10.2V12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="h-6 w-6 rounded-full border border-gray-200 bg-white flex items-center justify-center" :class="role === 'donor' ? 'border-brand-600' : ''">
                                    <svg x-show="role === 'donor'" viewBox="0 0 20 20" class="h-4 w-4 text-brand-600" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.4 7.4a1 1 0 01-1.42 0l-3.3-3.3a1 1 0 011.42-1.42l2.59 2.59 6.69-6.69a1 1 0 011.42 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-base font-semibold text-gray-900">Donatur</div>
                            <div class="mt-1 text-sm text-gray-600">Bagikan makanan berlebih dari usaha/rumah.</div>
                        </button>

                        <button type="button" class="relative rounded-2xl border bg-white p-5 text-left shadow-sm transition hover:shadow-md" :class="role === 'receiver' ? 'border-brand-600 ring-2 ring-brand-600/20' : 'border-gray-200'" @click="role='receiver'">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="h-10 w-10 rounded-xl bg-sage-50 p-2 text-sage-800">
                                    <svg viewBox="0 0 24 24" class="h-full w-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 21C16.4 21 20 17.4 20 13C20 8.6 16.4 5 12 5C7.6 5 4 8.6 4 13C4 17.4 7.6 21 12 21Z" stroke="currentColor" stroke-width="1.6"/>
                                        <path d="M8.5 13.3L10.7 15.5L15.5 10.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="h-6 w-6 rounded-full border border-gray-200 bg-white flex items-center justify-center" :class="role === 'receiver' ? 'border-brand-600' : ''">
                                    <svg x-show="role === 'receiver'" viewBox="0 0 20 20" class="h-4 w-4 text-brand-600" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.4 7.4a1 1 0 01-1.42 0l-3.3-3.3a1 1 0 011.42-1.42l2.59 2.59 6.69-6.69a1 1 0 011.42 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-base font-semibold text-gray-900">Penerima</div>
                            <div class="mt-1 text-sm text-gray-600">Klaim donasi untuk individu/komunitas.</div>
                        </button>

                        <div class="relative rounded-2xl border border-gray-200 bg-gray-50 p-5 text-left">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="h-10 w-10 rounded-xl bg-gray-200 p-2 text-gray-600">
                                    <svg viewBox="0 0 24 24" class="h-full w-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 3L20 7V17L12 21L4 17V7L12 3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                        <path d="M12 12V21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="rounded-full bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-700">Hanya via undangan</div>
                            </div>
                            <div class="text-base font-semibold text-gray-500">Admin</div>
                            <div class="mt-1 text-sm text-gray-500">Kelola platform, moderasi dan laporan.</div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 disabled:cursor-not-allowed disabled:opacity-50" :disabled="!role" @click="step=2">
                            Lanjut
                        </button>
                    </div>
                </div>

                <div id="step2" x-show="step === 2" x-transition>
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="role" :value="role">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="name" class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" @blur="checkEmail($event)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                <p x-show="emailTaken" x-cloak class="mt-1 text-sm text-red-600" id="email-taken-msg">Email sudah terdaftar</p>
                            </div>

                            <div>
                                <label for="phone" class="text-sm font-medium text-gray-700">No. Telepon</label>
                                <input id="phone" name="phone" type="text" autocomplete="tel" value="{{ old('phone') }}" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div>
                                <label for="city" class="text-sm font-medium text-gray-700">Kota</label>
                                <input id="city" name="city" type="text" autocomplete="address-level2" value="{{ old('city') }}" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2" x-show="role === 'donor'" x-transition>
                                <label for="business_name_donor" class="text-sm font-medium text-gray-700">Nama Usaha (Opsional)</label>
                                <input id="business_name_donor" name="business_name" type="text" value="{{ old('business_name') }}" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2" x-show="role === 'receiver'" x-transition>
                                <label for="business_name_receiver" class="text-sm font-medium text-gray-700">Nama Organisasi (Opsional)</label>
                                <input id="business_name_receiver" name="business_name" type="text" value="{{ old('business_name') }}" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="password" class="text-sm font-medium text-gray-700">Kata Sandi</label>
                                <input id="password" name="password" type="password" autocomplete="new-password" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-600 focus:ring-brand-600" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button" class="text-sm font-medium text-gray-600 hover:text-gray-900" @click="step=1">← Ubah peran</button>
                            <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>
