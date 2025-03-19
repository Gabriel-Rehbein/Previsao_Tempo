<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Meteorológico</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
</head>
<body>
    <div class="container">
        <h1>Mapa Meteorológico</h1>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="Digite uma cidade ou endereço">
            <button onclick="searchLocation()">Buscar</button>
        </div>

        <div id="map"></div>

        <button onclick="getUserLocation()" class="map-btn">Usar Minha Localização</button>
        <button onclick="shareWeather()" class="map-btn share-btn">Compartilhar Clima</button>

        <div id="weather-panel" class="weather-panel">
            <h2>Detalhes do Clima</h2>
            <p><strong>Local:</strong> <span id="location-name">Selecione um local</span></p>
            <p><strong>Temperatura:</strong> <span id="temperature">--°C</span></p>
            <p><strong>Condições:</strong> <span id="weather-desc">--</span></p>
            <img id="weather-icon" src="" alt="Ícone do Clima" style="display: none;">
        </div>
    </div>

    <script>
        let map = L.map('map').setView([-23.5505, -46.6333], 6);

        // Camada base do mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let geocoder = L.Control.geocoder({
            defaultMarkGeocode: false
        }).on('markgeocode', function(e) {
            let latlng = e.geocode.center;
            map.setView(latlng, 10);
            L.marker(latlng).addTo(map)
                .bindPopup(e.geocode.name)
                .openPopup();
            getWeather(latlng.lat, latlng.lng, e.geocode.name);
        }).addTo(map);

        function searchLocation() {
            let input = document.getElementById('search-input').value;
            if (!input) return;
            geocoder.options.geocoder.geocode(input, function(results) {
                if (results.length) {
                    let latlng = results[0].center;
                    map.setView(latlng, 10);
                    L.marker(latlng).addTo(map)
                        .bindPopup(results[0].name)
                        .openPopup();
                    getWeather(latlng.lat, latlng.lng, results[0].name);
                } else {
                    alert("Local não encontrado.");
                }
            });
        }

        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let lat = position.coords.latitude;
                    let lon = position.coords.longitude;
                    map.setView([lat, lon], 10);
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup("Você está aqui!")
                        .openPopup();
                    getWeather(lat, lon, "Sua Localização");
                });
            } else {
                alert("Geolocalização não suportada no seu navegador.");
            }
        }

        async function getWeather(lat, lon, locationName) {
            const apiKey = "SUA_API_KEY";
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&hourly=temperature_2m,weathercode&timezone=America%2FSao_Paulo`;

            try {
                let response = await fetch(url);
                let data = await response.json();
                let temperature = data.current_weather.temperature;
                let weatherCode = data.current_weather.weathercode;
                let weatherDesc = getWeatherDescription(weatherCode);
                let iconUrl = getWeatherIcon(weatherCode);

                document.getElementById('location-name').textContent = locationName;
                document.getElementById('temperature').textContent = `${temperature}°C`;
                document.getElementById('weather-desc').textContent = weatherDesc;
                document.getElementById('weather-icon').src = iconUrl;
                document.getElementById('weather-icon').style.display = "block";

                L.marker([lat, lon], {
                    icon: L.icon({
                        iconUrl: iconUrl,
                        iconSize: [50, 50],
                        iconAnchor: [25, 50]
                    })
                }).addTo(map)
                  .bindPopup(`${locationName}: ${temperature}°C, ${weatherDesc}`)
                  .openPopup();
            } catch (error) {
                console.error("Erro ao buscar dados meteorológicos:", error);
            }
        }

        function getWeatherDescription(code) {
            const descriptions = {
                0: "Céu limpo",
                1: "Principalmente limpo",
                2: "Parcialmente nublado",
                3: "Nublado",
                45: "Nevoeiro",
                48: "Neblina",
                51: "Chuvisco fraco",
                53: "Chuvisco moderado",
                55: "Chuvisco intenso",
                61: "Chuva fraca",
                63: "Chuva moderada",
                65: "Chuva intensa",
                80: "Pancadas de chuva",
                81: "Pancadas moderadas",
                82: "Pancadas intensas"
            };
            return descriptions[code] || "Desconhecido";
        }

        function getWeatherIcon(code) {
            const icons = {
                0: "icons/sunny.gif",
                1: "icons/mostly_sunny.gif",
                2: "icons/partly_cloudy.gif",
                3: "icons/cloudy.gif",
                45: "icons/fog.gif",
                48: "icons/fog.gif",
                51: "icons/drizzle.gif",
                53: "icons/drizzle.gif",
                55: "icons/drizzle.gif",
                61: "icons/rain.gif",
                63: "icons/rain.gif",
                65: "icons/heavy_rain.gif",
                80: "icons/showers.gif",
                81: "icons/showers.gif",
                82: "icons/showers.gif"
            };
            return icons[code] || "icons/default.gif";
        }
    </script>
</body>
</html>
