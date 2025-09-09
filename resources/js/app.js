import './bootstrap';
import '../css/app.css';

import Chart from 'chart.js/auto';
window.Chart = Chart;

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
window.L = L;


document.addEventListener("DOMContentLoaded", () => {

    // Map
    const mapEl = document.getElementById("map");
    if (mapEl) {
        const lat = mapEl.dataset.lat;
        const lon = mapEl.dataset.lon;
        const name = mapEl.dataset.name;

        const map = L.map(mapEl).setView([lat, lon], 12);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup(name)
            .openPopup();
    }

    // Forecast Chart
    const forecastEl = document.getElementById("forecast-graph");
    if (forecastEl) {
        const forecastData = window.forecastData || [];

        const labels = forecastData.map(d =>
            new Date(d.date).toLocaleDateString("en-US", { weekday: "short" })
        );
        const temps = forecastData.map(d => d.temp);
        const precips = forecastData.map(d => d.precip);

        new Chart(forecastEl.getContext("2d"), {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        type: "line",
                        label: "Temp °C",
                        data: temps,
                        borderColor: "#3B82F6",
                        backgroundColor: "rgba(59, 130, 246, 0.2)",
                        yAxisID: "y1",
                        tension: 0.4,
                    },
                    {
                        type: "bar",
                        label: "Rain %",
                        data: precips,
                        backgroundColor: "rgba(16, 185, 129, 0.7)",
                        yAxisID: "y2",
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: "index", intersect: false },
                stacked: false,
                scales: {
                    y1: {
                        type: "linear",
                        position: "left",
                        title: { display: true, text: "Temperature °C" },
                        beginAtZero: false,
                    },
                    y2: {
                        type: "linear",
                        position: "right",
                        title: { display: true, text: "Precipitation %" },
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                    },
                },
            },
        });
    }

    // Search Form
    const form = document.getElementById("location-form");
    const input = document.getElementById("location-input");

    if (form && input) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            const query = input.value.trim();
            if (query) {
                window.location.href = "/?location=" + encodeURIComponent(query);
            }
        });
    }

    // Wind Arrow
    const windArrow = document.getElementById("wind-arrow");
    if (windArrow) {
        const windDirection = parseFloat(windArrow.dataset.deg || 0);
        windArrow.style.transform = `rotate(${windDirection + 180}deg)`;
        windArrow.style.transition = "transform 0.3s ease-in-out";
    }
});
