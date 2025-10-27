<?php
session_start();
include '../conexao.php';

// Pega IDs do usuário e vendedor
$idusuario = $_SESSION['idusuario'] ?? 1; // exemplo — use a sessão real
$idvendedor = $_GET['vendedor'] ?? 1;

// Verifica se já existe uma conversa entre o usuário e o vendedor
$stmt = $conn->prepare("SELECT idconversa FROM conversa WHERE idusuario = ? AND idvendedor = ?");
$stmt->execute([$idusuario, $idvendedor]);
$conversa = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conversa) {
    $idconversa = $conversa['idconversa'];
} else {
    // Cria nova conversa
    $stmt = $conn->prepare("INSERT INTO conversa (idusuario, idvendedor, status) VALUES (?, ?, 'ativada')");
    $stmt->execute([$idusuario, $idvendedor]);
    $idconversa = $conn->lastInsertId();
}

// Se o formulário for enviado, adiciona mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    $remetente_tipo = $_POST['remetente_tipo']; // 'usuario' ou 'vendedor'
    $remetente_id = ($remetente_tipo === 'usuario') ? $idusuario : $idvendedor;

    $stmt = $conn->prepare("INSERT INTO mensagens_chat (idconversa, remetente_tipo, remetente_id, conteudo, lida)
                            VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$idconversa, $remetente_tipo, $remetente_id, $mensagem]);

    header("Location: chat.php?vendedor=$idvendedor");
    exit;
}

// Busca todas as mensagens da conversa
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
.formulario {
    display: flex;
    margin-top: 15px;
}
textarea {
    flex: 1;
    padding: 10px;
    resize: none;
}
button {
    padding: 10px 20px;
    margin-left: 5px;
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
    <h2>Conversa com o vendedor #<?php echo htmlspecialchars($idvendedor); ?></h2>
    <div class="mensagens">
        <?php if (empty($mensagens)): ?>
            <p>Nenhuma mensagem ainda. Comece a conversar!</p>
        <?php else: ?>
            <?php foreach ($mensagens as $msg): ?>
                <div class="mensagem <?php echo $msg['remetente_tipo']; ?>">
                    <strong><?php echo ucfirst($msg['remetente_tipo']); ?>:</strong>
                    <p><?php echo nl2br(htmlspecialchars($msg['conteudo'])); ?></p>
                    <small><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form method="POST" class="formulario">
        <input type="hidden" name="remetente_tipo" value="usuario">
        <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
        <button type="submit">Enviar</button>
    </form>
</div>

</body>
</html>
