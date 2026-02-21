<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/do-test', function () {
    return 'something';
});

/*
    1. Registration & Profile
        o Users register with: name, email, password, brand_name, brand_description,
            and optional website.
        o Authenticate via session or API token.
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    /*
        2. Dashboard
            o Display metrics: total requests made, total saved posts, date/time of last
                generation, and list of recent saved posts (title and snippet).
    */
    Route::get('/dashboard', function (Request $request) {
        $posts = $request->user()->posts;

        return [
            'metrics' => [
                'total_requests' => 140, // TODO
                'total_saved_posts' => $posts->count(),
                'last_generation_time' => $posts->last()->created_at,
            ],
            'posts' => $posts,
        ];
    });

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
    Route::resource('posts', PostController::class)->only('index', 'store');

    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->name('posts.show')
        ->middleware('can:view,post');

    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->name('posts.update')
        ->middleware('can:update,post');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('can:delete,post');
});
