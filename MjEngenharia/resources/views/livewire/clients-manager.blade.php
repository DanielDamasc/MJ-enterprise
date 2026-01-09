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
            <button wire:click="openCreate" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Cliente
            </button>
        </div>
    </div>

    <div>
        @livewire('clientTable')
    </div>

    @if ($showCreate || $showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="flex items-center justify-between p-5 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            {{ $showCreate ? 'Novo Cliente' : 'Editar Cliente' }}
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit="{{ $showCreate ? 'save' : 'edit' }}">
                        <div class="p-6 space-y-6">

                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Nome do Cliente
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="cliente"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Ex: João da Silva">
                                    @error('cliente') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">Pessoa de Contato
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model="contato"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                        placeholder="Ex: João da Silva">
                                    @error('contato') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
                                        <label class="block mb-1 text-sm font-medium text-primary-700">Telefone de Contato
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="telefone"
                                            class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5"
                                            placeholder="(00) 00000-0000"
                                            maxlength="11"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        @error('telefone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
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
                            Tem certeza que deseja excluir este cliente?
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

    @if($showDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-primary-950/75 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-gray-50 rounded-xl shadow-2xl">

                {{-- CABEÇALHO --}}
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t shadow-sm">
                    <h3 class="text-xl font-bold text-primary-900">
                        Listagem de Ar-Condicionados
                    </h3>

                    <button wire:click="closeDetails" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- LISTA DE CARDS --}}
                <div class="p-6">
                    @if(count($equipmentList) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($equipmentList as $ac)
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col">

                                    {{-- Topo: Identificação --}}
                                    <div class="p-5 flex justify-between items-start border-b border-gray-50">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-50 text-blue-600 p-2 rounded-lg">
                                                <x-ionicon-snow-outline class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 text-lg">{{ $ac->codigo_ac }}</h4>
                                                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">{{ $ac->marca }} • {{ $ac->tipo }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-sm font-bold text-gray-700">{{ number_format($ac->potencia, 0, ',', '.') }}</span>
                                            <span class="text-xs text-gray-400">BTUs</span>
                                        </div>
                                    </div>

                                    {{-- Corpo: Localização --}}
                                    <div class="p-5 flex-1 space-y-4">
                                        <div class="flex items-start gap-3">
                                            <x-heroicon-o-map-pin class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" />
                                            <div>
                                                <span class="block text-sm font-medium text-gray-900">
                                                    {{ $ac->ambiente ?? 'Ambiente não informado' }}
                                                </span>
                                                @if($ac->address)
                                                    <span class="text-xs text-gray-500 block leading-relaxed mt-0.5">
                                                        {{ $ac->address->rua }}, {{ $ac->address->numero }} <br>
                                                        {{ $ac->address->bairro }} - {{ $ac->address->cidade }}/{{ $ac->address->uf }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Endereço não cadastrado</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Rodapé: Datas --}}
                                    <div class="px-5 py-3 bg-gray-50 rounded-b-xl border-t border-gray-100 flex justify-end items-center text-sm">
                                        <div class="flex flex-col text-right">
                                            <span class="text-xs text-gray-500">Próx. Higienização</span>
                                            <span class="font-medium text-blue-600">
                                                {{ \Carbon\Carbon::parse($ac->prox_higienizacao)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State (Sem equipamentos) --}}
                        <div class="text-center py-12 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-4 mb-3">
                                <x-heroicon-o-cube class="w-8 h-8 text-gray-400" />
                            </div>
                            <h3 class="text-base font-medium text-gray-900">Nenhum equipamento</h3>
                            <p class="text-sm text-gray-500">Este cliente não possui ar-condicionados vinculados.</p>
                        </div>
                    @endif
                </div>

                {{-- Botão Fechar --}}
                <div class="p-6 border-t border-gray-200 bg-white rounded-b-xl flex justify-end sticky bottom-0 z-10">
                    <button wire:click="closeDetails" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
