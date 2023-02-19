<?php

use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
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
    return Inertia::render('Homepage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,

        'products' => Product::select()->join('users', 'artist_id', '=', 'user_id')->get()

    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['products' => Product::select()->join('users', 'artist_id', '=', 'user_id')->get()]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth', 'verified', 'user-role:artist')->group(function () {
    Route::get('/artist/dashboard', function () {
        return Inertia::render('Artist/Dashboard', [
            'artworks' => Product::select()->where('artist_id', '=', auth()->user()->user_id)->get()
        ]);
    });
    Route::get('/artist/activities', function () {
        sleep(2);
        return Inertia::render('Artist/Activities');
    });
});

Route::get('/artworks/add', [ArtworkController::class, 'create']);


// Route::get('/email/verify', function () {
//     return view('auth.verify-email');
// })->middleware('auth')->name('verification.notice');


require __DIR__ . '/auth.php';
