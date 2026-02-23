<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAILog extends Model
{
    /** @use HasFactory<\Database\Factories\OpenAILogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'input',
        'output', // generated text (structured json)
        'response_dump',
        'token_usage',
    ];

    protected $table = 'open_ai_logs';
}
