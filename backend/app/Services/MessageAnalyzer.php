<?php 

namespace App\Services;

class MessageAnalyzer
{
    public function needsWeatherData(string $message): bool
    {
        $keywords = ['clima', 'lloverá', 'lluvia', 'temperatura', 'paraguas', 'tiempo', 'frío', 'calor'];
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    public function extractCity(string $message): ?string
    {
        preg_match('/en\s+([A-ZÁÉÍÓÚÑa-záéíóúñ\s]+)/u', $message, $matches);
        return $matches[1] ?? null;
    }
}
