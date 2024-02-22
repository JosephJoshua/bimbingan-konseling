<?php

use App\Http\Controllers\ConsultationCategoryController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('students', StudentController::class);
    Route::resource('consultation-categories', ConsultationCategoryController::class);
    Route::resource('events', EventController::class);
    Route::resource('consultations', ConsultationController::class)->except('create', 'store');
    Route::resource('students.consultations', ConsultationController::class)->only('create', 'store');

    Route::post('/consultations/image', [ConsultationController::class, 'uploadImage'])->name('consultations.upload-image');
    Route::post('/events/image', [EventController::class, 'uploadImage'])->name('events.upload-image');
});

require __DIR__ . '/auth.php';
