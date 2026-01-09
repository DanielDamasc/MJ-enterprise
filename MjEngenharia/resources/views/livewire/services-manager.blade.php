<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                Gerenciamento de Ordens de Serviço
            </h1>
            <p class="text-sm text-primary-600 mt-1">
                Visualize e gerencie as ordens de serviço da MJ Engenharia.
            </p>
        </div>

        <div>
            <button wire:click="openCreate" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-secondary-500 hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all">
                <x-heroicon-o-plus class="w-5 h-5 mr-1"/>
                Novo Serviço
            </button>
        </div>
    </div>

    <div>
        @livewire('servicesTable')
    </div>

    @if ($showCreate || $showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-5xl max-h-[90vh] overflow-y-auto bg-white rounded-xl shadow-2xl transform transition-all">

                {{-- HEADER --}}
                <div class="px-6 pt-4 flex justify-between items-center sticky top-0 bg-white z-10 border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-bold text-primary-900">
                        {{ $showCreate ? 'Nova Ordem de Serviço' : 'Editar Ordem de Serviço' }}
                    </h3>
                    <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="{{ $showCreate ? 'save' : 'edit' }}" class="p-6 space-y-8">

                    {{-- SEÇÃO 1: QUEM E ONDE --}}
                    <div>
                        <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b pb-2">
                            1. Cliente e Equipamentos
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                            {{-- CLIENTE --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente
                                    <span class="text-red-500">*</span>
                                </label>
                                @if ($showEdit)
                                    <div class="h-10 bg-gray-100 border border-gray-200 rounded-lg flex items-center px-3 text-gray-600 cursor-not-allowed select-none">
                                        {{ $cliente_label }}
                                    </div>
                                    <small class="text-xs text-gray-500 mt-1">O cliente não pode ser alterado na edição.</small>
                                @else
                                    <select wire:model.live="cliente_id" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                        <option value="">Selecione o cliente...</option>
                                        @foreach($clientes as $client)
                                            <option value="{{ $client->id }}">{{ $client->cliente }}</option>
                                        @endforeach
                                    </select>
                                    @error('cliente_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            {{-- EXECUTOR --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Técnico Responsável (Executor)
                                    <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="executor_id" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                    <option value="">Selecione o executor...</option>
                                    @foreach($executores as $executor)
                                        <option value="{{ $executor->id }}">{{ $executor->name }}</option>
                                    @endforeach
                                </select>
                                @error('executor_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- LISTA DE AR-CONDICIONADOS (Checkboxes) --}}
                            <div class="col-span-1 md:col-span-2 lg:col-span-12">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Equipamentos para o Serviço
                                    <span class="text-red-500">*</span>
                                </label>

                                @if(count($acs_disponiveis) > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 p-3 rounded-lg bg-gray-50">
                                        @foreach($acs_disponiveis as $ac)
                                            <label class="flex items-start space-x-3 p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 hover:shadow-sm transition-all group">
                                                <div class="flex items-center h-5">
                                                    <input type="checkbox" wire:model="ac_ids" value="{{ $ac->id }}"
                                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                        @disabled($showEdit)
                                                    >
                                                </div>
                                                <div class="text-sm">
                                                    <p class="font-bold text-gray-800 group-hover:text-blue-700 transition-colors">{{ $ac->codigo_ac }}</p>
                                                    <p class="text-gray-600 text-xs mt-0.5">{{ $ac->ambiente }} • {{ $ac->marca }} • {{ $ac->potencia }} BTUs</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 text-center">
                                        @if($cliente_id)
                                            <x-heroicon-s-x-circle class="w-8 h-8 text-gray-400 mb-2" />
                                            <p class="text-sm text-gray-600">
                                                Este cliente não possui equipamentos cadastrados.
                                            </p>
                                        @else
                                            <x-ionicon-snow-outline class="w-8 h-8 text-gray-400 mb-2" />
                                            <p class="text-sm text-gray-600">
                                                Selecione um cliente acima para carregar a lista.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                                @error('ac_ids') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SEÇÃO 2: DADOS DO SERVIÇO --}}
                    <div>
                        <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 border-b pb-2">
                            2. Detalhes do Serviço
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">

                            {{-- TIPO --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Serviço
                                    <span class="text-red-500">*</span>
                                </label>
                                @if ($showEdit)
                                    <div class="h-10 bg-gray-100 border border-gray-200 rounded-lg flex items-center px-3 text-gray-600 cursor-not-allowed select-none">
                                        {{ $tipo_label }}
                                    </div>
                                    <small class="text-xs text-gray-500 mt-1">O cliente não pode ser alterado na edição.</small>
                                @else
                                    <select wire:model.live="tipo" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                        <option value="">Selecione...</option>
                                        <option value="higienizacao">Higienização</option>
                                    </select>
                                    @error('tipo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            {{-- DATA --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Realização
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="data_servico" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('data_servico') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- VALOR --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Unitário (R$)
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" wire:model="valor" placeholder="0.00" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                @error('valor') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- STATUS --}}
                            <div class="col-span-1 md:col-span-1 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Atual
                                    <span class="text-red-500">*</span>
                                </label>
                                @if ($showEdit)
                                    <div class="h-10 bg-gray-100 border border-gray-200 rounded-lg flex items-center px-3 text-gray-600 cursor-not-allowed select-none">
                                        {{ $status_label }}
                                    </div>
                                    <small class="text-xs text-gray-500 mt-1">O status não pode ser alterado na edição.</small>
                                @else
                                    <select wire:model="status" class="h-10 bg-gray-50 border border-gray-300 rounded-lg outline-none w-full focus:border-blue-500 focus:ring-blue-500 shadow-sm px-3">
                                        @foreach($statusServico as $_)
                                            <option value="{{ $_->value }}">{{ $_->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            {{-- CAMPOS DINÂMICOS (JSON) --}}
                            @if($tipo)
                                <div class="col-span-1 md:col-span-2 lg:col-span-12 mt-2">
                                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 animate-fade-in-down">
                                        <h5 class="text-sm font-bold text-blue-800 mb-3 flex items-center">
                                            <x-heroicon-o-clipboard-document-list class="w-4 h-4 mr-2"/>
                                            Checklist: {{ ucfirst($tipo) }}
                                        </h5>

                                        @if($tipo === 'higienizacao')
                                            <div class="flex items-center">
                                                <input id="chk-cond" type="checkbox" wire:model="detalhes.limpou_condensadora" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                <label for="chk-cond" class="ml-2 text-sm font-medium text-gray-900 cursor-pointer select-none">
                                                    Limpeza da Condensadora
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" wire:click="closeModal" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                            <span wire:loading.remove wire:target="{{ $showCreate ? 'save' : 'edit' }}">{{ $showCreate ? 'Criar' : 'Salvar Alterações' }}</span>
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
                            Tem certeza que deseja excluir este serviço?
                        </h3>

                        <p class="text-sm text-gray-800 mb-6">
                            Essa ação não pode ser desfeita.
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

    @if ($showConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">
                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    <div class="p-6 text-center">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 p-3 text-green-600">
                            <x-heroicon-o-check-circle class="w-8 h-8" />
                        </div>

                        <h3 class="mb-5 text-lg font-normal text-gray-600">
                            Deseja concluir o serviço?
                        </h3>

                        <p class="text-sm text-gray-800 mb-6">
                            Essa ação mudará o status de forma definitiva, e não pode ser revertida.
                        </p>

                        <div class="flex justify-center gap-3">

                            <button wire:click="closeModal" type="button" class="text-primary-600 bg-white hover:bg-primary-50 focus:ring-4 focus:outline-none focus:ring-primary-100 rounded-lg border border-primary-200 text-sm font-medium px-5 py-2.5 hover:text-primary-900 focus:z-10 transition-colors">
                                Cancelar
                            </button>

                            <button wire:click="serviceDone" type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md">
                                Concluir
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
