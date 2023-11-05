<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\RatingController       ;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SaveJobController;
use App\Http\Controllers\NotifController;

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
    Route::get('auth/find_jobs', [JobController::class, 'find_jobs']);    
    Route::get('auth/filter', [JobController::class, 'filter']);
    Route::get('auth/get_education_jobs_desc', [JobController::class, 'get_education_jobs_desc']);
    Route::get('auth/get_education_jobs_asc', [JobController::class, 'get_education_jobs_asc']);

    Route::get('auth/save_job', [SaveJobController::class, 'save_job']);
    Route::get('auth/unsave_job', [SaveJobController::class, 'unsave_job']);
    Route::get('auth/get_saved_jobs', [SaveJobController::class, 'get_saved_jobs']);

    Route::post('auth/add_cv', [CVController::class, 'add_cv']);

    Route::get('auth/img_slider', [SliderController::class, 'get_img_slider']);
    Route::post('auth/add_sliders', [SliderController::class, 'add_sliders']);

    Route::post('auth/add_rating', [RatingController::class, 'add_rating']);
    Route::get('auth/get_rating', [RatingController::class, 'get_rating']);

    Route::post('auth/addcart', [CartController::class, 'addcart']);
    Route::get('auth/getcarts', [CartController::class, 'get_carts']);

    Route::post('auth/add_favorite', [FavoriteController::class, 'add_favorite']);
    Route::get('auth/get_favorites', [FavoriteController::class, 'get_favorites']);

    Route::post('auth/send_notif', [NotifController::class, 'send_notif']);

    Route::post('auth/get_consultans', [ConsultantController::class, 'get_consultans']);

    Route::get('send-email', [SendEmailController::class, 'index']);
});
