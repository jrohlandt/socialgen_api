<?php

namespace App\DTOs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

readonly class PostSuggestionDTO
{
    public string $title;
    public string $content;
    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public static function collection(array $data): Collection
    {
        $validator = Validator::make($data, [
            'suggestions' => 'present|array',
            'suggestions.*.title' => 'required|string|max:150',
            'suggestions.*.content' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $suggestions = $validator->validated()['suggestions'];

        return collect($suggestions)->map(fn ($item) => new self(title: $item['title'], content: $item['content']));
    }
}
