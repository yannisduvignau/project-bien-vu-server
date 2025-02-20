<?php

namespace App\Actions;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIRequestAction
{
    public function execute(string $model, string $role_system, string $prompt, int $temperature)
    {
        $responseAI = OpenAI::chat()->create([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $role_system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => $temperature,
        ]);

        return $responseAI['choices'][0]['message']['content'];
    }
}
