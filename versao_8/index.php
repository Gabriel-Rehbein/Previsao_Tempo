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

    if (isset($locations[$city])) {
        $lat = $locations[$city]['lat'];
        $lon = $locations[$city]['lon'];
    } else {
        die("Cidade não encontrada.");
    }
}

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current_weather=true&daily=temperature_2m_min,temperature_2m_max,weathercode&hourly=temperature_2m&timezone=America%2FSao_Paulo";

// Verifica se a API está respondendo corretamente
$response = @file_get_contents($url);
if (!$response) {
    die("Erro ao buscar dados da API.");
}

$data = json_decode($response, true);

// Debug: Verifique a estrutura da resposta
if (!isset($data['current_weather'])) {
    die("Erro: Estrutura da resposta da API mudou ou está vazia.");
}

$temperature = $data['current_weather']['temperature'];
$weather_code = $data['current_weather']['weathercode'];
$hourlyTemperatures = $data['hourly']['temperature_2m'] ?? [];

// Substituindo a umidade (já que não é retornada pela API)
$min_temperature = $data['daily']['temperature_2m_min'][0] ?? "N/A";

// Verifica a conexão com o banco
if (!isset($pdo)) {
    die("Erro ao conectar com o banco de dados.");
}

// Insere o histórico do clima no banco de dados
try {
    $stmt = $pdo->prepare("INSERT INTO historico_clima (cidade, temperatura, descricao, umidade) VALUES (?, ?, ?, ?)");
    $stmt->execute([$city, $temperature, getWeatherDescription($weather_code), $min_temperature]);
} catch (PDOException $e) {
    die("Erro ao inserir no banco: " . $e->getMessage());
}

// Obtém o histórico salvo
try {
    $historico = $pdo->query("SELECT * FROM historico_clima WHERE cidade = '$city' ORDER BY data_hora DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    die("Erro ao recuperar o histórico: " . $e->getMessage());
}
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
            </select>
        </form>
        <div class="weather-box">
            <p class="temperature"><?= $temperature ?>°C</p>
            <p class="description"><?= getWeatherDescription($weather_code) ?></p>
            <p class="humidity">Mínima: <?= $min_temperature ?>°C</p>
        </div>

        <h2>Histórico do Clima</h2>
        <ul>
            <?php foreach ($historico as $item): ?>
                <li><?= htmlspecialchars($item['data_hora']) ?> - <?= $item['temperatura'] ?>°C, <?= htmlspecialchars($item['descricao']) ?>, Mínima: <?= $item['umidade'] ?>°C</li>
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
