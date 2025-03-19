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

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&hourly=temperature_2m&daily=temperature_2m_min,temperature_2m_max,weathercode&timezone=America%2FSao_Paulo";

$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
    die("Erro ao buscar dados da API.");
}

$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$hourly_temps = $data['hourly']['temperature_2m'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <h2>Temperatura ao longo do dia</h2>
        <div class="chart-container">
            <canvas id="tempChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('tempChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(range(0, count($hourly_temps)-1)) ?>,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: <?= json_encode($hourly_temps) ?>,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Hora' }},
                    y: { title: { display: true, text: 'Temperatura (°C)' }}
                }
            }
        });
    </script>
</body>
</html>
