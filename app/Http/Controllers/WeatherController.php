<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function show(Request $request, WeatherService $weather)
    {
        $request->validate([
            'location' => 'required|string',
            'cache' => 'nullable|integer|min:3600',
        ]);

        $location = $request->input('location');
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
        ]);
    }
}
