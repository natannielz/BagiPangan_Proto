<div x-data="toastManager()" @toast.window="addToast($event.detail)" class="fixed right-4 top-4 z-50 flex flex-col gap-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-8 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-8 opacity-0"
             class="flex items-start gap-3 rounded-xl border bg-white px-4 py-3 shadow-lg"
             :class="toast.type === 'error' ? 'border-red-200' : 'border-brand-200'">
            <div class="mt-0.5 h-2 w-2 flex-none rounded-full"
                 :class="toast.type === 'error' ? 'bg-red-500' : 'bg-brand-600'"></div>
            <p class="flex-1 text-sm text-gray-800" x-text="toast.message"></p>
            <button @click="dismiss(toast.id)" class="text-xs font-medium text-gray-400 hover:text-gray-600">&times;</button>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        addToast({ type = 'success', message = '' }) {
            const id = Date.now();
            this.toasts.push({ id, type, message, visible: true });
            setTimeout(() => this.dismiss(id), 3500);
        },
        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) {
                t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 200);
            }
        }
    };
}
</script>

@if(session('success'))
<script>
window.addEventListener('DOMContentLoaded', () =>
    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '{{ addslashes(session('success')) }}' } })));
</script>
@endif
@if(session('error'))
<script>
window.addEventListener('DOMContentLoaded', () =>
    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '{{ addslashes(session('error')) }}' } })));
</script>
@endif
@if(session('status'))
<script>
window.addEventListener('DOMContentLoaded', () =>
    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: '{{ addslashes(session('status')) }}' } })));
</script>
@endif
