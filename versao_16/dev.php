<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Em Desenvolvimento</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Corpo da página */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(45deg, #ff6a00, #ee0979);
            animation: gradientAnimation 5s infinite alternate;
            text-align: center;
            color: white;
        }

        /* Animação do fundo */
        @keyframes gradientAnimation {
            0% {
                background: linear-gradient(45deg, #ff6a00, #ee0979);
            }
            100% {
                background: linear-gradient(45deg, #ee0979, #ff6a00);
            }
        }

        /* Texto animado */
        h1 {
            font-size: 4rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 5px;
            animation: pulse 1.5s infinite alternate ease-in-out;
        }

        /* Animação do texto */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        /* Responsividade */
        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <h1>Em Desenvolvimento</h1>
</body>
</html>
