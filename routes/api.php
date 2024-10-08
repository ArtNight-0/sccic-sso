<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Api\Auth\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api', 'scope:view-user')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/logmeout', function (Request $request) {
    $user =  $request->user();
    $accessToken = $user->token();
    DB::table('oauth_refresh_tokens')
    ->where('access_token_id', $accessToken->id)
    ->delete();
    $user->token()->delete();


    return response()->json([
        'message' => 'Successfully logged out',
        'session' => session()->all()
    ]);
});

// MANAJEMEN USER
Route::get('/user-get', [UserController::class, 'getUser'])->name('user-get');
Route::get('/user-get-id/{id}', [UserController::class, 'getUserId'])->name('user-get-id');
Route::post('/user-post', [UserController::class, 'storeUser'])->name('user-post');
Route::put('/user-update/{id}', [UserController::class, 'updateUser'])->name('user-update');
Route::delete('/user-delete/{id}', [UserController::class, 'destroyUser'])->name('user-delete');
