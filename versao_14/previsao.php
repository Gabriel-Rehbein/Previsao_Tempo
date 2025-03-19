<?php
// Definir cidade e coordenadas padrão
$city = "Porto Alegre";
$lat = -30.0346;
$lon = -51.2177;

// Verifica se há coordenadas via GET
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
}

// URL da API Open-Meteo para previsão de 7 dias
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=temperature_2m_min,temperature_2m_max,weathercode&timezone=America%2FSao_Paulo&forecast_days=7";

// Buscar os dados da API
$data = fetchWeatherData($url);

if (!isset($data['daily'])) {
    die("Erro ao buscar dados da API.");
}

// Função para buscar dados da API
function fetchWeatherData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Função para obter o ícone do clima
function getWeatherIcon($code) {
    $icons = [
        0 => "wi-day-sunny", 1 => "wi-day-cloudy", 2 => "wi-cloud",
        3 => "wi-cloudy", 45 => "wi-fog", 48 => "wi-dust",
        51 => "wi-sprinkle", 53 => "wi-showers", 55 => "wi-rain-mix",
        61 => "wi-rain", 63 => "wi-rain-wind", 65 => "wi-thunderstorm",
        80 => "wi-rain"
    ];
    return $icons[$code] ?? "wi-na";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo - <?= htmlspecialchars($city) ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.12/css/weather-icons.min.css">
</head>
<body>
    <div class="container">
        <h1>Previsão do Tempo para a Semana</h1>
        <h2><?= htmlspecialchars($city) ?></h2>

        <div class="week-forecast">
            <?php for ($i = 0; $i < 7; $i++): ?>
                <div class="day-card">
                    <p class="day"><?= date("D", strtotime("+$i day")) ?></p>
                    <i class="wi <?= getWeatherIcon($data['daily']['weathercode'][$i]) ?>"></i>
                    <p class="max-temp">🔼 <?= $data['daily']['temperature_2m_max'][$i] ?>°C</p>
                    <p class="min-temp">🔽 <?= $data['daily']['temperature_2m_min'][$i] ?>°C</p>
                </div>
            <?php endfor; ?>
        </div>

        <div class="menu">
            <a href="index.php" class="btn">⬅️ Voltar</a>
        </div>
    </div>
</body>
</html>
