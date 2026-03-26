<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 to-brand-900 p-6 text-white sm:p-8">
    {{-- Decorative circles --}}
    <div class="absolute -right-6 -top-6 h-32 w-32 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-4 right-20 h-20 w-20 rounded-full bg-white/5"></div>

    <div class="relative z-10 flex items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold sm:text-2xl">
                Selamat Datang, {{ auth()->user()->name }}
            </h2>
            <p class="mt-1 text-sm text-white/70">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            @if(isset($stats))
                <div class="mt-4 flex flex-wrap gap-4">
                    @foreach($stats as $stat)
                        <div class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm">
                            <span class="text-lg font-bold">{{ $stat['value'] }}</span>
                            <span class="ml-1 text-xs text-white/70">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 3D Bowl illustration --}}
        <div class="hidden flex-none sm:block">
            <img src="{{ asset('img/bowl-3d.svg') }}" alt="" class="h-28 w-28 lg:h-36 lg:w-36">
        </div>
    </div>
</div>
