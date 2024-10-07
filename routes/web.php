<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/different-account', [App\Http\Controllers\HomeController::class, 'differentAccount'])->name('different-account');
Route::get('/reset-auth', [App\Http\Controllers\HomeController::class, 'resetAuth'])->name('reset-auth');

// Route untuk ClientController
Route::post('/clients', [ClientController::class, 'createClient'])->name('clients.create');

// Jika perlu, tambahkan route untuk melihat daftar client
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

// Middleware auth untuk melindungi route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [ClientController::class, 'index'])->name('home');
    Route::post('/clients', [ClientController::class, 'createClient'])->name('clients.create');
    Route::get('/clients/data', [ClientController::class, 'getClients'])->name('clients.data'); // Server-side processing
    Route::delete('/clients/{id}', [ClientController::class, 'deleteClient']);
    Route::get('/clients/{id}/edit', [ClientController::class, 'editClient']);
    Route::put('/clients/{id}', [ClientController::class, 'updateClient']);

});
