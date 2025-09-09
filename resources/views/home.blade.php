<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Widget</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex flex-col items-center justify-start p-4">

    <!-- Search -->
    <form id="location-form" class="w-full max-w-2xl mb-4 flex">
        <input id="location-input" type="text" placeholder="Enter location..." class="flex-grow rounded-l-2xl p-3 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            value="{{ request()->get('location') }}">
        <button type="submit" class="bg-blue-600 text-white px-5 rounded-r-2xl hover:bg-blue-700 transition">Search</button>
    </form>

    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="space-y-4">
            <!-- Location -->
            <div class="bg-white shadow-md rounded-2xl p-5 text-center">
                <h2 class="text-lg font-semibold text-gray-700">Location</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $location['name'] }}</p>
                <div id="map" data-lat="{{ $location['lat'] }}" data-lon="{{ $location['lon'] }}" data-name="{{ $location['name'] }}" class="mt-3 h-48 w-full rounded-lg shadow-sm"></div>
            </div>

            <!-- Conditions -->
            <div class="bg-blue-600 text-white shadow-md rounded-2xl p-6 flex flex-col items-center text-center">
                <h2 class="text-lg font-semibold mb-3">Conditions</h2>
                <img src="{{ asset('tomorrow-icons/' . $weather['weatherCode'] . '0.png') }}" alt="Weather Icon" class="w-20 h-20 mb-3">
                <p>Cloud Cover: {{ $weather['cloudCover'] }}%</p>
                <p>Humidity: {{ $weather['humidity'] }}%</p>
                <p>Visibility: {{ $weather['visibility'] }} km</p>
            </div>
        </div>

        <div class="flex space-x-3">
            <!-- Info cards -->
            <div class="flex-1 flex flex-col space-y-5">
                <!-- Temperature Card -->
                <div class="bg-white shadow-md rounded-2xl p-6 flex-1">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Temperature</h2>
                    <div class="flex justify-between text-base mb-2">
                        <span>Current:</span>
                        <span>{{ $weather['temperature'] }}°C</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span>Feels Like:</span>
                        <span>{{ $weather['temperatureApparent'] }}°C</span>
                    </div>
                </div>

                <!-- Wind Card -->
                <div class="bg-white shadow-md rounded-2xl p-6 flex-1">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Wind</h2>
                    <div class="flex justify-between text-base mb-2">
                        <span>Speed:</span>
                        <span>{{ $weather['windSpeed'] }} km/h</span>
                    </div>
                    <div class="flex justify-between items-center text-base">
                        <span>Direction:</span>
                        <img id="wind-arrow" src="{{ asset('right-arrow.png') }}" alt="Wind Arrow" class="w-5 h-5 ml-2" data-deg="{{ $weather['windDirection'] }}">
                    </div>
                </div>

                <!-- Other Card -->
                <div class="bg-white shadow-md rounded-2xl p-6 flex-1">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Other</h2>
                    <div class="flex justify-between text-base mb-2">
                        <span>UV Index:</span>
                        <span>{{ $weather['uvIndex'] }}</span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span>Precip Chance:</span>
                        <span>{{ $weather['precipitationProbability'] }}%</span>
                    </div>
                </div>
            </div>
            <!-- Hourly forecast -->
            <div class="w-28 bg-white shadow-md rounded-2xl p-3 overflow-y-auto max-h-[564px]">
                <h2 class="text-lg font-semibold text-gray-700 mb-3 text-center">Next 24h</h2>
                <div class="flex flex-col items-center">
                    @forelse($forecastHourly as $hour)
                        @php
                            $values = $hour['values'];
                            $time = \Carbon\Carbon::parse($hour['time'])->format('H:i');
                            $temp = round($values['temperature'] ?? 0);
                            $icon = $values['weatherCode'] ?? 1000;
                        @endphp
                        <div class="flex flex-col items-center w-full py-2 border-b last:border-b-0 border-gray-200">
                            <p class="text-xs text-gray-500">{{ $time }}</p>
                            <img src="{{ asset('tomorrow-icons/' . $icon . '0.png') }}" alt="Icon" class="w-8 h-8 my-1">
                            <p class="text-sm font-semibold">{{ $temp }}°C</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center mt-2">No hourly data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Forecast -->
    <div class="w-full max-w-4xl mt-6 bg-white shadow-md rounded-2xl p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700 text-center flex-grow">5-Day Forecast</h2>
        </div>

        <!-- Cards view -->
        <div id="forecast-cards" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-center">
            @foreach($forecast as $index => $day)
                @if($index >= 5)
                    @break
                @endif
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