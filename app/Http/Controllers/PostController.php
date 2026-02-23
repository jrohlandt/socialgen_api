<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(['posts' => $request->user()->posts]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = $request->user()->posts()->create($request->validated());
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

        $post->update($request->validated());

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

    public function generate(Request $request)
    {
        $topic = $request->input('topic');

        $apiKey = config('services.openai.api_key');
        $endpoint = 'https://api.openai.com/v1/responses';

        $response = Http::withToken($apiKey)
            ->post($endpoint, self::createBody($topic))
            ->throw();

        if ($response->failed()) {
            // handle errors e.g. rate limits, invalid schema
            return $response->json('error.message');
        }

        $content = $response->json('output.0.content.0.text');
        $structuredOutput = json_decode($content, true);

        return response()->json($structuredOutput, 201);
    }

    public static function createBody(string $topic)
    {
        $outputSchema = [
            'type' => 'object',
            'properties' => [
                'suggestions' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'content' => ['type' => 'string'],
                        ],
                        'required' => ['title', 'content'],
                        'additionalProperties' => false,
                    ]
                ],
            ],
            'required' => ['suggestions'],
            'additionalProperties' => false
        ];

        return [
            'model' => 'gpt-4o-mini-2024-07-18', // gpt-5-nano cheaper but not as good as gpt-4o-mini
            'input' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert social media content writer.',
                ],
                [
                    'role' => 'user',
                    'content' => 'You will be provided with the topic for a social media post
                        and you should generate exactly 3 social media posts based on that topic and return only the title and the content of each post
                        nothing else.
                        The topic for the social media post is: ' . $topic,
                ]
            ],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'social_media_post_suggestions',
                    'schema' => $outputSchema,
                    'strict' => true
                ]
            ]
        ];

    }
}
