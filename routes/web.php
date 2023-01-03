<?php

use App\Http\Controllers\NomineeController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VoterController;
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

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});


Route::middleware('auth')->group(function() {

    Route::controller(PollController::class)->group(function() {
       Route::get('poll','index')->name('dashboard');
       Route::post('poll.store','store')->name('poll.store');
       Route::post('poll.delete','destroy')->name('poll.destroy');
       Route::post('poll.update', 'update')->name('poll.update');
    });

    Route::controller(NomineeController::class)->group(function() {
        Route::get('poll/{poll}/nominees','index')->name('poll.nominees');
        Route::post('nominee.store','store')->name('nominee.store');
        Route::post('nominee.delete','destroy')->name('nominee.destroy');
        Route::post('nominee.update', 'update')->name('nominee.update');
    });

    Route::controller(VoteController::class)->group(function() {
        Route::get('poll/{poll}/votes','index')->name('poll.votes');
    });

    Route::controller(VoterController::class)->group(function() {
       Route::get('voters','index')->name('voters');
       Route::post('voters/reset-password','resetPassword')->name('voters.reset-password');
       Route::post('voters/import-list','importVotersList')->name('voters.import-list');
        Route::post('voters/purge-list','purgeVotersList')->name('voters.purge-list');
    });

    Route::controller(PositionController::class)->group(function() {
        Route::get('positions','index')->name('positions');
        Route::post('position.store','store')->name('position.store');
        Route::post('position.delete','destroy')->name('position.destroy');
        Route::post('position.update', 'update')->name('position.update');
    });

    Route::controller(UserController::class)->group(function() {
        Route::get('users','index')->name('users');
        Route::post('user.store','store')->name('user.store');
        Route::post('user.delete','destroy')->name('user.destroy');
        Route::post('user.update', 'update')->name('user.update');
        Route::post('user.reset-password','resetPassword')->name('user.reset-password');
    });
});

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

require __DIR__.'/auth.php';
