<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Meteorológico - Open-Meteo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <div class="container">
        <h1>Mapa Meteorológico</h1>
        <div id="map"></div>
        <button onclick="getUserLocation()" class="map-btn">Usar Minha Localização</button>
    </div>

    <script>
        let map = L.map('map').setView([-23.5505, -46.6333], 6); // Posição inicial (São Paulo)

        // Camada base do mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap Contributors'
        }).addTo(map);

        // Função para obter os dados da Open-Meteo e exibir no mapa
        async function fetchWeatherData(lat, lon) {
            const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&hourly=temperature_2m,precipitation&wind_speed=10m&timezone=America%2FSao_Paulo`;

            try {
                let response = await fetch(apiUrl);
                let data = await response.json();

                let temperature = data.current_weather.temperature;
                let windSpeed = data.current_weather.windspeed;
                let precipitation = data.hourly.precipitation[0];

                // Adiciona um marcador no mapa com os dados
                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<b>Clima Atual</b><br>
                                Temperatura: ${temperature}°C<br>
                                Vento: ${windSpeed} km/h<br>
                                Precipitação: ${precipitation} mm`)
                    .openPopup();
            } catch (error) {
                console.error("Erro ao buscar dados da API Open-Meteo:", error);
            }
        }

        // Pegando a localização do usuário
        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let lat = position.coords.latitude;
                    let lon = position.coords.longitude;
                    map.setView([lat, lon], 10);
                    fetchWeatherData(lat, lon);
                });
            } else {
                alert("Geolocalização não suportada pelo navegador.");
            }
        }
    </script>
</body>
</html>
