<?php
session_start();
include '../conexao.php';

$idusuario = $_SESSION['idusuario'] ?? null;
$nome_usuario = $_SESSION['nome_usuario'] ?? null;

if (!$idusuario) {
    header("Location: ../login/login.php");
}

$categorias = [];
$stmt = $conn->query("SELECT * FROM categoria");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $idcategoria = (int)$_POST['idcategoria'];
    $regras = trim($_POST['regras'] ?? '');

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    $sql = "INSERT INTO comunidades (nome, descricao, imagem, idusuario, idcategoria, status)
            VALUES (:nome, :descricao, :imagem, :idusuario, :idcategoria, 'ativa')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':imagem', $imagem, PDO::PARAM_LOB);
    $stmt->bindParam(':idusuario', $idusuario);
    $stmt->bindParam(':idcategoria', $idcategoria);
    $stmt->execute();

    $idcomunidade = $conn->lastInsertId();

    $stmt = $conn->prepare("
        INSERT INTO membros_comunidade (idcomunidades, idusuario, papel)
        VALUES (:idcomunidade, :idusuario, 'dono')
    ");
    $stmt->execute([':idcomunidade' => $idcomunidade, ':idusuario' => $idusuario]);

    if (!empty($regras)) {
        $regras_array = explode("\n", $regras);
        $stmtRegras = $conn->prepare("
            INSERT INTO regras_comunidade (idcomunidades, regra)
            VALUES (:idcomunidade, :regra)
        ");
        foreach ($regras_array as $r) {
            $r = trim($r);
            if ($r !== '') {
                $stmtRegras->execute([':idcomunidade' => $idcomunidade, ':regra' => $r]);
            }
        }
    }

    header("Location: ver_comunidade.php?id=$idcomunidade");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
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

    <label>Categoria:</label><br>
    <select name="idcategoria" required>
        <option value="">Selecione a categoria</option>
        <?php foreach($categorias as $c): ?>
            <option value="<?= $c['idcategoria'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Regras iniciais (uma por linha, opcional):</label><br>
    <textarea name="regras" rows="4" cols="50" placeholder="Ex: Respeitar os membros&#10;Proibido spam"></textarea><br><br>

    <button type="submit">Criar Comunidade</button>
</form>
</body>
</html>