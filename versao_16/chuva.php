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

// URL da API Open-Meteo para previsão de chuva
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=precipitation_probability_max&timezone=America%2FSao_Paulo&forecast_days=7";

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
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão de Chuva - <?= htmlspecialchars($city) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🌧️ Previsão de Chuva</h1>
        <h2><?= htmlspecialchars($city) ?></h2>

        <div class="rain-info">
            <?php for ($i = 0; $i < 7; $i++): ?>
                <div class="rain-card">
                    <p class="day"><?= date("D", strtotime("+$i day")) ?></p>
                    <p>💧 Probabilidade: <?= $data['daily']['precipitation_probability_max'][$i] ?>%</p>
                </div>
            <?php endfor; ?>
        </div>

        <div class="menu">
            <a href="index.php" class="btn">⬅️ Voltar</a>
        </div>
    </div>
</body>
</html>
