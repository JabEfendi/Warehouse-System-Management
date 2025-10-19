<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MasterDataController;

Route::get('/signin', fn() => view('signin', ['title' => 'Sign In']))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/signup', fn() => view('signup', ['title' => 'Sign Up']))->name('signup');
Route::post('/register', [AuthController::class, 'regist'])->name('register');

// hanya user login yang boleh ke homepage
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => view('homepage', ['title' => 'Home Page']))->name('homepage');
    
    Route::get('/masterdata', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('masterdata');
    Route::get('/api/users',  [MasterDataController::class, 'index'])->name('api.users');
    Route::post('/api/users/{id}/status', [MasterDataController::class, 'updateStatus']);
    Route::get('/api/users/{user}', [MasterDataController::class, 'show']);

    Route::view('/inbound', 'inbound', ['title' => 'Inbound']);
    Route::view('/inventory', 'inventory', ['title' => 'Inventory']);
    Route::view('/outbound', 'outbound', ['title' => 'Outbound']);
    Route::view('/report', 'report', ['title' => 'Reports']);
});
