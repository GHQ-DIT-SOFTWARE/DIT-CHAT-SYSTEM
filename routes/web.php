<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BioController;

Route::get('/', function () {
    return view('welcome');
});

 //biometric route
 Route::get('/my-bio', [BioController::class, 'index'])->name('myBio');
 Route::post('/fingerprint', [BioController::class, 'upload'])->name('myBio.upload');

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


    //template routes
    //Route::get('/chat/send', [MessageController::class, 'sendTest']);
    Route::get('/my-chat', [MessageController::class, 'myChat'])->name('myChat');
    Route::get('/chat/unread-counts', [MessageController::class, 'unreadCounts'])->name('chat.unreadCounts');

   

});

require __DIR__.'/auth.php';



Route::post('/fingerprint', function (\Illuminate\Http\Request $request) {
    // for testing, just dump JSON
    return response()->json([
        'received' => true,
        'size' => strlen($request->input('data', ''))
    ]);
});

