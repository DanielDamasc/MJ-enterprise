<?php

use App\Livewire\ForgotPassword;
use App\Livewire\Inicio;
use App\Livewire\Login;
use App\Livewire\ResetPassword;
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
    Route::get('/inicio', Inicio::class)->name('inicio');

    Route::get('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
