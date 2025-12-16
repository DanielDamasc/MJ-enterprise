<?php

use App\Livewire\Inicio;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/inicio', Inicio::class);