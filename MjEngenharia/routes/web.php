<?php

use App\Livewire\Inicio;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

// ---------- GRUPO DE ROTAS PÚBLICAS ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// ---------- GRUPO DE ROTAS PROTEGIDAS ----------
Route::middleware('auth')->group(function () {
    Route::get('/inicio', Inicio::class)->name('inicio');
});
