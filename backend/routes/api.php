<?php 

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/chat', [ChatController::class, 'ask'])->name('api.v1.chat.ask');
    Route::get('/chat', [ChatController::class, 'historyByUser'])->name('api.v1.chat.history');
    Route::get('/user', [ChatController::class, 'getUserName']);
    Route::post('/user', [ChatController::class, 'storeUserName']);

});

