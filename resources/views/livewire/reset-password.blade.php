<div class="min-h-screen flex items-center justify-center bg-primary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border-t-4 border-primary-800">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-primary-900">Nova Senha</h2>
            <p class="mt-2 text-sm text-primary-600">Defina sua nova senha de acesso.</p>
        </div>

        <form wire:submit="resetPassword" class="space-y-6">
            
            <input type="hidden" wire:model="token">

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1">Email</label>
                <input type="email" wire:model="email" required class="appearance-none block w-full px-3 py-2 border border-primary-200 rounded-md focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm text-primary-900">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1">Nova Senha</label>
                <input type="password" wire:model="password" required class="appearance-none block w-full px-3 py-2 border border-primary-200 rounded-md focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm text-primary-900">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-primary-700 mb-1">Confirmar Senha</label>
                <input type="password" wire:model="password_confirmation" required class="appearance-none block w-full px-3 py-2 border border-primary-200 rounded-md focus:ring-2 focus:ring-auxiliar-300 focus:border-secondary-400 sm:text-sm text-primary-900">
            </div>

            <button type="submit" class="w-full py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-secondary-400 hover:bg-secondary-500 shadow-md transition-all duration-200">
                Redefinir Senha
            </button>
        </form>
    </div>
</div>