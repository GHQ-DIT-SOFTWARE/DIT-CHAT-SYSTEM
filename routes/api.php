<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BioController;;


Route::post('/fingerprint', [BioController::class, 'upload']);
Route::get('/fingerprints', [BioController::class, 'index']);
