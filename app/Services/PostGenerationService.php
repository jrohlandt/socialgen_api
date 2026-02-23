<?php

namespace App\Services;

// use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Factory as Http;

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

    public function __construct()
    {
        $this->http = new Http();
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

    public function generate(string $topic): void
    {
        $this->response = $this->http->withToken($this->apiKey)
            ->post($this->endpoint, $this->createBody($topic));

        if ($this->response->failed()) {
            // e.g. rate limits, invalid schema
            $this->errorMessage = $this->response->json('error.message');
            $this->statusCode = $this->response->status();
            return;
        }

        $content = $this->response->json('output.0.content.0.text');
        $this->posts = json_decode($content, true)['suggestions'];
    }

    public function failed(): bool
    {
        return $this->response->failed();
    }

    protected function createBody(string $topic): array
    {
        return [
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

    }
}
