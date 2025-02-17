<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


//<------------  Authentication Api Starts ----------------->
Route::post('login', [AuthenticationController::class, 'login']);
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('resend-otp', [AuthenticationController::class, 'resendOtp']);
Route::post('verify-otp', [AuthenticationController::class, 'verifyOtp']);
Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword']);
Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);
//Route::get('send-mail', [MailController::class, 'index']);
//<------------  Authentication Api Ends ----------------->


//<------------  User Profile Api Starts ----------------->
Route::middleware(['jwt.verify'])->prefix('user/profile/')->group(function () {
    Route::get('/fetch', [UserController::class, 'getUserProfile']);
    Route::put('/update', [UserController::class, 'updateUserAndProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::delete('/delete-account', [UserController::class, 'deleteAccount']);
    Route::post('/logout', [UserController::class, 'logout']);
});
//<------------  User Profile Api Ends ----------------->



Route::middleware(['jwt.verify'])->prefix('posts/')->group(function () {
    Route::post('create', [PostController::class, 'store']);
    Route::get('fetch/all', [PostController::class, 'fetchAllPosts']);  // Get all post
    Route::get('single/{id}', [PostController::class, 'fetchPost']); // Get single post
    Route::put('update/{id}', [PostController::class, 'updatePost']); // Update a post
    Route::delete('delete/{id}', [PostController::class, 'deletePost']); // Delete a post
    Route::get('search', [PostController::class, 'searchPosts']); // Search & Filter posts
});



Route::middleware(['jwt.verify'])->prefix('user/')->group(function () {
    Route::post('follow/{userId}', [FollowController::class, 'followUser']);
    Route::delete('unfollow/{userId}', [FollowController::class, 'unfollowUser']);
    Route::get('followers', [FollowController::class, 'getFollowers']);
    Route::get('following', [FollowController::class, 'getFollowing']);

});

Route::middleware(['jwt.verify'])->prefix('comment/')->group(function () {

    Route::post('create', [CommentController::class, 'store']);
    Route::get('{postId}', [CommentController::class, 'fetchAllCommentsInPost']);
    Route::put('update/{id}', [CommentController::class, 'update']);  // Edit comment
    Route::delete('delete/{id}', [CommentController::class, 'destroy']);  // Delete comment
});
