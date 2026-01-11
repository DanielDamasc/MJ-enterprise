<div class="p-4 space-y-4">
    <h2 class="text-xl font-bold text-gray-800">Meus Agendamentos</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        @forelse($services as $service)
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

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                             <x-heroicon-o-map-pin class="w-5 h-5 text-red-500" />
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-1">
                                ENDEREÇO:
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ $service->air_conditioner->address->rua }}, {{ $service->air_conditioner->address->numero }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $service->air_conditioner->address->bairro }}
                                - {{ $service->air_conditioner->address->cidade }}/{{ $service->air_conditioner->address->uf }}
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <x-heroicon-o-power class="w-5 h-5 text-gray-500"/>
                        </div>
                        <div class="ml-3 w-full">
                            <h4 class="text-sm font-bold text-gray-900 uppercase">Equipamento</h4>
                            <div class="grid grid-cols-2 gap-y-1 mt-1 text-sm text-gray-600">
                                <div><span class="font-medium text-gray-800">Marca:</span> {{ $service->air_conditioner->marca }}</div>
                                <div><span class="font-medium text-gray-800">Potência:</span> {{ $service->air_conditioner->potencia }} BTUs</div>
                                <div><span class="font-medium text-gray-800">Tipo:</span> {{ $service->air_conditioner->tipo }}</div>
                                <div><span class="font-medium text-gray-800">Local:</span> {{ $service->air_conditioner->ambiente }}</div>
                            </div>
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
                            <p class="text-xs text-gray-400 uppercase font-bold">Valor</p>
                            <p class="text-lg font-bold text-green-600">
                                R$ {{ $service->valor }}
                            </p>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 p-3 grid grid-cols-2 gap-3 border-t border-gray-100">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($service->air_conditioner->address->endereco) }}"
                       target="_blank"
                       class="flex items-center justify-center bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-100 transition shadow-sm text-sm font-bold">
                        Abrir no Maps
                    </a>

                    <button class="flex items-center justify-center bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition shadow-sm text-sm font-bold">
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
