<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected string $baseUri;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tomorrow.key') ?? env('TOMORROW_API_KEY');
        $this->baseUri = rtrim(config('services.tomorrow.base_uri', 'https://api.tomorrow.io/v4'), '/');
    }

    public function realtime(string $location, array $opts = []): array
    {
        $endpoint = $this->baseUri . '/weather/realtime';

        $params = [
            'location' => $location,
            'units' => $opts['units'] ?? 'metric',
        ];

        $response = Http::retry(3, 200)
            ->withHeaders([
                'Accept' => 'application/json',
                'apikey' => $this->apiKey,
            ])
            ->get($endpoint, $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Tomorrow.io Realtime API request failed {$response->status()} {$response->body()}");
    }

    public function realtimeCached(string $location, int $seconds = 60, array $opts = []): array
    {
        $key = 'tomorrow:realtime:' . md5($location . json_encode($opts));

        return Cache::remember($key, now()->addSeconds($seconds), function () use ($location, $opts) {
            return $this->realtime($location, $opts);
        });
    }

    /**
     * Forecast (hourly/daily).
     *
     * @param string $location
     * @param array $opts
     *        Example: ['timesteps' => '1d', 'startTime' => 'now', 'endTime' => '+5d']
     */
    public function forecast(string $location, array $opts = []): array
    {
        $endpoint = $this->baseUri . '/weather/forecast';

        $params = array_merge([
            'location' => $location,
            'timesteps' => $opts['timesteps'] ?? '1d', // daily by default
            'units' => $opts['units'] ?? 'metric',
        ], $opts);

        $response = Http::retry(3, 200)
            ->withHeaders([
                'Accept' => 'application/json',
                'apikey' => $this->apiKey,
            ])
            ->get($endpoint, $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Tomorrow.io Forecast API failed {$response->status()} {$response->body()}");
    }

    /**
     * Cached forecast wrapper.
     */
    public function forecastCached(string $location, int $seconds = 300, array $opts = []): array
    {
        $key = 'tomorrow:forecast:' . md5($location . json_encode($opts));

        return Cache::remember($key, now()->addSeconds($seconds), function () use ($location, $opts) {
            return $this->forecast($location, $opts);
        });
    }
}
