<div
    x-data="{
        show: false,
        message: '',
        type: 'success', // 'success' ou 'error'

        init() {
            // Ouvinte para Sucesso
            Livewire.on('notify-success', (msg) => {
                this.showNotification(msg, 'success');
            });

            // Ouvinte para Erro
            Livewire.on('notify-error', (msg) => {
                this.showNotification(msg, 'error');
            });
        },

        showNotification(msg, type) {
            this.message = msg;
            this.type = type;
            this.show = true;

            // 1. Limpa o timer anterior se existir (impede fechamento prematuro)
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            // 2. Cria novo timer
            this.timeout = setTimeout(() => {
                this.show = false
            }, 5000);
        }
    }"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none"
>
    {{-- A Notificação em si --}}
    <div
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="pointer-events-auto w-full overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5"
        :class="type === 'success' ? 'bg-white border-l-4 border-green-500' : 'bg-white border-l-4 border-red-500'"
    >
        <div class="p-4">
            <div class="flex items-start">

                {{-- Ícone Sucesso --}}
                <div x-show="type === 'success'" class="flex-shrink-0">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-500" />
                </div>

                {{-- Ícone Erro --}}
                <div x-show="type === 'error'" class="flex-shrink-0">
                    <x-heroicon-o-x-circle class="w-6 h-6 text-red-500" />
                </div>

                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p x-text="type === 'success' ? 'Sucesso!' : 'Atenção!'" class="text-sm font-medium text-gray-900"></p>
                    <p x-text="message" class="mt-1 text-sm text-gray-500"></p>
                </div>

                {{-- Botão Fechar --}}
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
