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

// URL da API Open-Meteo para horários de nascer e pôr do sol
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&daily=sunrise,sunset&timezone=America%2FSao_Paulo&forecast_days=7";

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
    <title>Nascer/Pôr do Sol - <?= htmlspecialchars($city) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🌅 Nascer e Pôr do Sol</h1>
        <h2><?= htmlspecialchars($city) ?></h2>

        <div class="sun-info">
            <?php for ($i = 0; $i < 7; $i++): ?>
                <div class="sun-card">
                    <p class="day"><?= date("D", strtotime("+$i day")) ?></p>
                    <p>🌞 Nascer: <?= date("H:i", strtotime($data['daily']['sunrise'][$i])) ?></p>
                    <p>🌇 Pôr: <?= date("H:i", strtotime($data['daily']['sunset'][$i])) ?></p>
                </div>
            <?php endfor; ?>
        </div>

        <div class="menu">
            <a href="index.php" class="btn">⬅️ Voltar</a>
        </div>
    </div>
</body>
</html>
