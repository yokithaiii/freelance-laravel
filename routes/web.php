<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs/create', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/manage', [JobController::class, 'manage'])->name('jobs.manage');
    Route::get('/jobs/edit/{id}', [JobController::class, 'edit'])->name('jobs.edit');
    Route::post('/jobs/edit/{id}', [JobController::class, 'update'])->name('jobs.update');
    Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/manage/{id}', [JobController::class, 'destroy'])->name('jobs.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/detail', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/detail', [ProfileController::class, 'updateDetails'])->name('profile.updateDetail');
});

Route::middleware('auth')->group(function () {
    Route::get('/user/{login}', [UserController::class, 'show'])->name('user.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/offers', [OfferController::class, 'index'])->name('offer.index');
    Route::get('/offers/notifications', [OfferController::class, 'offerNotifications'])->name('offer.offerNotifications');
    Route::get('/offer/job={id}', [OfferController::class, 'create'])->name('offer.create');
    Route::post('/offer/store', [OfferController::class, 'store'])->name('offer.store');
    Route::post('/offer/accept', [OfferController::class, 'offerAccept'])->name('offer.offerAccept');
    Route::post('/offer/decline', [OfferController::class, 'offerDecline'])->name('offer.offerDecline');
});

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{user}', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{login}', [ChatController::class, 'show'])->name('chat.show');
    Route::patch('/read_message', [ChatController::class, 'readMessage'])->name('chat.readMessage');
    Route::patch('/read_all_messages', [ChatController::class, 'readAllMessages'])->name('chat.readAllMessages');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
});

require __DIR__.'/auth.php';
