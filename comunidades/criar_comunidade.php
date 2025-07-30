<?php
include '../php/conexao_comunidade.php'; // Deve retornar um objeto PDO em $connComunidades

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO comunidades (nome, descricao) VALUES (:nome, :descricao)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->execute();

    header("Location: ../comunidade.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Criar Comunidade</title>
</head>
<body>

<h2>Criar Nova Comunidade</h2>
<form method="POST">
    <label>Nome:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Descrição:</label><br>
    <textarea name="descricao" rows="4" cols="50"></textarea><br><br>

    <button type="submit">Criar Comunidade</button>
</form>

</body>
</html>
