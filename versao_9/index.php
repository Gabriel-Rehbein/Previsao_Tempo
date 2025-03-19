<?php
// Definir cidade padrão
$city = "Porto Alegre";
$lat = -30.0346;
$lon = -51.2177;

// Se latitude e longitude forem passadas via GET, sobrescreve as coordenadas
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
}

// Montar a URL da API Open-Meteo
$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&daily=temperature_2m_min,temperature_2m_max,weathercode&hourly=temperature_2m&timezone=America%2FSao_Paulo";

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

// Buscar os dados da API
$data = fetchWeatherData($url);

// Verificar se os dados retornados contêm as informações esperadas
if (!isset($data['current_weather'])) {
    die("Erro: A estrutura da resposta da API mudou ou está vazia.");
}

// Obter valores do JSON
$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$hourlyTemperatures = $data['hourly']['temperature_2m'] ?? [];
$min_temperature = $data['daily']['temperature_2m_min'][0] ?? "N/A";

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Clima - <?= htmlspecialchars($city) ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body onload="getLocation()">
    <div class="container">
        <h1>Clima em <?= htmlspecialchars($city) ?></h1>
        
        <form method="GET" class="city-form">
            <select name="city" onchange="this.form.submit()">
                <option value="São Paulo" <?= $city == "São Paulo" ? "selected" : "" ?>>São Paulo</option>
                <option value="Rio de Janeiro" <?= $city == "Rio de Janeiro" ? "selected" : "" ?>>Rio de Janeiro</option>
                <option value="Porto Alegre" <?= $city == "Porto Alegre" ? "selected" : "" ?>>Porto Alegre</option>
            </select>
        </form>

        <div class="weather-box">
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= getWeatherDescription($weather_code) ?></p>
            <p class="humidity">Mínima: <?= $min_temperature ?>°C</p>
        </div>

        <h2>Temperatura ao longo do dia</h2>
        <div class="chart-container">
            <canvas id="tempChart"></canvas>
        </div>
    </div>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    window.location.href = `index.php?lat=${position.coords.latitude}&lon=${position.coords.longitude}`;
                });
            }
        }

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
?>
