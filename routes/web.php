<?php

use App\Http\Controllers\AiAdvisorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// homepage
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');

// Goals
Route::get('/goals', [GoalController::class, 'index'])->middleware('auth');
// buat goals jadi functional
Route::middleware(['auth'])->group(function () {
    Route::get('/goals', [GoalController::class, 'index']); // Ini dipakai juga untuk filtering
    Route::post('/goals', [GoalController::class, 'store']);
    Route::get('/goals/{id}/edit', [GoalController::class, 'edit']);
    Route::post('/goals/{id}/update', [GoalController::class, 'update']);
    Route::post('/goals/{id}/delete', [GoalController::class, 'destroy']);
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

// Transaction
Route::middleware(['auth'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit']);
    Route::post('/transactions/{id}/update', [TransactionController::class, 'update']);
    Route::post('/transactions/{id}/delete', [TransactionController::class, 'destroy']);
});

// AI-Advisor
Route::get('/advisor', [AiAdvisorController::class, 'index']);
