<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\SampleController;

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

// Routes Lang
Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');

// Route Backend
Route::get('admin/login', [LoginController::class, 'showAdminLoginForm'])->name('login');
Route::post('admin/login', [LoginController::class, 'adminLogin'])->name('admin.login');
Route::get('/admin/logout', [LoginController::class, 'adminLogout'])->name('admin.logout');

// ROUTES ADMIN

Route::middleware('auth:admin')->prefix('admin')->group( function () {

    // Route Roles
    Route::resource('roles', RoleController::class);

    // Route Dashboard
    Route::resource('dashboard', DashboardController::class);

    // Route Admins
    Route::resource('admins', AdminController::class);

    // Route Users
    Route::resource('users', UsersController::class);
});
