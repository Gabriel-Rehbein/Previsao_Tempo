<?php
require 'db.php';

$apiKey = "SUA_API_KEY";
$city = $_GET['city'] ?? "São Paulo";

if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
} else {
    $locations = [
        "São Paulo" => ["lat" => -23.5505, "lon" => -46.6333],
        "Rio de Janeiro" => ["lat" => -22.9068, "lon" => -43.1729],
    ];
    $lat = $locations[$city]['lat'] ?? -23.5505;
    $lon = $locations[$city]['lon'] ?? -46.6333;
}

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&daily=temperature_2m_min,temperature_2m_max,weathercode&timezone=America%2FSao_Paulo";

$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
    die("Erro ao buscar dados da API.");
}

$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$humidity = $data['daily']['temperature_2m_min'][0];

// Salvar histórico no banco de dados
$stmt = $pdo->prepare("INSERT INTO historico_clima (cidade, temperatura, descricao, umidade) VALUES (?, ?, ?, ?)");
$stmt->execute([$city, $temperature, getWeatherDescription($weather_code), $humidity]);

// Obter histórico salvo
$historico = $pdo->query("SELECT * FROM historico_clima WHERE cidade = '$city' ORDER BY data_hora DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Clima - <?= $city ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body onload="getLocation()">
    <div class="container">
        <h1>Clima em <?= $city ?></h1>
        <form method="GET" class="city-form">
            <select name="city" onchange="this.form.submit()">
                <option value="São Paulo">São Paulo</option>
                <option value="Rio de Janeiro">Rio de Janeiro</option>
            </select>
        </form>
        <div class="weather-box">
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= getWeatherDescription($weather_code) ?></p>
            <p class="humidity">Umidade: <?= $humidity ?>%</p>
        </div>

        <h2>Histórico do Clima</h2>
        <ul>
            <?php foreach ($historico as $item): ?>
                <li><?= $item['data_hora'] ?> - <?= $item['temperatura'] ?>°C, <?= $item['descricao'] ?>, Umidade: <?= $item['umidade'] ?>%</li>
            <?php endforeach; ?>
        </ul>

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
                labels: <?= json_encode(range(0, count($data['hourly']['temperature_2m'])-1)) ?>,
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: <?= json_encode($data['hourly']['temperature_2m']) ?>,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            }
        });

        function showNotification() {
            if ("Notification" in window) {
                Notification.requestPermission().then((permission) => {
                    if (permission === "granted") {
                        new Notification("Alerta de Clima", {
                            body: "Mudança brusca de temperatura detectada!",
                            icon: "icons/alert.png"
                        });
                    }
                });
            }
        }

        if (<?= $temperature ?> <= 10 || <?= $temperature ?> >= 35) {
            showNotification();
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
        61 => "Chuva fraca",
        63 => "Chuva moderada",
        65 => "Chuva intensa",
        80 => "Pancadas de chuva",
    ];
    return $descriptions[$code] ?? "Desconhecido";
}
?>
