<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\PostSuggestionDTO;

class PostSuggestionDTOTest extends TestCase
{
    public function test_that_dto_can_parse_suggestions_from_external_api_response(): void
    {
        $response = fakeHttpRequest();
        $output = $response['output'][0]['content'][0]['text'];
        $rawSuggestions = $output['suggestions'];

        $posts = PostSuggestionDTO::collection($output);

        for ($i = 0; $i < count($rawSuggestions); $i++) {
            $raw = $rawSuggestions[$i];
            $dto = $posts[$i];
            $this->assertEquals($raw['title'], $dto->title);
            $this->assertEquals($raw['content'], $dto->content);
        }
    }
}

function fakeHttpRequest()
{
    $responseObject = [
        "output" => [
            [
                "id" => "",
                "type" => "message",
                "status" => "completed",
                "content" => [
                    [
                        "type" => "output_text",
                        "annotations" => [],
                        "logprobs" => [],
                        "text" => [
                            "suggestions" => [
                                ["title" => "Savoring Southern Seas","content" => "Dive into the rich flavors of the southern seas with our succulent prawns! Whether grilled, sautéed, or tossed in a refreshing salad, these little delights are a seafood lover's dream. 🦐✨ #PrawnPerfection #SeafoodLove"],["title" => "The Best Prawn Dishes from the Southern Seas","content" => "Explore the delicious world of southern sea prawns! From spicy curries to zesty citrus marinades, these dishes will take your taste buds on a captivating journey. What’s your favorite way to enjoy prawns? 🍤🌊 #SeafoodEats #PrawnRecipes"],["title" => "Sustainable Prawn Harvesting=> A Southern Delight","content" => "Did you know that the southern seas are home to some of the most sustainable prawn harvesting practices? Enjoy your meal while caring for our oceans. Let’s choose responsibly sourced seafood! 🌱🐚 #SustainableSeafood #OceanConscious"]]]
                    ]
                ],
                "role" => "assistant"
            ]
        ],

    ];
    return $responseObject;
}
