<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                Gerenciamento de Clientes
            </h1>
            <p class="text-sm text-primary-600 mt-1">
                Visualize e gerencie a base de clientes da MJ Engenharia.
            </p>
        </div>

        <div>
            <button wire:click="openModal" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Cliente
            </button>
        </div>
    </div>

    <div>
        @livewire('client-table')
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            Novo Cliente
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <form wire:submit="save">

                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Nome Completo</label>
                                    <input type="text" wire:model="nome"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Ex: João da Silva">
                                    @error('nome') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">E-mail</label>
                                    <input type="email" wire:model="email"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="cliente@exemplo.com">
                                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-primary-700">Telefone</label>
                                        <input type="text" wire:model="telefone"
                                            class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                            placeholder="(00) 00000-0000"
                                            maxlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        @error('telefone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="flex items-center justify-end p-6 space-x-2 border-t border-primary-50 rounded-b bg-gray-50/50">

                        <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                            Cancelar
                        </button>

                        <button wire:click="save" type="button" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50" wire:loading.attr="disabled">

                            <span wire:loading.remove wire:target="save">Salvar</span>
                            <span wire:loading wire:target="save">Salvando...</span>

                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
