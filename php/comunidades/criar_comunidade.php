<?php
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    
    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        // Abra o arquivo de imagem em modo binário
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    $sql = "INSERT INTO comunidades (nome, descricao, imagem) VALUES (:nome, :descricao, :imagem)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
    $stmt->execute();

    header("Location: comunidade.php");
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
<form method="POST" enctype="multipart/form-data">
    <label>Nome:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Descrição:</label><br>
    <textarea name="descricao" rows="4" cols="50"></textarea><br><br>

    <label>Foto de perfil da comunidade:</label><br>
    <input type="file" name="imagem" accept="image/*"><br><br>

    <button type="submit">Criar Comunidade</button>
</form>

</body>
</html>
