<?php

use App\Livewire\Inicio;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class);

// ---------- AUTH CLASSES ----------
Route::get('/inicio', Inicio::class);