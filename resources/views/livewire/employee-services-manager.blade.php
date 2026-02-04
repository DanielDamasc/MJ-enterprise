<div>
    <div class="p-4 space-y-4">
        <h2 class="text-xl font-bold text-gray-800">Meus Agendamentos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            @forelse($services as $service)
                {{-- Variáveis auxiliares --}}
                @php
                    $firstAC = $service->airConditioners->first();
                    $address = $firstAC?->address;
                    $numACs = $service->airConditioners->count();
                @endphp

                <div class="bg-white rounded-lg shadow-md border-t-4 border-blue-600 overflow-hidden flex flex-col">

                    <div class="bg-gray-50 p-3 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center text-gray-700 font-bold">
                            <x-heroicon-o-clock class="w-5 h-5 mr-1 text-blue-500" />
                            {{ \Carbon\Carbon::parse($service->data_servico)->format('d/m/Y') }}
                            @if ($service->horario)
                                - {{ \Carbon\Carbon::parse($service->horario)->format('H:i') }}
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-bold uppercase tracking-wide text-blue-800 bg-blue-100 rounded-full">
                            {{ $service->status }}
                        </span>
                    </div>

                    <div class="p-4 flex-grow space-y-4">

                        <div class="flex items-center pb-4 border-b border-gray-100 mb-2">
                            <div class="p-2 bg-blue-50 rounded-lg mr-3">
                                <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Serviço</p>
                                <h3 class="text-lg font-extrabold text-gray-800 capitalize leading-none">
                                    {{ $service->tipo_label }}
                                </h3>
                            </div>
                        </div>

                        @if ($address)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <x-heroicon-o-map-pin class="w-5 h-5 text-red-500" />
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-bold text-gray-900 uppercase mb-1">
                                        ENDEREÇO:
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $address->rua }}, {{ $address->numero }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $address->bairro }}
                                        - {{ $address->cidade }}/{{ $address->uf }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center text-orange-500 text-sm">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5 mr-2"/>
                                Endereço não encontrado nos equipamentos.
                            </div>
                        @endif

                        <hr class="border-gray-100">

                        {{-- LISTA DE EQUIPAMENTOS --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="text-xs font-bold text-gray-500 uppercase">
                                    Equipamentos
                                </h4>
                                <span class="text-xs font-bold bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                                    Qtd: {{ $service->airConditioners->count() }}
                                </span>
                            </div>

                            <div class="bg-gray-50 rounded-lg border border-gray-100 p-2 space-y-2">

                                {{-- Mostra apenas os 3 primeiros --}}
                                @foreach($service->airConditioners->take(3) as $ac)
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="truncate pr-2">
                                            <span class="font-bold text-gray-700">{{ $ac->marca ? $ac->marca : 'N/A' }}</span>
                                            <span class="text-xs text-gray-500 block">{{ $ac->ambiente ? $ac->ambiente : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="border-b border-gray-200"></div>
                                    @endif
                                @endforeach

                                {{-- Se tiver mais que 1, mostra o botão "Ver todos" --}}
                                @if($service->airConditioners->count() > 1)
                                    <div class="pt-1 text-center">
                                        <button wire:click="showEquipments({{ $service->id }})"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline w-full py-1">
                                            @if ($service->airConditioners->count() > 3)
                                                + Ver outros {{ $service->airConditioners->count() - 3 }} equipamentos
                                            @else
                                                + Ver lista de equipamentos
                                            @endif
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Cliente</p>
                                <p class="font-medium text-gray-800">{{ $service->client->cliente }}</p>
                                <p class="text-sm text-blue-600 flex items-center mt-1">
                                    <x-heroicon-o-phone class="w-4 h-4 mr-1"/> {{ $service->client->telefone }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 uppercase font-bold">Total</p>
                                <p class="text-lg font-bold text-green-600">
                                    R$ {{ $service->total }}
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="bg-gray-50 p-3 grid grid-cols-2 gap-3 border-t border-gray-100">
                        @if ($address)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($address->endereco) }}"
                            target="_blank"
                            class="flex items-center justify-center bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-100 transition shadow-sm text-sm font-bold">
                                Abrir no Maps
                            </a>
                        @else
                            <button disabled class="opacity-50 cursor-not-allowed flex items-center justify-center bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded text-sm font-bold">
                                Sem Endereço
                            </button>
                        @endif

                        <button wire:click="concluirTotal({{ $service->id }})" class="flex items-center justify-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition shadow-sm text-sm font-bold">
                            Concluir Serviço
                        </button>
                    </div>

                </div>

                @empty
                <div class="col-span-full flex flex-col items-center justify-center p-10 text-center bg-white rounded-lg border border-dashed border-gray-300">
                    <p class="text-gray-500 text-lg">Nenhum serviço agendado.</p>
                </div>
            @endforelse

        </div>
    </div>

    {{-- MODAL DE LISTAGEM DE EQUIPAMENTOS --}}
    @if($showEquipmentsModal && $selectedService)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm transition-opacity">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Cabeçalho do Modal --}}
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-bold text-primary-900">Lista de Equipamentos</h3>
                        <p class="text-sm text-gray-500">
                            Cliente: <strong>{{ $selectedService->client->cliente }}</strong>
                        </p>
                    </div>
                    <button wire:click="closeEquipments" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                {{-- Corpo do Modal com Scroll --}}
                <div class="p-0 overflow-y-auto flex-1 bg-gray-50">

                    {{-- Agrupamento opcional: Se quiser agrupar visualmente --}}
                    <div class="divide-y divide-gray-200">
                        @foreach($selectedService->airConditioners->sortBy('codigo_ac', SORT_NATURAL) as $ac)
                            <label wire:key="ac-{{ $ac->id }}"
                                    class="p-4 bg-white flex items-start hover:bg-gray-50 transition
                                        {{ in_array($ac->id, $selectedEquipmentsIds) ? 'ring-1 ring-blue-500' : 'opacity-60 hover:opacity-100' }}">
                                {{-- Checkbox para conclusão parcial --}}
                                <div class="flex-shrink-0 mt-1 mr-3">
                                    <input type="checkbox"
                                            value="{{ $ac->id }}"
                                            wire:click.stop="toggleEquipment({{ $ac->id }})"
                                            @checked(in_array($ac->id, $selectedEquipmentsIds))
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>

                                <div class="flex-grow select-none">
                                    <div class="flex justify-between">
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $ac->marca ? $ac->marca : 'N/A' }}</h4>
                                        <span class="text-sm font-mono font-bold text-gray-600">
                                            {{ $ac->codigo_ac }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold text-blue-600 rounded text-xs uppercase">
                                            {{ $ac->ambiente ? $ac->ambiente : 'N/A' }}
                                        </span>
                                        • {{ $ac->potencia }} BTUs • {{ $ac->modelo ? $ac->modelo : 'N/A' }}
                                    </p>
                                    @if($ac->pivot->valor > 0)
                                        <p class="text-sm text-green-600 mt-1">Valor Unitário: <strong>R$ {{ $ac->pivot->valor }}</strong></p>
                                    @elseif($ac->pivot->valor == 0)
                                        <p class="text-sm text-green-600 mt-1">Valor Unitário: <strong>R$ 0,00</strong></p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>

                </div>

                {{-- Rodapé do Modal --}}
                <div class="p-4 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500">
                            Resumo da Execução
                        </span>
                        <span class="font-bold text-gray-900 text-lg">
                            {{ count($selectedEquipmentsIds) }} <span class="text-sm font-normal text-gray-500">/{{ $selectedService->airConditioners->count() }} equipamentos</span>
                        </span>
                    </div>

                    {{-- Botão para já concluir direto do modal se quiser --}}
                    <button
                        wire:click="concluirParcial"
                        @if (count($selectedEquipmentsIds) == 0)
                            disabled
                        @endif
                        class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                            <div class="flex items-center justify-center w-full">
                                <x-heroicon-m-check class="w-5 h-5 mr-2"/>
                                @if(count($selectedEquipmentsIds) < $selectedService->airConditioners->count())
                                    Concluir Parcial
                                @else
                                    Concluir Total
                                @endif
                            </div>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showFinishModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">

                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex flex-col gap-1">
                        <h3 class="text-lg font-bold text-primary-900">Finalizar Serviço</h3>
                        <p class="text-sm text-gray-500">Confirme os detalhes da execução.</p>
                    </div>
                    <button wire:click="closeFinishModal" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Aviso Visual se for parcial --}}
                    @if($isPartial)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 font-bold">
                                        Conclusão Parcial
                                    </p>
                                    <p class="text-sm text-yellow-600 mt-1">
                                        Apenas os equipamentos selecionados serão considerados. Ao clicar em confirmar, a ordem de serviço será finalizada <strong>parcialmente</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Observações do Técnico <span class="text-gray-400 font-normal">(Opcional)</span>
                        </label>
                        <textarea
                            wire:model="observacoes_executor"
                            rows="4"
                            class="w-full bg-blue-50 border-blue-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-gray-400 text-sm p-3"
                            placeholder="Adicione observações relevantes sobre o serviço antes de concluir..."
                        ></textarea>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeFinishModal"
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm font-bold hover:bg-gray-50 transition">
                        Cancelar
                    </button>

                    <button wire:click="finalizarServico"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 shadow-md flex items-center transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="finalizarServico">Confirmar e Finalizar</span>
                        <span wire:loading wire:target="finalizarServico">Processando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
