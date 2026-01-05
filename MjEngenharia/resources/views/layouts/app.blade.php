<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <div class="flex h-screen overflow-hidden">

            <aside class="w-64 bg-primary-950 text-white flex-col hidden md:flex">

                <div class="h-16 flex items-center justify-center border-b border-primary-800 bg-primary-900">
                    <h1 class="text-xl font-bold">MJ Engenharia</h1>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

                    {{-- NAVIGATE OPTIONS --}}
                    <a href="/" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('/') ? 'bg-secondary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        <x-heroicon-o-home class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Início</span>
                    </a>

                    <a href="/clientes" wire:navigate
                        class="flex items-center px-4 py-3 rounded-lg transition-colors
                            {{ request()->is('clientes') ? 'bg-secondary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        <x-heroicon-o-users class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-md">Clientes</span>
                    </a>

                </nav>

                <div class="p-4 border-t border-primary-800 bg-primary-950 shrink-0">
                    <a href="{{ route('logout') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary-200 hover:bg-red-500/10 hover:text-red-400 transition-colors group">
                        <span class="font-semibold text-md">Logout</span>
                    </a>
                </div>

            </aside>

            <div class="flex-1 flex flex-col h-screen overflow-y-auto">

                <header class="md:hidden bg-primary-900 text-white shadow p-4 flex justify-start items-center">

                    <span class="font-bold text-lg">MJ Engenharia</span>

                </header>

                <main class="p-6">

                    {{ $slot }}

                </main>

            </div>

        </div>
    </body>
</html>
