<?php
include '../conexao.php';
session_start();

$idusuario = $_SESSION['idusuario'] ?? null;

if (!$idusuario) {
    die("Erro: usuário não autenticado. Faça login novamente.");
}

$categorias = [];
    $stmt = $conn->query("SELECT * FROM categoria");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categorias[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $idcategoria = $_POST['idcategoria'];

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    $sql = "INSERT INTO comunidades (nome, descricao, imagem, idusuario, idcategoria)
        VALUES (:nome, :descricao, :imagem, :idusuario, :idcategoria)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
    $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
    $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
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

        <label for="categoria">Categoria:</label>
        <select name="idcategoria" id="categoria" required>
        <option value="">Selecione a categoria</option>
            <?php foreach($categorias as $categoria): ?>
            <option value="<?= $categoria['idcategoria'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Criar Comunidade</button>
    </form>
</body>
</html>