<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function show(Request $request, WeatherService $weather)
    {
        $defaultLocation = 'Heerenveen';

        $location = $request->input('location', $defaultLocation);

        $request->validate([
            'location' => 'string',
            'cache' => 'nullable|integer|min:3600',
        ]);

        $cacheSeconds = $request->input('cache', 3600);

        try {
            $realtime = $weather->realtimeCached($location, $cacheSeconds, [
                'units' => 'metric',
            ]);

            $forecast = $weather->forecastCached($location, $cacheSeconds, [
                'timesteps' => '1d',
                'startTime' => 'now',
                'endTime' => '+5d',
                'units' => 'metric',
            ]);

            $forecastHourly = $weather->forecastHourlyCached($location, $cacheSeconds, [
                'timesteps' => '1h',
                'startTime' => 'now',
                'endTime' => '+24h',
                'units' => 'metric',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch weather',
                'detail' => $e->getMessage()
            ], 502);
        }

        return view('home', [
            'weather' => $realtime['data']['values'],
            'location' => $realtime['location'],
            'forecast' => $forecast['timelines']['daily'] ?? [],
            'forecastHourly' => $forecastHourly['timelines']['hourly'] ?? [],
        ]);
    }

}
