<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outras Funções</title>
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

    <!-- Conteúdo da página -->
    <div class="container fade-in">
        <h1>Outras Funções</h1>
        <p>Explore as ferramentas adicionais do Projeto Clima.</p>

        <div class="section">
            <h2>📞 Suporte</h2>
            <p>Entre em contato com nossa equipe de suporte preenchendo o formulário abaixo.</p>
            
            <form action="enviar_email.php" method="POST" class="contact-form">
                <input type="text" name="nome" placeholder="Seu Nome" required>
                <input type="email" name="email" placeholder="Seu E-mail" required>
                <textarea name="mensagem" placeholder="Sua mensagem..." required></textarea>
                <button type="submit" class="btn">Enviar</button>
            </form>
        </div>

        <a href="index.php" class="btn back-btn">⬅️ Voltar</a>
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
