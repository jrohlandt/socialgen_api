<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as Http;
use App\Models\OpenAILog;
use App\DTOs\PostSuggestionDTO;

class PostGenerationService
{
    public $response;
    protected string $apiKey;
    protected string $endpoint;
    protected Http $http;
    public $posts;
    public $errorMessage;
    public $statusCode;
    protected array $outputSchema;
    protected string $model = 'gpt-4o-mini-2024-07-18';
    private OpenAILog $openAILog;

    public function __construct()
    {
        $this->http = new Http();
        $this->openAILog = new OpenAILog();

        $this->apiKey = config('services.openai.api_key');
        $this->endpoint = 'https://api.openai.com/v1/responses';
        $this-> outputSchema = [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'social_media_post_suggestions',
                    'schema' => [
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
                    ],
                    'strict' => true
                ]
            ];
    }

    public function generate(int $userId, string $topic): void
    {
        $body = [
            'model' => $this->model,
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
            'text' => $this->outputSchema,
        ];

        $this->response = $this->http->withToken($this->apiKey)->post($this->endpoint, $body);

        if ($this->response->failed()) {
            $this->errorMessage = $this->response->json('error.message');
            $this->statusCode = $this->response->status();
            return;
        }

        $content = $this->response->json('output.0.content.0.text');
        $this->createOpenAILog($userId, $body, $content);
        $this->posts = PostSuggestionDTO::collection(json_decode($content, true));

    }

    public function failed(): bool
    {
        return $this->response->failed();
    }

    private function createOpenAILog(int $userId, $input, $output)
    {
        $this->openAILog->create([
            'user_id' => $userId,
            'model' => $this->model,
            'input' => json_encode($input),
            'output' => $output,
            'token_usage' => $this->response->json('usage.total_tokens'),
        ]);
    }
}
