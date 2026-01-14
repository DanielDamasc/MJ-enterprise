<?php

use App\Livewire\AirConditionersManager;
use App\Livewire\ClientsManager;
use App\Livewire\EmployeeManager;
use App\Livewire\EmployeeServicesManager;
use App\Livewire\ForgotPassword;
use App\Livewire\Inicio;
use App\Livewire\Login;
use App\Livewire\LogsManager;
use App\Livewire\ResetPassword;
use App\Livewire\ServicesManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------- GRUPO DE ROTAS PÚBLICAS ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');

    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// ---------- GRUPO DE ROTAS PROTEGIDAS ----------
Route::middleware('auth')->group(function () {

    Route::get('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // ----- ROTAS DO ADMIN -----
    Route::group(['middleware' => ['role:adm']], function () {
        Route::get('/', Inicio::class);
        Route::get('/executores', EmployeeManager::class)->name('executores');
        Route::get('/clientes', ClientsManager::class)->name('clientes');
        Route::get('/ar-condicionados', AirConditionersManager::class)->name('ar-condicionados');
        Route::get('/servicos', ServicesManager::class)->name('servicos');
        Route::get('/logs', LogsManager::class)->name('logs');
    });

    // ----- ROTAS DO EXECUTOR -----
    Route::group(['middleware' => ['role:executor']], function () {
        Route::get('/servicos-executor', EmployeeServicesManager::class)->name('servicos-executor');
    });
});
