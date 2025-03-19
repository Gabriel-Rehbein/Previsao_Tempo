<?php
$apiKey = "SUA_API_KEY";
$city = $_GET['city'] ?? "São Paulo";

$locations = [
    "São Paulo" => ["lat" => -23.5505, "lon" => -46.6333],
    "Rio de Janeiro" => ["lat" => -22.9068, "lon" => -43.1729],
    "Belo Horizonte" => ["lat" => -19.9167, "lon" => -43.9345],
    "Curitiba" => ["lat" => -25.4284, "lon" => -49.2733],
];

$lat = $locations[$city]['lat'] ?? -23.5505;
$lon = $locations[$city]['lon'] ?? -46.6333;

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&daily=temperature_2m_min,temperature_2m_max,weathercode&timezone=America%2FSao_Paulo";

$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
    die("Erro ao buscar dados da API.");
}

$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$min_temps = $data['daily']['temperature_2m_min'];
$max_temps = $data['daily']['temperature_2m_max'];
$weather_codes = $data['daily']['weathercode'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clima - <?= $city ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Clima em <?= $city ?></h1>
        <form method="GET" class="city-form">
            <select name="city" onchange="this.form.submit()">
                <?php foreach ($locations as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $city == $key ? "selected" : "" ?>><?= $key ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="weather-box">
            <img src="<?= getWeatherIcon($weather_code) ?>" class="weather-icon">
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= getWeatherDescription($weather_code) ?></p>
        </div>
        <h2>Previsão para os próximos dias</h2>
        <div class="forecast">
            <?php for ($i = 0; $i < count($min_temps); $i++): ?>
                <div class="forecast-day">
                    <p><?= date('D', strtotime("+$i days")) ?></p>
                    <img src="<?= getWeatherIcon($weather_codes[$i]) ?>" class="weather-icon-small">
                    <p><?= $min_temps[$i] ?>°C / <?= $max_temps[$i] ?>°C</p>
                </div>
            <?php endfor; ?>
        </div>
        <button onclick="toggleTheme()" class="theme-button">Alternar Tema</button>
    </div>
    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark-mode");
        }
    </script>
</body>
</html>

<?php
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
        81 => "Pancadas moderadas",
        82 => "Pancadas intensas",
    ];
    return $descriptions[$code] ?? "Desconhecido";
}

function getWeatherIcon($code) {
    $icons = [
        0 => "icons/sunny.png",
        1 => "icons/mostly_sunny.png",
        2 => "icons/partly_cloudy.png",
        3 => "icons/cloudy.png",
        45 => "icons/fog.png",
        48 => "icons/fog.png",
        51 => "icons/drizzle.png",
        53 => "icons/drizzle.png",
        55 => "icons/drizzle.png",
        61 => "icons/rain.png",
        63 => "icons/rain.png",
        65 => "icons/heavy_rain.png",
        80 => "icons/showers.png",
        81 => "icons/showers.png",
        82 => "icons/showers.png",
    ];
    return $icons[$code] ?? "icons/default.png";
}
?>
