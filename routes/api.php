<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SendEmailController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [UserController::class, 'register']);

Route::middleware('jwt.verify')->group(function () {


    Route::post('auth/logout', [UserController::class, 'logout']);
    Route::get('auth/getprofile', [UserController::class, 'getprofile']);
    Route::post('auth/editprofile', [UserController::class, 'editprofile']);
    Route::get('auth/refresh', [UserController::class, 'refresh']);

    Route::post('auth/add_job', [JobController::class, 'add_job']);
    Route::get('auth/get_jobs', [JobController::class, 'get_jobs']);
    Route::get('auth/update_job', [JobController::class, 'update_job']);
    Route::get('auth/delete_job', [JobController::class, 'delete_job']);
    Route::get('auth/find_job', [JobController::class, 'find_job']);    
    Route::get('auth/get_condition_job', [JobController::class, 'get_condition_job']);

    Route::post('auth/add_cv', [CVController::class, 'add_cv']);

    Route::get('auth/img_slider', [SliderController::class, 'get_img_slider']);
    Route::post('auth/add_sliders', [SliderController::class, 'add_sliders']);

    Route::post('auth/addreview', [ReviewController::class, 'addreview']);

    Route::post('auth/addcart', [CartController::class, 'addcart']);
    Route::get('auth/getcarts', [CartController::class, 'get_carts']);

    Route::post('auth/add_favorite', [FavoriteController::class, 'add_favorite']);
    Route::get('auth/get_favorites', [FavoriteController::class, 'get_favorites']);

    Route::get('send-email', [SendEmailController::class, 'index']);
});
