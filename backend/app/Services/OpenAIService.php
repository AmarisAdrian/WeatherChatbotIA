<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    public function ask(string $prompt): string
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente experto en clima  con acceso a datos
                            actualizados. Responde en español y sé claro. Si te preguntan sobre el clima, decide si necesitas consultar una API externa como la API de OPEN-METEO .Si no tienes datos precisos, indícalo.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
            ]);

        return $response->json('choices.0.message.content');
    }
}
