<div class="min-h-screen flex items-center justify-center bg-primary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border-t-4 border-primary-800">
        
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-primary-900">
                Bem-vindo
            </h2>
            <p class="mt-2 text-sm text-primary-600">
                Acesse o painel da <span class="font-semibold text-primary-800">MJ Engenharia</span>
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit="login">
            <div class="-space-y-px">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-primary-700 mb-1">
                        Email
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        wire:model="email" 
                        autocomplete="email" 
                        required 
                        class="appearance-none relative block w-full px-3 py-2 border border-primary-200 placeholder-gray-400 text-primary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm transition duration-200" 
                        placeholder="seu@email.com"
                    >
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-primary-700 mb-1">
                        Senha
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        wire:model="password" 
                        autocomplete="current-password" 
                        required 
                        class="appearance-none relative block w-full px-3 py-2 border border-primary-200 placeholder-gray-400 text-primary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm transition duration-200" 
                        placeholder="••••••••"
                    >
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember-me"
                        name="remember-me" 
                        type="checkbox" 
                        wire:model="remember"
                        class="h-4 w-4 text-secondary-600 focus:ring-secondary-500 border-gray-300 rounded cursor-pointer"
                    >
                    <label for="remember-me" class="ml-2 block text-sm text-primary-700 cursor-pointer">
                        Lembrar-me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-secondary-600 hover:text-secondary-500 transition-colors">
                        Esqueceu sua senha?
                    </a>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-secondary-400 hover:bg-secondary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-400 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                >
                    <svg wire:loading wire:target="login" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <span wire:loading.remove wire:target="login">
                        Entrar
                    </span>
                    <span wire:loading wire:target="login">
                        Entrando...
                    </span>
                </button>
            </div>
        </form>
    </div>
    
    <div class="fixed bottom-4 text-center w-full text-xs text-primary-400">
        &copy; {{ date('Y') }} MJ Engenharia. Todos os direitos reservados.
    </div>
</div>