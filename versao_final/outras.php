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


    <!-- Conteúdo da página -->
    <div class="section">
        <h1>Outras Funções</h1>
        <p>Explore as ferramentas adicionais do Projeto Clima.</p>

            <h2>📞 Suporte</h2>
            <p>Entre em contato com nossa equipe de suporte preenchendo o formulário abaixo.</p>
            
            <form action="enviar_email.php" method="POST" id="contactForm">
                <input type="text" name="nome" placeholder="Seu Nome" required>
                <input type="email" name="email" placeholder="Seu E-mail" required>
                <textarea name="mensagem" placeholder="Sua mensagem..." required></textarea>
                <button type="submit" class="btn">Enviar</button>
            </form>


        <a href="index.php" class="btn back-btn">⬅️ Voltar</a>
    </div>

<script>
        $(document).ready(function() {
            // Esconder preloader após carregamento da página
            setTimeout(function() {
                $(".preloader").fadeOut(800, function() {
                    $(".container").fadeIn(800);
                });
            }, 3000);

            // Intercepta o envio do formulário
            $("#contactForm").submit(function(event) {
                event.preventDefault(); // Evita o envio padrão do formulário
                
                // Oculta o preloader caso ainda esteja visível
                $(".preloader").hide();

                // Redireciona para a página "Em Desenvolvimento"
                window.location.href = "dev.php";
            });
        });
</script>


</body>
</html>
