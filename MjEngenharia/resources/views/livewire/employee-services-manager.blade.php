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
                        </div>
                        <span class="px-2 py-1 text-xs font-bold uppercase tracking-wide text-blue-800 bg-blue-100 rounded-full">
                            {{ $service->status }}
                        </span>
                    </div>

                    <div class="p-4 flex-grow space-y-4">

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
                                            <span class="font-bold text-gray-700">{{ $ac->marca ? $ac->marca : 'Marca N/A' }}</span>
                                            <span class="text-xs text-gray-500 block">{{ $ac->ambiente }}</span>
                                        </div>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="border-b border-gray-200"></div>
                                    @endif
                                @endforeach

                                {{-- Se tiver mais que 3, mostra o botão "Ver todos" --}}
                                @if($service->airConditioners->count() > 3)
                                    <div class="pt-1 text-center">
                                        <button wire:click="showEquipments({{ $service->id }})"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline w-full py-1">
                                            + Ver outros {{ $service->airConditioners->count() - 3 }} equipamentos
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

                        <button wire:click="concluirService({{ $service->id }})" class="flex items-center justify-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition shadow-sm text-sm font-bold">
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
                        @foreach($selectedService->airConditioners as $ac)
                            <div class="p-4 bg-white flex items-start hover:bg-gray-50 transition">
                                {{-- Checkbox Visual (apenas visual para ajudar o tecnico) --}}
                                <div class="flex-shrink-0 mt-1 mr-3">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>
                                </div>

                                <div class="flex-grow">
                                    <div class="flex justify-between">
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $ac->marca ? $ac->marca : 'Marca N/A' }}</h4>
                                        <span class="text-sm font-mono font-bold text-gray-600">
                                            {{ $ac->codigo_ac }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-semibold text-blue-600 rounded text-xs uppercase">
                                            {{ $ac->ambiente }}
                                        </span>
                                        • {{ $ac->potencia }} BTUs • {{ $ac->modelo ? $ac->modelo : 'Modelo N/A' }}
                                    </p>
                                    @if($ac->pivot->valor > 0)
                                        <p class="text-sm text-green-600 mt-1">Valor Unitário: <strong>R$ {{ $ac->pivot->valor }}</strong></p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- Rodapé do Modal --}}
                <div class="p-4 border-t border-gray-100 flex justify-between items-center bg-gray-50">
                    <span class="text-sm font-bold text-gray-600">
                        Qtd: {{ $selectedService->airConditioners->count() }}
                    </span>

                    {{-- Botão para já concluir direto do modal se quiser --}}
                    <button wire:click="concluirService({{ $selectedService->id }})"
                            wire:confirm="Concluir todos os {{ $selectedService->airConditioners->count() }} itens?"
                            class="text-white bg-secondary-500 hover:bg-secondary-600 focus:ring-4 focus:outline-none focus:ring-secondary-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                        Concluir Serviço
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
