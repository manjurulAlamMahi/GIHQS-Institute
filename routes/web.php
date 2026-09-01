<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login'); //added redirect to login --- custom
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && in_array($user->role, ['admin', 'manager'])) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Frontend Catalogue route
Route::get('/catalogue', [\App\Http\Controllers\Frontend\CatalogueController::class, 'index'])->name('catalogue');

// custom route file
require __DIR__ . '/backend_farhad.php';
