@props([
    'message'     => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, lanjutkan',
    'cancelText'  => 'Batal',
])

<div
    x-data="{ open: false, formId: '' }"
    @confirm-action.window="open = true; formId = $event.detail.formId"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none"
    role="dialog"
    aria-modal="true"
>
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    <div class="relative bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4" style="min-height: 200px">
        <p class="text-gray-900 font-semibold mb-6">{{ $message }}</p>
        <div class="flex justify-end gap-3">
            <button
                type="button"
                @click="open = false"
                class="px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50"
            >
                {{ $cancelText }}
            </button>
            <button
                type="button"
                @click="open = false; document.getElementById(formId).submit()"
                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700"
            >
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>
