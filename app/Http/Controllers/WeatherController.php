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

        // Obtener datos del clima actual
        $responseCurrent = Http::get("http://api.openweathermap.org/data/2.5/weather", [
            "q" => $city,
            "appid" => $apiKey,
            "units" => "metric"
        ]);

        $weatherCurrent = $responseCurrent->json();

        // Obtener pronóstico del clima
        $responseForecast = Http::get("http://api.openweathermap.org/data/2.5/forecast", [
            "q" => $city,
            "appid" => $apiKey,
            "units" => "metric"
        ]);

        $weatherForecast = $responseForecast->json();

        // Obtener imagen de Unsplash
        $imageResponse = Http::get("https://api.unsplash.com/photos/random", [
            "query" => $city,
            "client_id" => env('UNSPLASH_ACCESS_KEY'),
        ]);

        $imageData = $imageResponse->json();
        $imageUrl = $imageData['urls']['small'] ?? null;

        //Preparar datos para graficos
        $temperatures = [];
        $humidities = [];
        $dates = [];

        foreach ($weatherForecast['list'] as $index => $forecast) {
            if ($index % 8 == 0) { // Solo cada 8 horas
                $temperatures[] = $forecast['main']['temp'];
                $humidities[] = $forecast['main']['humidity'];
                $dates[] = \Carbon\Carbon::createFromTimestamp($forecast['dt'])->format('d M H:i');
            }
        }

        return view('weather.index', compact('weatherCurrent', 'weatherForecast', 'imageUrl', 'temperatures', 'humidities', 'dates'));
    }
}
