<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-start mb-8">
        <div>
            <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
                Gerenciamento de Logs
            </h1>
            <p class="text-sm text-primary-600 mt-1">
                Visualize as principais operações realizadas no sistema.
            </p>
        </div>
    </div>

    <div>
        @livewire('logsTable')
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-primary-950/75 backdrop-blur-sm p-4 md:inset-0 h-modal md:h-full transition-opacity">

            <div class="relative w-full max-w-md h-full md:h-auto">

                <div class="relative bg-white rounded-xl shadow-2xl border border-primary-100">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between p-4 border-b border-primary-50 rounded-t">
                        <h3 class="text-xl font-bold text-primary-900">
                            Detalhes do Log
                        </h3>

                        <button wire:click="closeModal" type="button" class="text-primary-400 bg-transparent hover:bg-primary-50 hover:text-primary-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>


                    <div class="px-4 pt-2 pb-4 space-y-2">
                        <h3 class="text-md font-medium text-gray-500">
                            Model: {{ $model }}
                        </h3>

                        @if ($createdData)
                            <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 pt-2">
                                1. Dados Criados
                            </h4>
                            @foreach ($createdData as $key => $value)
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">
                                        {{ $key }}
                                    </label>
                                    <input type="text"
                                        value="{{ $value }}"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                </div>
                            @endforeach

                        @elseif ($deletedData)
                            <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 pt-2">
                                1. Dados Excluídos
                            </h4>
                            @foreach ($deletedData as $key => $value)
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-primary-700">
                                        {{ $key }}
                                    </label>
                                    <input type="text"
                                        value="{{ $value }}"
                                        class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                </div>
                            @endforeach

                        @elseif ($updatedData)
                            @foreach ($updatedData as $data)

                                @if ($loop->first)
                                    <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 pt-2">
                                        1. Dados Antigos
                                    </h4>
                                @else
                                    <h4 class="text-sm uppercase tracking-wide text-blue-600 font-bold mb-4 pt-2">
                                        2. Dados Novos
                                    </h4>
                                @endif

                                @foreach ($data as $key => $value)
                                    <div>
                                        <label class="block mb-1 text-sm font-medium text-primary-700">
                                            {{ $key }}
                                        </label>
                                        <input type="text"
                                            value="{{ $value }}"
                                            class="bg-primary-50 border border-primary-200 text-primary-900 text-sm rounded-lg focus:ring-secondary-500 focus:border-secondary-500 block w-full p-2.5">
                                    </div>
                                @endforeach

                                <hr class="mb-2">

                            @endforeach

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
