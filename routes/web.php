<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'service' => 'mooda-be',
    'docs' => url('/api/documentation'),
]));

// App API-only: tidak ada halaman login. Route bernama 'login' ini hanya agar
// fallback bawaan Laravel untuk request tak-terautentikasi tidak melempar
// "Route [login] not defined". Selalu balas 401 JSON.
Route::get('/login', fn () => response()->json(['message' => 'Unauthenticated.'], 401))
    ->name('login');
