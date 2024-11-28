<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;


Route::apiResource('hello', HelloWorldController::class);


use App\Http\Controllers\JsonController;

// Rutas para JSON
Route::get('/json', [JsonController::class, 'index']);
Route::post('/json', [JsonController::class, 'store']);
Route::get('/json/{filename}', [JsonController::class, 'show']);
Route::put('/json/{filename}', [JsonController::class, 'update']);
Route::delete('/json/{filename}', [JsonController::class, 'destroy']);

use App\Http\Controllers\CsvController;

// Rutas para CSV
Route::get('/csv', [CsvController::class, 'index']);
Route::post('/csv', [CsvController::class, 'store']);
Route::get('/csv/{filename}', [CsvController::class, 'show']);
Route::put('/csv/{filename}', [CsvController::class, 'update']);
Route::delete('/csv/{filename}', [CsvController::class, 'destroy']);