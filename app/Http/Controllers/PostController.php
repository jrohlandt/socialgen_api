<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->posts;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $post = $request->user()->posts()->create($validated);

        return response()->json(['post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post)
    {
        if ($request->user()->cannot('view', $post)) {
            abort(403);
        }

        return ['post' => $post];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $validated = $request->validated();

        $post->update($validated);

        return ['post' => $post];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        $post->delete();

        return response()->noContent();
    }
}
