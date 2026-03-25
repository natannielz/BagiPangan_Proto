<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi akun dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <div class="flex items-center gap-4">
                @php
                    $avatarUrl = null;
                    if ($user->avatar_path) {
                        $avatarUrl = URL::temporarySignedRoute('avatars.show', now()->addHour(), ['user' => $user->id]);
                    }
                @endphp

                <div class="h-16 w-16 overflow-hidden rounded-2xl bg-gray-100">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" loading="lazy" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M4 22C4 17.5817 7.58172 14 12 14C16.4183 14 20 17.5817 20 22" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Foto Profil</div>
                    <div class="mt-1 text-sm text-gray-600">PNG/JPG/WebP, maks 5MB.</div>
                </div>
            </div>

            <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                @csrf
                <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-xl file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-gray-800 hover:file:bg-gray-200" />
                <x-primary-button>Unggah</x-primary-button>
            </form>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            @if (session('status') === 'avatar-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="mt-2 text-sm text-gray-600"
                >Foto profil diperbarui.</p>
            @endif
        </div>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="'Nama Lengkap'" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Email Anda belum terverifikasi.

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-600 focus:ring-offset-2">
                            Kirim ulang email verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Tautan verifikasi baru sudah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="'No. Telepon'" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="city" :value="'Kota'" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" required autocomplete="address-level2" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div>
            @php
                $businessLabel = $user->role === 'donor' ? 'Nama Usaha (Opsional)' : 'Nama Organisasi (Opsional)';
            @endphp
            <x-input-label for="business_name" :value="$businessLabel" />
            <x-text-input id="business_name" name="business_name" type="text" class="mt-1 block w-full" :value="old('business_name', $user->business_name)" autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
