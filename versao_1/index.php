<?php
$apiKey = "SUA_API_KEY";
$city = "São Paulo"; // Você pode alterar para qualquer cidade

$url = "https://api.open-meteo.com/v1/forecast?latitude=-23.5505&longitude=-46.6333&current_weather=true&hourly=temperature_2m,humidity_2m&timezone=America%2FSao_Paulo";

$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
    die("Erro ao buscar dados da API.");
}

$temperature = $data['current_weather']['temperature'];
$weather_description = $data['current_weather']['weathercode'];
$humidity = $data['hourly']['humidity_2m'][0];
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
        <div class="weather-box">
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= getWeatherDescription($weather_description) ?></p>
            <p class="humidity">Umidade: <?= $humidity ?>%</p>
        </div>
    </div>
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
?>
