<?php
session_start();
include '../conexao.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_comunidade = $_GET['id_comunidade'] ?? ($_POST['id_comunidade'] ?? null);

if(!$id_usuario || !$id_comunidade){
    die("Acesso inválido.");
}

// Busca comunidade
$stmt = $conn->prepare("SELECT * FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([":id" => $id_comunidade]);
$com = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$com || $com['idusuario'] != $id_usuario){
    die("Apenas o dono pode editar esta comunidade.");
}

// Se o formulário foi enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    $nome = $_POST['nome'] ?? null;
    $descricao = $_POST['descricao'] ?? null;

    if(!$nome || !$descricao){
        die("Preencha todos os campos.");
    }

    // Se tiver nova foto
    if(isset($_FILES['foto']) && $_FILES['foto']['tmp_name']){
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        $stmt = $conn->prepare("UPDATE comunidades 
                                SET nome = :nome, descricao = :descricao, imagem = :foto 
                                WHERE idcomunidades = :id");
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":foto", $foto, PDO::PARAM_LOB);
        $stmt->bindParam(":id", $id_comunidade);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("UPDATE comunidades 
                                SET nome = :nome, descricao = :descricao 
                                WHERE idcomunidades = :id");
        $stmt->execute([":nome" => $nome, ":descricao" => $descricao, ":id" => $id_comunidade]);
    }

    header("Location: chat/chat.php?id_comunidade=$id_comunidade&msg=editado");
    exit;
}

// Desativar/Reativar comunidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'status') {
    $novo_status = ($com['status'] === 'ativa') ? 'desativada' : 'ativa';

    $stmt = $conn->prepare("UPDATE comunidades SET status = :status WHERE idcomunidades = :id");
    $stmt->execute([":status" => $novo_status, ":id" => $id_comunidade]);

    header("Location: editar_comunidade.php?id_comunidade=$id_comunidade&msg=status");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Comunidade</title>
</head>
<body>
<h2>Editar Comunidade</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_comunidade" value="<?= $id_comunidade ?>">
    <input type="hidden" name="acao" value="editar">

    <label>Nome:</label><br>
    <input type="text" name="nome" value="<?= htmlspecialchars($com['nome']) ?>" required><br><br>

    <label>Descrição:</label><br>
    <textarea name="descricao" rows="5" cols="40" required><?= htmlspecialchars($com['descricao']) ?></textarea><br><br>

    <label>Foto da Comunidade:</label><br>
    <input type="file" name="foto"><br>
    <?php if(!empty($com['imagem'])): ?>
        <p>Foto atual:</p>
        <img src="data:image/jpeg;base64,<?= base64_encode($com['imagem']) ?>" width="120"><br>
    <?php endif; ?>
    <br>

    <button type="submit">Salvar Alterações</button>
</form>

<hr>
<h3>Status da Comunidade: <?= strtoupper($com['status']) ?></h3>
<form method="POST">
    <input type="hidden" name="id_comunidade" value="<?= $id_comunidade ?>">
    <input type="hidden" name="acao" value="status">
    <button type="submit" style="background:<?= $com['status']==='ativa' ? 'red' : 'green' ?>; color:white; padding:8px 12px; border:none; border-radius:5px;">
        <?= $com['status']==='ativa' ? 'Desativar Comunidade' : 'Reativar Comunidade' ?>
    </button>
</form>

<br>
<a href="chat/chat.php?id_comunidade=<?= $id_comunidade ?>">Voltar ao Chat</a>
</body>
</html>
