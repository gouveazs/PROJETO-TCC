<?php
session_start();
include '../conexao.php'; // conexão única com seu banco

// Usuário logado
$id_usuario = $_SESSION['idusuario']; // exemplo fixo se não estiver logado
$id_comunidade = $_GET['id_comunidade'];

// Verifica se o usuário participa da comunidade
$stmtVer = $conn->prepare("
    SELECT * FROM membros_comunidade
    WHERE idcomunidades = :id AND idusuario = :usuario
");
$stmtVer->execute([
    ':id' => $id_comunidade,
    ':usuario' => $id_usuario
]);
$participa = $stmtVer->fetch(PDO::FETCH_ASSOC);

if (!$participa) {
    die("Você não participa desta comunidade.");
}

// Enviar nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensagem'])) {
    $mensagem = $_POST['mensagem'];

    $stmtMsg = $conn->prepare("
        INSERT INTO mensagens_chat (idcomunidades, idusuario, mensagem)
        VALUES (:id_comunidade, :id_usuario, :mensagem)
    ");
    $stmtMsg->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario,
        ':mensagem' => $mensagem
    ]);

    header("Location: chat.php?id_comunidade=$id_comunidade");
    exit;
}

// Buscar nome da comunidade
$stmtCom = $conn->prepare("SELECT nome FROM comunidades WHERE idcomunidades = :id");
$stmtCom->execute([':id' => $id_comunidade]);
$comunidade = $stmtCom->fetch(PDO::FETCH_ASSOC);

// Buscar mensagens da comunidade com dados do usuário
$stmtMensagens = $conn->prepare("
    SELECT m.mensagem, m.enviada_em, u.nome, u.foto_de_perfil
    FROM mensagens_chat m
    JOIN usuario u ON m.idusuario = u.idusuario
    WHERE m.idcomunidades = :id
    ORDER BY m.enviada_em ASC
");
$stmtMensagens->execute([':id' => $id_comunidade]);
$mensagens = $stmtMensagens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Chat - <?= htmlspecialchars($comunidade['nome']) ?></title>
<style>
#chat-box {
    width: 100%;
    height: 400px;
    border: 1px solid #ccc;
    overflow-y: scroll;
    padding: 10px;
    margin-bottom: 10px;
    background: #f9f9f9;
}
.mensagem {
    display: flex;
    align-items: flex-start;
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}
.mensagem img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}
.mensagem .conteudo {
    max-width: 90%;
}
.mensagem strong {
    color: #333;
}
textarea {
    width: 100%;
    resize: none;
}
</style>
</head>
<body>

<h2>Chat da Comunidade: <?= htmlspecialchars($comunidade['nome']) ?></h2>

<div id="chat-box">
    <?php foreach ($mensagens as $msg): ?>
        <div class="mensagem">
            <img src="data:image/jpeg;base64,<?= base64_encode($msg['foto_de_perfil']) ?>" alt="Perfil">
            <div class="conteudo">
                <strong><?= htmlspecialchars($msg['nome']) ?>:</strong><br>
                <?= nl2br(htmlspecialchars($msg['mensagem'])) ?><br>
                <small><?= $msg['enviada_em'] ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<form method="POST">
    <textarea name="mensagem" rows="3" placeholder="Digite sua mensagem..." required></textarea><br>
    <button type="submit">Enviar</button>
</form>

</body>
</html>
