<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto Clima</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Tela de carregamento -->
    <div class="preloader">
        <div class="loader">
            <div class="cloud"></div>
            <div class="sun"></div>
        </div>
        <p>Carregando...</p>
    </div>

    <!-- Página principal (escondida até o carregamento terminar) -->
    <div class="container fade-in">
        <div class="logo">
            <img src="logo.png" alt="Logo do Projeto">
        </div>
        <h1>Bem-vindo ao Projeto Clima</h1>
        <p>Escolha uma das opções abaixo:</p>

        <div class="menu">
            <a href="mapa.php" class="btn">🌍 Mapa</a>
            <a href="clima.php" class="btn">☀️ Clima</a>
            <a href="previsao.php" class="btn">📅 Previsão da Semana</a>
            <a href="outras.php" class="btn">🔧 Outras Funções</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $(".preloader").fadeOut(800, function() {
                    $(".container").fadeIn(800);
                });
            }, 3000);
        });
    </script>

</body>
</html>
