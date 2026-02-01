<div class="min-h-screen flex items-center justify-center bg-primary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border-t-4 border-primary-800">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-primary-900">
                Recuperar Senha
            </h2>
            <p class="mt-2 text-sm text-primary-600 leading-relaxed">
                Informe seu email e enviaremos um link de redefinição de senha.
            </p>
        </div>

        <form wire:submit="sendResetLink" class="space-y-6">
            
            <div>
                <label for="email" class="block text-sm font-medium text-primary-700 mb-1">
                    Email cadastrado
                </label>
                <input 
                    id="email" 
                    type="email" 
                    wire:model="email" 
                    required 
                    autofocus
                    class="appearance-none block w-full px-3 py-2 border border-primary-200 placeholder-gray-400 text-primary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm transition duration-200" 
                    placeholder="exemplo@mjengenharia.com.br"
                >
                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button 
                type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-secondary-400 hover:bg-secondary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-400 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="sendResetLink">Enviar Link de Recuperação</span>
                <span wire:loading wire:target="sendResetLink">Enviando...</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-secondary-600 hover:text-secondary-800 transition-colors flex items-center justify-center gap-2">
                Voltar para o Login
            </a>
        </div>

    </div>
</div>