<?php
// Definir cidade padrão e coordenadas
$city = "Porto Alegre";
$lat = -30.0346;
$lon = -51.2177;

// Se latitude e longitude forem passadas via GET, sobrescreve as coordenadas
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
}

// Montar a URL da API Open-Meteo com dados passados e históricos
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&daily=temperature_2m_min,temperature_2m_max,temperature_2m_mean,weathercode&hourly=temperature_2m,uv_index,relative_humidity_2m,wind_speed_10m&timezone=America%2FSao_Paulo&past_days=1";

// Buscar os dados da API
$data = fetchWeatherData($url);

if (!isset($data['current_weather'])) {
    die("Erro ao buscar dados.");
}

// Obter valores do JSON
$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$humidity = $data['hourly']['relative_humidity_2m'][0] ?? "N/A";
$wind_speed = $data['hourly']['wind_speed_10m'][0] ?? "N/A";
$uv_index = $data['hourly']['uv_index'][0] ?? "N/A";
$min_temperature = $data['daily']['temperature_2m_min'][0] ?? "N/A";
$max_temperature = $data['daily']['temperature_2m_max'][0] ?? "N/A";
$average_temperature_yesterday = $data['daily']['temperature_2m_mean'][1] ?? "N/A"; // Média do dia anterior
$comparison_today_yesterday = ($temperature - $average_temperature_yesterday);
$hourlyTemperatures = $data['hourly']['temperature_2m'] ?? [];

// Obter descrição e ícone do clima
$weather_description = getWeatherDescription($weather_code);
$weather_icon = getWeatherIcon($weather_code);

// Gerar frase amigável sobre o clima
$weather_phrase = generateWeatherPhrase($temperature, $humidity, $wind_speed, $weather_description);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Clima - <?= htmlspecialchars($city) ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body onload="getLocation()">
    <div class="container">
        <h1>Clima em <?= htmlspecialchars($city) ?></h1>

        <div class="weather-box">
            <i class="wi <?= $weather_icon ?>"></i>
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= $weather_description ?></p>
            <p class="phrase"><?= $weather_phrase ?></p>
            <p>🌫️ Umidade: <?= $humidity ?>%</p>
            <p>💨 Vento: <?= $wind_speed ?> km/h</p>
            <p>🌞 Índice UV: <?= $uv_index ?></p>
            <p>🔽 Mín: <?= $min_temperature ?>°C | 🔼 Máx: <?= $max_temperature ?>°C</p>
            <p>📊 Temperatura média do dia anterior: <?= $average_temperature_yesterday ?>°C</p>
            <p>📉 Comparação hoje x ontem: <?= $comparison_today_yesterday >= 0 ? "🔺" : "🔻" ?> <?= abs($comparison_today_yesterday) ?>°C</p>
        </div>

        <h2>Temperatura ao longo do dia</h2>
      
            <canvas id="tempChart"></canvas>
       
        <a href="index.php" class="btn back-btn">⬅️ Voltar</a>
    </div>

    <script>
        const ctx = document.getElementById('tempChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(range(0, count($hourlyTemperatures) - 1)) ?>,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: <?= json_encode($hourlyTemperatures) ?>,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            }
        });
    </script>
</body>
</html>

<?php
// Função para buscar dados da API via cURL
function fetchWeatherData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200 || !$response) {
        die("Erro ao buscar dados da API. Código HTTP: $httpCode");
    }

    return json_decode($response, true);
}

// Função para converter código do clima em descrição legível
function getWeatherDescription($code) {
    $descriptions = [
        0 => "Céu limpo",
        1 => "Principalmente limpo",
        2 => "Parcialmente nublado",
        3 => "Nublado",
        45 => "Nevoeiro",
        48 => "Neblina",
        51 => "Chuvisco fraco",
        53 => "Chuvisco moderado",
        55 => "Chuvisco intenso",
        61 => "Chuva fraca",
        63 => "Chuva moderada",
        65 => "Chuva intensa",
        80 => "Pancadas de chuva",
    ];
    return $descriptions[$code] ?? "Desconhecido";
}

// Função para obter o ícone correto para o clima
function getWeatherIcon($code) {
    $icons = [
        0 => "wi-day-sunny",
        1 => "wi-day-cloudy",
        2 => "wi-cloud",
        3 => "wi-cloudy",
        45 => "wi-fog",
        48 => "wi-dust",
        51 => "wi-sprinkle",
        53 => "wi-showers",
        55 => "wi-rain-mix",
        61 => "wi-rain",
        63 => "wi-rain-wind",
        65 => "wi-thunderstorm",
        80 => "wi-rain",
    ];
    return $icons[$code] ?? "wi-na";
}

// Função para gerar uma frase amigável com base nas condições do clima
function generateWeatherPhrase($temperature, $humidity, $wind_speed, $weather_desc) {
    $phrase = "O clima está agradável.";

    if ($temperature > 30) {
        $phrase = "Hoje está um dia quente, perfeito para um sorvete! 🍦";
    } elseif ($temperature > 20) {
        $phrase = "O clima está ótimo para um passeio ao ar livre! 🚶‍♂️";
    } elseif ($temperature > 10) {
        $phrase = "Está um pouco fresco, um casaco leve pode ser útil. 🧥";
    } else {
        $phrase = "O frio chegou! Um chocolate quente cai bem! ☕";
    }

    if ($humidity > 80) $phrase .= " A umidade está alta, pode ser um dia abafado. 💦";
    if ($humidity < 30) $phrase .= " O ar está seco, lembre-se de se hidratar. 💧";
    if ($wind_speed > 30) $phrase .= " Está ventando forte, segure seu chapéu! 🎩💨";
    if (strpos($weather_desc, "chuva") !== false) $phrase .= " Melhor levar um guarda-chuva! ☔";

    return $phrase;
}
?>
