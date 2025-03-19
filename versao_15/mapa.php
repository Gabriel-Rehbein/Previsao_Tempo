<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Meteorológico Profissional</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: #1e1e1e;
            color: #fff;
            text-align: center;
        }

        h1 {
            font-size: 26px;
            margin-top: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        /* Caixa de pesquisa */
        #search {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            outline: none;
            transition: 0.3s;
        }

        #search:focus {
            box-shadow: 0px 0px 8px rgba(255, 255, 255, 0.7);
        }

        /* Estilização do mapa */
        #map {
            width: 95%;
            height: 80vh;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            transition: 0.3s;
            position: relative;
        }

        /* Estilização do tooltip */
        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            display: none;
            pointer-events: none;
            box-shadow: 0px 0px 8px rgba(255, 255, 255, 0.3);
            transition: opacity 0.2s ease-in-out;
            z-index: 1000; /* Garante que fique acima do mapa */
        }

        /* Botões */
        .map-btn {
            display: inline-block;
            background: #ffcc00;
            color: #000;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            transition: 0.3s;
        }

        .map-btn:hover {
            background: #ffdd44;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            h1 {
                font-size: 22px;
            }

            p {
                font-size: 14px;
            }

            #search {
                width: 90%;
            }

            #map {
                height: 60vh;
            }
        }

    </style>
</head>
<body>

    <!-- Tooltip -->
    <div id="tooltip" class="tooltip"></div>

    <h1>🌍 Mapa Meteorológico Profissional</h1>
    <p>Visualize previsões climáticas com detalhes.</p>

    <!-- Barra de pesquisa -->
    <input type="text" id="search" placeholder="🔎 Digite uma cidade..." onkeypress="if(event.key === 'Enter') searchLocation()">

    <div id="map"></div>
    <button onclick="getUserLocation()" class="map-btn">📍 Usar Minha Localização</button>

    <script>
        let map = L.map('map').setView([-23.5505, -46.6333], 6);
        let tooltip = document.getElementById("tooltip");
        let lastRequestTime = 0;
        let cacheWeatherData = {};

        // Camada de mapa moderna
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap Contributors'
        }).addTo(map);

        async function fetchWeatherData(lat, lon) {
            const cacheKey = `${lat.toFixed(2)},${lon.toFixed(2)}`;
            const currentTime = Date.now();

            // Se os dados já foram buscados recentemente, usar cache
            if (cacheWeatherData[cacheKey] && (currentTime - lastRequestTime < 5000)) {
                return cacheWeatherData[cacheKey];
            }

            const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&timezone=America%2FSao_Paulo`;

            try {
                let response = await fetch(apiUrl);
                let data = await response.json();

                // Cache dos dados para evitar múltiplas requisições
                cacheWeatherData[cacheKey] = data.current_weather;
                lastRequestTime = currentTime;

                return data.current_weather;
            } catch (error) {
                console.error("Erro ao buscar dados da API Open-Meteo:", error);
                return null;
            }
        }

        map.on('mousemove', async function(e) {
            let lat = e.latlng.lat;
            let lon = e.latlng.lng;

            let weather = await fetchWeatherData(lat, lon);
            if (!weather) return;

            tooltip.innerHTML = `
                <b>🌦 Clima Atual</b><br>
                🌡 <b>${weather.temperature}°C</b><br>
                💨 Vento: ${weather.windspeed} km/h
            `;

            tooltip.style.left = (e.originalEvent.pageX + 15) + "px";
            tooltip.style.top = (e.originalEvent.pageY + 15) + "px";
            tooltip.style.opacity = "1";
            tooltip.style.display = "block";
        });

        map.on('mouseout', function() {
            tooltip.style.opacity = "0";
            setTimeout(() => tooltip.style.display = "none", 200);
        });

        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    let lat = position.coords.latitude;
                    let lon = position.coords.longitude;
                    map.setView([lat, lon], 10);
                    fetchWeatherData(lat, lon);
                }, () => {
                    alert("⚠️ Não foi possível obter sua localização.");
                });
            } else {
                alert("⚠️ Geolocalização não suportada pelo navegador.");
            }
        }

        function searchLocation() {
            let query = document.getElementById("search").value;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        let lat = data[0].lat;
                        let lon = data[0].lon;
                        map.setView([lat, lon], 10);
                        fetchWeatherData(lat, lon);
                    } else {
                        alert("⚠️ Local não encontrado.");
                    }
                });
        }

    </script>

</body>
</html>
