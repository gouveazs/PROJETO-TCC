<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include '../conexao.php';

$nome = $_SESSION['nome_usuario'] ?? null;
$idusuario = $_SESSION['idusuario'] ?? 1;
$idvendedor = $_GET['idvendedor'] ?? 1;
$remetente_tipo = $_GET['remetente_tipo'] ?? 'usuario';

// Verifica se já existe conversa
$stmt = $conn->prepare("SELECT idconversa FROM conversa WHERE idusuario = ? AND idvendedor = ?");
$stmt->execute([$idusuario, $idvendedor]);
$conversa = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversa) {
    $idconversa = $conversa['idconversa'];
} else {
    $stmt = $conn->prepare("INSERT INTO conversa (idusuario, idvendedor, status) VALUES (?, ?, 'ativada')");
    $stmt->execute([$idusuario, $idvendedor]);
    $idconversa = $conn->lastInsertId();
}

// Envio da mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    $remetente_tipo = $_POST['remetente_tipo'];
    $remetente_id = ($remetente_tipo === 'usuario') ? $idusuario : $idvendedor;

    $stmt = $conn->prepare("INSERT INTO mensagens_chat (idconversa, remetente_tipo, remetente_id, conteudo, lida)
                            VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$idconversa, $remetente_tipo, $remetente_id, $mensagem]);

    header("Location: chat.php?idvendedor=$idvendedor&remetente_tipo=$remetente_tipo");
    exit;
}

// Busca todas as mensagens
$stmt = $conn->prepare("SELECT * FROM mensagens_chat WHERE idconversa = ? ORDER BY data_envio ASC");
$stmt->execute([$idconversa]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Chat</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
    }
    .chat-box {
        width: 600px;
        margin: 30px auto;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px #ccc;
    }
    .mensagem {
        margin: 10px 0;
        padding: 10px;
        border-radius: 10px;
        max-width: 75%;
    }
    .usuario {
        background: #DCF8C6;
        text-align: right;
        margin-left: auto;
    }
    .vendedor {
        background: #eee;
        text-align: left;
        margin-right: auto;
    }
    .mensagens {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 10px;
    }
    .formulario {
        display: flex;
        gap: 5px;
        margin-top: 15px;
    }
    textarea {
        flex: 1;
        padding: 10px;
        resize: none;
        border-radius: 5px;
        border: 1px solid #ccc;
        height: 60px;
    }
    button {
        padding: 10px 20px;
        background: #007BFF;
        border: none;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background: #0056b3;
    }
</style>
</head>
<body>

<div class="chat-box">
    <h2>
        <?php if ($remetente_tipo === 'usuario'): ?>
            Conversa com o vendedor #<?= htmlspecialchars($idvendedor) ?>
        <?php else: ?>
            Conversa com o usuário #<?= htmlspecialchars($idusuario) ?>
        <?php endif; ?>
    </h2>

    <div class="mensagens">
        <?php if (empty($mensagens)): ?>
            <p>Nenhuma mensagem ainda. Comece a conversar!</p>
        <?php else: ?>
            <?php foreach ($mensagens as $msg): ?>
                <div class="mensagem <?= htmlspecialchars($msg['remetente_tipo']) ?>">
                    <strong><?= ucfirst($msg['remetente_tipo']) ?>:</strong>
                    <p><?= nl2br(htmlspecialchars($msg['conteudo'])) ?></p>
                    <small><?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method="POST" class="formulario">
        <input type="hidden" name="remetente_tipo" value="<?= htmlspecialchars($remetente_tipo) ?>">
        <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
        <button type="submit">Enviar</button>
    </form>
</div>

</body>
</html>
