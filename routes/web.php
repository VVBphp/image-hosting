<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramApisController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/gallery', [ImagesController::class, 'index'])->name('images.index');
    Route::get('/upload', [ImagesController::class, 'create'])->name('images.create');
    Route::post('/upload', [ImagesController::class, 'store'])->name('images.store');
    Route::delete('/image/{image:code}', [ImagesController::class, 'destroy'])->name('images.destroy');
    Route::post('/image/{image:code}', [ImagesController::class, 'restore'])->name('images.restore')->withTrashed();

    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/users/{user}', [UserController::class, 'update'])->name('user.update');

    Route::put('/telegram', [TelegramApisController::class, 'store'])->name('telegram.credentials');

});

Route::get('/image/{image:code}', [ImagesController::class, 'image'])->name('images.download')->withTrashed();
Route::get('/{image:code}', [ImagesController::class, 'show'])->name('images.show')->withTrashed();
