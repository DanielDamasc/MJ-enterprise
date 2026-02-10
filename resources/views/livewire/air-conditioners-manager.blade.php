<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                Gerenciamento de Ar-condicionados
            </h1>
            <p class="text-sm text-primary-600 mt-1">
                Visualize e gerencie os equipamentos mantidos pela MJ Engenharia.
            </p>
        </div>

        <div>
            <button wire:click="openCreate" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Ar-condicionado
            </button>
        </div>
    </div>

    <div>
        @livewire('airConditioningTable')
    </div>

    @if($showCreate || $showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-5xl max-h-[90vh] overflow-y-auto bg-white rounded-xl shadow-2xl transform transition-all">

                <div class="px-6 pt-4 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h3 class="text-xl font-bold text-primary-900">
                        {{ $showCreate ? 'Cadastrar Ar-Condicionado' : 'Editar Informações do Ar-Condicionado' }}
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="{{ $showCreate ? 'save' : 'edit' }}" class="p-6 space-y-8">

                    <div>
                        <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b pb-2">
                            Dados do Equipamento
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                            <div class="col-span-1 md:col-span-2 lg:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente Responsável
                                    <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="cliente_id" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                    <option value="">Selecione um cliente...</option>
                                    @foreach($clientes as $client)
                                        <option value="{{ $client->id }}">{{ $client->cliente }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            @if ($showEdit)
                                <div class="col-span-1 md:col-span-1 lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Identificador
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="codigo_ac"
                                        readonly
                                        class="h-10 bg-gray-200 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3 bg-gray-50">
                                </div>
                            @endif

                            <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                                <input type="text" wire:model="modelo" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('modelo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                                <input type="text" wire:model="marca" placeholder="Ex: LG, Samsung" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('marca') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Potência (BTUs)
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="potencia" placeholder="Ex: 12000" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('potencia') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo
                                    <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="tipo" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                    <option value="">Selecione...</option>
                                    <option value="hw">HW</option>
                                    <option value="k7">K7</option>
                                    <option value="piso_teto">Piso-teto</option>
                                </select>
                                @error('tipo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Gás</label>
                                <input type="text" wire:model="tipo_gas" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('tipo_gas') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                @if ($showCreate)
                                    <div class="flex items-center mb-1 gap-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Próxima Higienização
                                        </label>

                                        <div class="relative group">
                                            <x-heroicon-o-question-mark-circle class="w-4 h-4 text-blue-400 cursor-help" />

                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-56 p-2 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 text-center pointer-events-none">

                                                Quando concluída uma ordem de serviço, o sistema calcula automaticamente a data da próxima higienização. Adicione agora apenas se julgar necessário.

                                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Próxima Higienização
                                    </label>
                                @endif
                                <input type="date" wire:model="prox_higienizacao" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('prox_higienizacao') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b pb-2 mt-8">
                            Local do Equipamento
                        </h4>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ambiente Específico</label>
                            <input type="text" wire:model="ambiente" placeholder="Ex: Sala de Reunião, Quarto Principal"
                                class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    CEP
                                    <span class="text-red-500">*</span>
                                    <span wire:loading wire:target="cep" class="text-xs text-blue-600 font-normal ml-2">
                                        Buscando...
                                    </span>
                                </label>
                                <input type="text" wire:model.blur="cep" placeholder="00000-000" maxlength="9"
                                    class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('cep') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rua / Logradouro
                                </label>
                                <input type="text" wire:model="rua"
                                    class="h-10 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3 bg-gray-50">
                                @error('rua') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número
                                </label>
                                <input type="text" wire:model="numero" placeholder="100"
                                    class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('numero') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro
                                </label>
                                <input type="text" wire:model="bairro"
                                    class="h-10 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3 bg-gray-50">
                                @error('bairro') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                                <input type="text" wire:model="complemento" placeholder="Apto. 201"
                                    class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="cidade"
                                    class="h-10 bg-gray-200 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3 bg-gray-50" readonly>
                                @error('cidade') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">UF
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="uf" maxlength="2"
                                    class="h-10 bg-gray-200 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3 bg-gray-50 uppercase" readonly>
                                @error('uf') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" wire:click="closeModal" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                            <span wire:loading.remove wire:target="{{ $showCreate ? 'save' : 'edit' }}">Salvar Registro</span>
                            <span wire:loading wire:target="{{ $showCreate ? 'save' : 'edit' }}" class="flex items-center">
                                Salvando...
                            </span>
                        </button>
                    </div>

                </form>
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
                            Tem certeza que deseja excluir este equipamento?
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
