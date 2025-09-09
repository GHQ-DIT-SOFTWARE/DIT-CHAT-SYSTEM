<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/chat', [MessageController::class, 'index'])->name('chat.index');
    Route::get('/chat/{userId}', [MessageController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [MessageController::class, 'send'])->name('chat.send');
});

require __DIR__.'/auth.php';
