<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->input('city', 'Bogotá'); // Ciudad por defecto
        $apiKey = env('OPENWEATHER_API_KEY');

        // Cambia a la URL correcta y quita el 'id=524901'
        $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
            "q" => $city,
            'appid' => $apiKey,
            'units' => 'metric'
        ]);

        $weather = $response->json();

        $imageResponse = Http::get("https://api.unsplash.com/photos/random", [
            "query" => $city,
            "client_id" => env('UNSPLASH_ACCESS_KEY'),
        ]);

        $imageData = $imageResponse->json();
        
        $imageUrl = $imageData['urls']['small'] ?? null;

        // Verificar si la respuesta contiene datos válidos
        if (isset($weather['cod']) && $weather['cod'] !== 200) {
            return view('weather.index')->with('error', 'Ciudad no encontrada');
        }

        return view('weather.index', compact('weather', 'imageUrl'));
    }
}
