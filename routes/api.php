<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
    1. Registration & Profile
        o Users register with: name, email, password, brand_name, brand_description,
            and optional website.
        o Authenticate via session or API token.
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [UserController::class, 'show'])->name('user.show');
    /*
        2. Dashboard
            o Display metrics: total requests made, total saved posts, date/time of last
                generation, and list of recent saved posts (title and snippet).
    */
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard.show');

    /*
        3. Content Generation
            o Endpoint: POST /api/posts/generate
            o Request payload: { "topic": "<user-provided topic>" }
            o Response: { "options": [ { "title": "...", "content": "..." }, ... ] } (exactly 3 items)
    */
    Route::post('/posts/generate', function (Request $request) {
        //
        $topic = $request->input('topic');

        // TODO Generate
        $result = [
            'options' => [
                ['title' => 'Post 1', 'content' => 'This is social media post 1 '.$topic],
                ['title' => 'Post 2', 'content' => 'This is social media post 2 '.$topic],
                ['title' => 'Post 3', 'content' => 'This is social media post 3 '.$topic],
            ],
        ];

        return $result;
    });

    /*
        4. Post Management
            o Create: POST /api/posts saves a chosen option { "title": "...", "content": "..." }.
            o List: GET /api/posts returns all user’s saved posts.
            o View: GET /api/posts/{id} shows a single post.
            o Update: PUT /api/posts/{id} edits title or content.
            o Delete: DELETE /api/posts/{id} removes a post.
    */
    Route::resource('posts', PostController::class)->only('index', 'store', 'show', 'update', 'destroy');
});
