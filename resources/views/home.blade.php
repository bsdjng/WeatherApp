<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Widget</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex flex-col items-center justify-start p-4">

    <!-- Location Search -->
    <form id="location-form" class="w-full max-w-2xl mb-4 flex">
        <input id="location-input" type="text" placeholder="Enter location..." class="flex-grow rounded-l-2xl p-3 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            value="{{ request()->get('location') }}">
        <button type="submit" class="bg-blue-600 text-white px-5 rounded-r-2xl hover:bg-blue-700 transition">Search</button>
    </form>

    <!-- Weather Layout -->
    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- LEFT COLUMN -->
        <div class="space-y-4">
            <!-- Location Card -->
            <div class="bg-white shadow-md rounded-2xl p-5 text-center">
                <h2 class="text-lg font-semibold text-gray-700">Location</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $location['name'] }}</p>
                <div id="map" data-lat="{{ $location['lat'] }}" data-lon="{{ $location['lon'] }}" data-name="{{ $location['name'] }}" class="mt-3 h-48 w-full rounded-lg shadow-sm"></div>
            </div>

            <!-- Conditions Card -->
            <div class="bg-blue-600 text-white shadow-md rounded-2xl p-6 flex flex-col items-center text-center">
                <h2 class="text-lg font-semibold mb-3">Conditions</h2>
                <img src="{{ asset('tomorrow-icons/' . $weather['weatherCode'] . '0.png') }}" alt="Weather Icon" class="w-20 h-20 mb-3">
                <p>Cloud Cover: {{ $weather['cloudCover'] }}%</p>
                <p>Humidity: {{ $weather['humidity'] }}%</p>
                <button data-toggle="cond-extra" class="text-sm underline mt-2">More ▼</button>
                <div id="cond-extra" class="hidden mt-2 space-y-1 text-sm">
                    <p>Visibility: {{ $weather['visibility'] }} km</p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-4">
            <!-- Temperature Card -->
            <div class="bg-white shadow-md rounded-2xl p-5">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Temperature</h2>
                <div class="flex justify-between text-base">
                    <span>Current:</span>
                    <span>{{ $weather['temperature'] }}°C</span>
                </div>
                <div class="flex justify-between text-base">
                    <span>Feels Like:</span>
                    <span>{{ $weather['temperatureApparent'] }}°C</span>
                </div>
                <button data-toggle="temp-extra" class="text-sm text-blue-600 mt-2">More ▼</button>
                <div id="temp-extra" class="hidden mt-2 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Dew Point:</span>
                        <span>{{ $weather['dewPoint'] }}°C</span>
                    </div>
                </div>
            </div>

            <!-- Wind Card -->
            <div class="bg-white shadow-md rounded-2xl p-5">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Wind</h2>
                <div class="flex justify-between text-base">
                    <span>Speed:</span>
                    <span>{{ $weather['windSpeed'] }} km/h</span>
                </div>
                <div class="flex justify-between text-base">
                    <span>Direction:</span>
                    <span>{{ $weather['windDirection'] }}°</span>
                </div>
                <button data-toggle="wind-extra" class="text-sm text-blue-600 mt-2">More ▼</button>
                <div id="wind-extra" class="hidden mt-2 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Gusts:</span>
                        <span>{{ $weather['windGust'] }} km/h</span>
                    </div>
                </div>
            </div>

            <!-- Other Metrics -->
            <div class="bg-white shadow-md rounded-2xl p-5">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Other</h2>
                <div class="flex justify-between text-base">
                    <span>UV Index:</span>
                    <span>{{ $weather['uvIndex'] }}</span>
                </div>
                <div class="flex justify-between text-base">
                    <span>Precip Chance:</span>
                    <span>{{ $weather['precipitationProbability'] }}%</span>
                </div>
                <button data-toggle="other-extra" class="text-sm text-blue-600 mt-2">More ▼</button>
                <div id="other-extra" class="hidden mt-2 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Pressure:</span>
                        <span>{{ $weather['pressureSurfaceLevel'] }} hPa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forecast Section -->
    <div class="w-full max-w-4xl mt-6 bg-white shadow-md rounded-2xl p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700 text-center flex-grow">5-Day Forecast</h2>
        </div>

        <!-- Cards view -->
        <div id="forecast-cards" class="grid grid-cols-1 sm:grid-cols-6 gap-3 text-center">
            @foreach($forecast as $day)
                @php
                    $values = $day['values'];
                    $date = \Carbon\Carbon::parse($day['time'])->format('D');
                    $weatherCode = $values['weatherCodeMax'];
                    $temp = $values['temperatureAvg'] ?? '—';
                    $precip = $values['precipitationProbabilityAvg'] ?? 0;
                @endphp
                <div class="p-3 rounded-lg bg-blue-50 flex flex-col items-center justify-center">
                    <p class="text-sm font-semibold">{{ $date }}</p>
                    <img src="{{ asset('tomorrow-icons/' . $weatherCode . '0.png') }}" alt="Icon" class="w-12 h-12 my-2">
                    <p class="text-lg font-bold">{{ $temp }}°C</p>
                    <p class="text-xs">Rain: {{ $precip }}%</p>
                </div>
            @endforeach
        </div>

        <!-- Graph view -->
        <canvas id="forecast-graph" height="200"></canvas>
    </div>

    @php
        $forecastData = array_map(function ($day) {
            return [
                'date' => $day['time'],
                'temp' => $day['values']['temperatureAvg'] ?? null,
                'precip' => $day['values']['precipitationProbabilityAvg'] ?? 0,
            ];
        }, $forecast);
    @endphp

    <script>
        window.forecastData = @json($forecastData);
    </script>
</body>

</html>