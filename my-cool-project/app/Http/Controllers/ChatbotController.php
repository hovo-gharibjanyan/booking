<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\TourDate;

class ChatbotController extends Controller
{
    // public function handle(Request $request)
    // {
    //     $validated = $request->validate(['message' => 'required|string|max:500']);
        
    //     try {
    //         $apiKey = env('GEMINI_API_KEY');
    //         if (!$apiKey) { throw new \Exception('API ключ Gemini не настроен.'); }

    //         $client = \Gemini::client($apiKey);
    //         $result = $client->generativeModel('gemini-pro-latest')
    //             ->generateContent($validated['message']);

    //         return response()->json(['reply' => $result->text()]);
    //     } catch (\Exception $e) {
    //         \Log::error('Gemini Chat Error: ' . $e->getMessage()); // Логируем ошибку для себя
    //         return response()->json(['reply' => 'Извините, произошла ошибка. Попробуйте позже.'], 500);
    //     }
    // }

    public function handle(Request $request)
    {
        $validated = $request->validate(['message' => 'required|string|max:1000']);
        $userQuestion = $validated['message'];

        $tours = Tour::with('dates')->get();
        
        $context = "Вот список доступных туров и их дат:\n\n";
        foreach ($tours as $tour) {
            $context .= "- Название тура: " . $tour->title . "\n";
            $context .= "  Цена: " . ($tour->price_minor / 100) . " USD\n";
            $context .= "  Доступные даты: ";
            $dates = $tour->dates->pluck('date')->implode(', ');
            $context .= $dates ? $dates : "Нет доступных дат";
            $context .= "\n\n";
        }

        $prompt = "Ты — 'BookingBot', дружелюбный ассистент сайта BookingLite. Твоя задача — помочь пользователю найти идеальный тур. 
        Отвечай на вопрос пользователя, основываясь ИСКЛЮЧИТЕЛЬНО на информации, предоставленной ниже в разделе 'СПИСОК ТУРОВ'. 
        Не придумывай ничего от себя. Если в списке нет подходящего тура, вежливо сообщи об этом. 
        Всегда предлагай конкретные туры по их названию.

        ---
        СПИСОК ТУРОВ:
        $context
        ---

        ВОПРОС ПОЛЬЗОВАТЕЛЯ:
        \"$userQuestion\"";
        
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) { throw new \Exception('API ключ Gemini не настроен.'); }

            $client = \Gemini::client($apiKey);
            
            $result = $client->generativeModel('gemini-pro-latest')
                ->generateContent($prompt); // Отправляем наш большой промпт

            return response()->json(['reply' => $result->text()]);
        } catch (\Exception $e) {
            \Log::error('Gemini Chat Error: ' . $e->getMessage());
            return response()->json(['reply' => 'Извините, произошла ошибка. Попробуйте позже.'], 500);
        }
    }
}
