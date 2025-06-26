<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Dto\ConversationData;
use App\Repositories\ConversationRepository;
use App\Services\MessageAnalyzer;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;
class ChatController extends Controller
{
    protected $openAI;
    protected $analyzer;
    protected $repo;
    protected $weatherService;
    public function __construct(OpenAIService $openAI, MessageAnalyzer $analyzer, ConversationRepository $repo, WeatherService $weatherService)
    {
        $this->openAI = $openAI;
        $this->analyzer = $analyzer;
        $this->repo = $repo;
        $this->weatherService = $weatherService;
    }

   public function ask(Request $request)
{
    $request->validate([
        'user_name' => 'required|string|max:100',
        'message' => 'required|string',
    ]);

    $userName = $request->input('user_name');
    $prompt = $request->input('message');
    $finalPrompt = $prompt;
    $weatherData = null;

    if ($this->analyzer->needsWeatherData($prompt)) {
        $city = $this->analyzer->extractCity($prompt);
        Log::warning("No se detectó ciudad en el mensaje: $city");
        if (!$city) {
            $finalPrompt = "Usuario preguntó: $prompt\n\nNo se pudo detectar una ciudad. Responde sin datos del clima.";
        } else {
            $forecast = $this->weatherService->getForecast($city);

            if (!$forecast) {
                $finalPrompt = "Usuario preguntó: $prompt\n\nNo se pudo obtener el clima para '$city'. Responde lo mejor posible sin datos del clima.";
            } else {
                $daily = $forecast['weather']['daily'] ?? [];
                $fecha = $daily['time'][1] ?? 'fecha desconocida';
                $temp = $daily['temperature_2m_max'][1] ?? 'desconocida';
                $rain = $daily['precipitation_sum'][1] ?? 'desconocida';

                $weatherData = [
                    'city' => $forecast['location']['name'] ?? $city,
                    'country' => $forecast['location']['country'] ?? '',
                    'date' => $fecha,
                    'temperature' => $temp,
                    'precipitation' => $rain,
                    'formatted' => "Clima en {$forecast['location']['name']} ({$fecha}):\n- Temperatura: {$temp}°C\n- Precipitación: {$rain} mm"
                ];

                $finalPrompt = "Usuario preguntó: $prompt\n\nDatos del clima disponibles:\n{$weatherData['formatted']}\n\nPor favor responde con claridad en español, usando esta información para generar una respuesta natural y amigable.";
            }
        }
    } else {
        $finalPrompt = "Usuario preguntó: $prompt\n\nNo requiere consultar datos externos de clima. Responde con claridad en español.";
    }
     
    $response = $this->openAI->ask($finalPrompt);

    if (!$response) {
        return response()->json([
            'error' => 'Falló al obtener una respuesta de IA',
            'weatherData' => $weatherData
        ], 500);
    }

    $conversation = new ConversationData(
        user_name: $userName,
        user_message: $prompt,
        ai_response: $response,
        api_response: $weatherData ? json_encode($weatherData) : 'No aplica'
    );
    
    $this->repo->save($conversation);

    return response()->json([
        'response' => $response,
        'weatherData' => $weatherData
    ], 200);
}
    public function historyByUser(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:100',
        ]);
        $userName = $request->input('user_name');
        $conversations = $this->repo->findByUserName($userName);
        if (empty($conversations)) {
            return response()->json([
                'message' => "No se encontraron conversaciones para el usuario: $userName"
            ], 200);
        }
        return response()->json([
            'user_name' => $userName,
            'conversations' => $conversations
        ], 201);
    }
}
