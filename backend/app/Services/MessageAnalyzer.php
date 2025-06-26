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
        if (preg_match('/en\s+([A-ZÁÉÍÓÚÑa-záéíóúñ\s]+)/u', $message, $matches)) {
            return trim($matches[1]);
        }
        $words = explode(' ', $message);
        $possibleCity = null;

        foreach ($words as $word) {
            if (in_array(strtolower($word), ['hoy', 'mañana', 'el', 'la', 'un', 'una', 'en', 'de', 'a'])) {
                continue;
            }
            if (preg_match('/^[A-ZÁÉÍÓÚÑ]/u', $word)) {
                $possibleCity = $word;
            }
        }
        return $possibleCity;
    }



}
