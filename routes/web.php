<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


require __DIR__.'/auth.php';

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::controller(RestaurantController::class)->group(function () {
    Route::get('/admin/restaurants/index', 'index')->name('admin.restaurants.index');
    Route::get('/admin/restaurants/show/{restaurant}', 'show')->name('admin.restaurants.show');
    Route::get('/admin/restaurants/edit/{restaurant}', 'edit')->name('admin.restaurants.edit');
    Route::get('/admin/restaurants/create', 'create')->name('admin.restaurants.create');
    Route::post('/admin/restaurants/store', 'store')->name('admin.restaurants.store');
    Route::patch('/admin/restaurants/show/{restaurant}', 'update')->name('admin.restaurants.update');
    Route::delete('/admin/restaurants/{restaurant}', 'destroy')->name('admin.restaurants.destroy');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('users', [UserController::class, 'index'])->name('users.index'); // 会員一覧ページ
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show'); // 会員詳細ページ
    Route::resource('restaurants', RestaurantController::class);
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('categories', Admin\CategoryController::class);

});

Route::prefix('admin/company')->group(function () {
    Route::get('/index', [CompanyController::class, 'index'])->name('admin.company.index');
    Route::get('/edit', [CompanyController::class, 'edit'])->name('admin.company.edit');
    Route::patch('/edit', [CompanyController::class, 'update'])->name('admin.company.update');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('companies', Admin\CompanyController::class);
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('terms', Admin\TermController::class);
});