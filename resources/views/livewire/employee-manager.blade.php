<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                Gerenciamento de Colaboradores
            </h1>
            <p class="text-sm text-primary-600 mt-1">
                Visualize e gerencie os colaboradores da MJ Engenharia.
            </p>
        </div>

        <div>
            <button wire:click="openCreate" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Colaborador
            </button>
        </div>
    </div>

    <div>
        @livewire('employeeTable')
    </div>

    @if ($showCreate || $showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            {{ $showCreate ? 'Novo Colaborador' : 'Editar Colaborador' }}
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit="{{ $showCreate ? 'save' : 'edit' }}">
                        <div class="p-6 space-y-6">

                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Nome do Colaborador
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="name"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Ex: João da Silva">
                                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">E-mail do Colaborador
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" wire:model="email"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="colaborador@exemplo.com">
                                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Perfil do Colaborador
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="perfil" class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                        <option value="">Selecione...</option>
                                        <option value="assistente">Assistente</option>
                                        <option value="executor">Executor</option>
                                    </select>
                                    @error('perfil') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end p-6 space-x-2 border-t border-primary-50 rounded-b bg-gray-50/50">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="{{ $showCreate ? 'save' : 'edit' }}" type="button" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50" wire:loading.attr="disabled">

                                <span wire:loading.remove wire:target="{{ $showCreate ? 'save' : 'edit' }}">Salvar</span>
                                <span wire:loading wire:target="{{ $showCreate ? 'save' : 'edit' }}">Salvando...</span>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($showDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">
                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="p-6 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 p-3 text-red-600">
                            <x-ionicon-warning-sharp class="w-8 h-8" />
                        </div>

                        <h3 class="mb-5 text-lg font-normal text-gray-600">
                            Tem certeza que deseja excluir este colaborador?
                        </h3>

                        <p class="text-sm text-gray-800 mb-6">
                            Essa ação não pode ser desfeita e removerá todos os dados vinculados.
                        </p>

                        <div class="flex justify-center gap-3">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="delete" type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                Sim, excluir
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
