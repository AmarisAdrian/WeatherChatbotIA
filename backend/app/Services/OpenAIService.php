<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function ask(string $prompt): string
    {
        $apiKey = $this->getApiKey();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->retry(3, 500)
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
        // Verifica la respuesta completa para debugging
        Log::debug('OpenAI Response', $response->json());

        if ($response->failed()) {
            throw new \Exception("OpenAI API request failed: " . $response->status());
        }

        return $response->json('choices.0.message.content');
    }
    protected function getApiKey(): string
    {
        $apiKey = env('OPENAI_API_KEY') ?? config('services.openai.key');
        
        if (empty($apiKey)) {
            Log::critical('OpenAI API key is not configured');
            throw new \RuntimeException(
                'La clave API de OpenAI no está configurada. ' .
                'Por favor configure OPENAI_API_KEY en el archivo .env o en config/services.php'
            );
        }

        return $apiKey;
    }
}
