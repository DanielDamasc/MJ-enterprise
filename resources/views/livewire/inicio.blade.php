<div class="w-full">
    {{-- Cabeçalho da Página --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-primary-900 tracking-tight">
            Visão Geral
        </h1>
        <p class="text-sm text-primary-600 mt-1">
            Acompanhe os principais indicadores do sistema.
        </p>
    </div>

    {{-- Grid de Cards --}}
    {{-- mobile: 1 coluna | tablet: 2 colunas | desktop: 3 colunas --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

        {{-- CARD 1: COLABORADORES --}}
        <a  href="{{ route('colaboradores') }}"
            wire:navigate
            class="block relative overflow-hidden bg-white rounded-xl border border-gray-100 p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total de Colaboradores
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->totalEmployees }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <x-heroicon-s-briefcase class="h-6 w-6" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-green-500"></div>
        </a>

        {{-- CARD 2: CLIENTES --}}
        <a  href="{{ route('clientes') }}"
            wire:navigate
            class="block relative overflow-hidden bg-white rounded-xl border border-gray-100 p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total de Clientes
                    </p>
                    {{-- Usando computed property: $this->totalClients --}}
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->totalClients }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    {{-- Ícone (Requer blade-heroicons ou similar) --}}
                    <x-heroicon-s-users class="h-6 w-6" />
                </div>
            </div>
            {{-- Barra decorativa inferior --}}
            <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-500"></div>
        </a>


        {{-- CARD 3: EQUIPAMENTOS --}}
        <a  href="{{ route('ar-condicionados') }}"
            wire:navigate
            class="block relative overflow-hidden bg-white rounded-xl border border-gray-100 p-6 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total de Ar-condicionados
                    </p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $this->totalACs }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                    {{-- <x-heroicon-o-wrench-screwdriver class="h-6 w-6" /> --}}
                    <x-ionicon-snow-outline class="w-6 h-6" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-orange-500"></div>
        </a>

    </div>
</div>
