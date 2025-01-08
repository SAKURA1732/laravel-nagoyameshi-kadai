<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RegularHolidayController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

// 一般ユーザー用ルート
Route::group(['middleware' => 'guest:admin'], function () {
    // トップページ
    Route::get('/', [HomeController::class, 'index'])->name('home');
     // 会員情報編集ページ
    Route::get('/user/edit', [UserController::class, 'edit'])->name('user.edit');
    
    // 会員情報更新機能
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');
});

// 認証ルート
require __DIR__.'/auth.php';

// 管理者用ルート
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    // ホームページ
    Route::get('home', [AdminHomeController::class, 'index'])->name('home');

    // 会員管理
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

    // 店舗管理
    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
    Route::get('restaurants/{restaurant}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
    Route::patch('restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('restaurants.update');
    Route::delete('restaurants/{restaurant}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

    // カテゴリ管理
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // 定休日管理
    Route::get('regular-holidays', [RegularHolidayController::class, 'index'])->name('regular_holidays.index');
    Route::get('regular-holidays/create', [RegularHolidayController::class, 'create'])->name('regular_holidays.create');
    Route::post('regular-holidays', [RegularHolidayController::class, 'store'])->name('regular_holidays.store');
    Route::get('regular-holidays/{regularHoliday}', [RegularHolidayController::class, 'show'])->name('regular_holidays.show');
    Route::get('regular-holidays/{regularHoliday}/edit', [RegularHolidayController::class, 'edit'])->name('regular_holidays.edit');
    Route::patch('regular-holidays/{regularHoliday}', [RegularHolidayController::class, 'update'])->name('regular_holidays.update');
    Route::delete('regular-holidays/{regularHoliday}', [RegularHolidayController::class, 'destroy'])->name('regular_holidays.destroy');

    // 会社概要管理
    Route::get('company', [CompanyController::class, 'index'])->name('company.index');
    Route::get('company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('company', [CompanyController::class, 'update'])->name('company.update');

    // 利用規約管理
    Route::get('terms', [TermController::class, 'index'])->name('terms.index');
    Route::get('terms/edit', [TermController::class, 'edit'])->name('terms.edit');
    Route::patch('terms', [TermController::class, 'update'])->name('terms.update');
});