<?php
include '../php/conexao_comunidade.php';

$id_usuario_externo = 1; // ID fixo do usuário logado (exemplo)
$id_comunidade = $_GET['id_comunidade'] ?? 0;

// Verifica se o usuário participa da comunidade
$stmtVer = $conn->prepare("
    SELECT * FROM membros_comunidade
    WHERE id_comunidade = :id AND id_usuario_externo = :usuario
");
$stmtVer->execute([
    ':id' => $id_comunidade,
    ':usuario' => $id_usuario_externo
]);
$participa = $stmtVer->fetch(PDO::FETCH_ASSOC);

if (!$participa) {
    die("Você não participa desta comunidade.");
}

// Enviar nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensagem'])) {
    $mensagem = $_POST['mensagem'];

    $stmtMsg = $conn->prepare("
        INSERT INTO mensagens_chat (id_comunidade, id_usuario_externo, mensagem)
        VALUES (:id_comunidade, :id_usuario, :mensagem)
    ");
    $stmtMsg->execute([
        ':id_comunidade' => $id_comunidade,
        ':id_usuario' => $id_usuario_externo,
        ':mensagem' => $mensagem
    ]);

    header("Location: chat.php?id_comunidade=$id_comunidade");
    exit;
}

// Buscar nome da comunidade
$stmtCom = $conn->prepare("SELECT nome FROM comunidades WHERE id = :id");
$stmtCom->execute([':id' => $id_comunidade]);
$comunidade = $stmtCom->fetch(PDO::FETCH_ASSOC);

// Buscar mensagens da comunidade
$stmtMensagens = $conn->prepare("
    SELECT * FROM mensagens_chat
    WHERE id_comunidade = :id
    ORDER BY enviada_em ASC
");
$stmtMensagens->execute([':id' => $id_comunidade]);
$mensagens = $stmtMensagens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chat - <?= htmlspecialchars($comunidade['nome']) ?></title>
    <style>
        #chat-box {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            overflow-y: scroll;
            padding: 10px;
            margin-bottom: 10px;
            background: #f9f9f9;
        }
        .mensagem {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
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
            <strong>Usuário #<?= $msg['id_usuario_externo'] ?>:</strong><br>
            <?= nl2br(htmlspecialchars($msg['mensagem'])) ?><br>
            <small><?= $msg['enviada_em'] ?></small>
        </div>
    <?php endforeach; ?>
</div>

<form method="POST">
    <textarea name="mensagem" rows="3" placeholder="Digite sua mensagem..." required></textarea><br>
    <button type="submit">Enviar</button>
</form>

</body>
</html>
