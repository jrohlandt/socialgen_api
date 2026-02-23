<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\GeneratePostSuggestionsRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(['posts' => $request->user()->posts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $request->user()->posts()->create($request->validated());
        return response()->json(['post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        if ($request->user()->cannot('view', $post)) {
            abort(403);
        }

        return response()->json(['post' => $post], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        $post->update($request->validated());

        return response()->json(['post' => $post], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post): Response
    {
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        $post->delete();

        return response()->noContent();
    }

    /**
     * Generate social media post suggestions.
     */
    public function generate(GeneratePostSuggestionsRequest $request, PostGenerationService $service): JsonResponse
    {
        $service->generate($request->validated('topic'));

        if ($service->failed()) {
            return response()->json(['message' => $service->errorMessage], $service->statusCode);
        }

        return response()->json(['options' => $service->posts], 201);
    }
}
