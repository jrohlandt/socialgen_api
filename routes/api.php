<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/do-test', function() {
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

/*
    2. Dashboard
        o Display metrics: total requests made, total saved posts, date/time of last
            generation, and list of recent saved posts (title and snippet).
*/
Route::get('/dashboard', function(Request $request) {
    return [
        'metrics' => [
            'total_requests' => 140,
            'total_saved_posts' => 25,
            'last_generation_time' => '2026-02-20 21:57:00', 
        ],
        'saved_posts' => [
            ['title' => 'Some title', 'content' => 'Watch out yall'],
            ['title' => 'Some Other Title', 'content' => 'Watch out yall part 2'],
        ]
    ];
});

Route::prefix('/posts/')->middleware('auth:sanctum')->group(function() {

    /*
        3. Content Generation
            o Endpoint: POST /api/posts/generate
            o Request payload: { "topic": "<user-provided topic>" }
            o Response: { "options": [ { "title": "...", "content": "..." }, ... ] } (exactly 3 items)
    */
    Route::post('/generate', function(Request $request) {
        // 
        $topic = $request->input('topic');
        // Generate
        $result = [
            'options' => [
                ['title' => 'Post 1', 'content' => 'This is social media post 1 ' . $topic],
                ['title' => 'Post 2', 'content' => 'This is social media post 2 ' . $topic],
                ['title' => 'Post 3', 'content' => 'This is social media post 3 ' . $topic],
            ]
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
    // Route::resource('/', PostController::class)->only('index', 'store', 'show', 'update', 'delete');
});    
