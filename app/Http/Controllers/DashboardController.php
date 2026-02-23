<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show(Request $request): JsonResponse
    {
        $posts = $request->user()->posts;
        $logs = $request->user()->openAILogs;

        return response()->json([
            'total_requests' => $logs->count(),
            'total_saved_posts' => $posts->count(),
            'last_generation_time' => $posts->last()->created_at,
            'token_usage' => $logs->sum('token_usage'),
            'posts' => $posts,
        ], 200);
    }
}
