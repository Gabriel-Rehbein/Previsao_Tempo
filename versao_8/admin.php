<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// CRUD para cidades
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $nome = $_POST['nome'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        $stmt = $pdo->prepare("INSERT INTO cidades (nome, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $latitude, $longitude]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM cidades WHERE id = ?")->execute([$id]);
    }
}

// Obtendo cidades
$cidades = $pdo->query("SELECT * FROM cidades")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Painel Administrativo</h1>
        <a href="logout.php">Sair</a>

        <h2>Adicionar Cidade</h2>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome da cidade" required>
            <input type="text" name="latitude" placeholder="Latitude" required>
            <input type="text" name="longitude" placeholder="Longitude" required>
            <button type="submit" name="add">Adicionar</button>
        </form>

        <h2>Cidades Salvas</h2>
        <ul>
            <?php foreach ($cidades as $cidade): ?>
                <li>
                    <?= $cidade['nome'] ?> (<?= $cidade['latitude'] ?>, <?= $cidade['longitude'] ?>)
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $cidade['id'] ?>">
                        <button type="submit" name="delete">Remover</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
