<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Mail\VerificationEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
    return view('welcome');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/do_login',[AuthController::class,'login'])->name('do_login');
Route::post('/save_user',[AuthController::class,'register'])->name('save_user');
Route::get('/register',[AuthController::class,'showregister'])->name('register');
Route::get('/user_profile',[UserProfileController::class,'show'])->name('user_profile');

// Send Email
Route::get('/verify_email/{token}/{password}',[AuthController::class,'activeUser'])->name('verify_email');
Route::post('/resendEmail',[AuthController::class,'resendEmail'])->name('resendEmail');



// Reset Password
Route::get('/showResetPassword',[AuthController::class,'showResetPassword'])->name('showResetPassword');
Route::post('/resetPassword',[AuthController::class,'resetPassword'])->name('resetPassword');
Route::get('/verify_password/{token}',[AuthController::class,'formPassword'])->name('verify_password');
Route::post('/new_password',[AuthController::class,'newPassword'])->name('new_password');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/users_list',[UserController::class,'showAllUsers'])->name('users_list');
});