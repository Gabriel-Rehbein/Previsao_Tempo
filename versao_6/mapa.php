<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Meteorológico</title>
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

        // Adicionando camada do mapa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Camadas Meteorológicas
        let precipitationLayer = L.tileLayer('https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=SUA_API_KEY', {
            attribution: 'Precipitação © OpenWeather'
        }).addTo(map);

        let cloudsLayer = L.tileLayer('https://tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=SUA_API_KEY', {
            attribution: 'Nuvens © OpenWeather'
        });

        let tempLayer = L.tileLayer('https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=SUA_API_KEY', {
            attribution: 'Temperatura © OpenWeather'
        });

        let windLayer = L.tileLayer('https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=SUA_API_KEY', {
            attribution: 'Vento © OpenWeather'
        });

        // Controle de Camadas
        let overlayMaps = {
            "Precipitação": precipitationLayer,
            "Nuvens": cloudsLayer,
            "Temperatura": tempLayer,
            "Vento": windLayer
        };

        L.control.layers(null, overlayMaps, { collapsed: false }).addTo(map);

        // Função para pegar localização do usuário
        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let lat = position.coords.latitude;
                    let lon = position.coords.longitude;
                    map.setView([lat, lon], 10);
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup("Você está aqui!")
                        .openPopup();
                });
            } else {
                alert("Geolocalização não suportada no seu navegador.");
            }
        }
    </script>
</body>
</html>
