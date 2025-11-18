<?php
session_start();
include '../conexao.php';

$idusuario  = $_GET['idusuario']  ?? $_POST['idusuario']  ?? $_SESSION['idusuario']  ?? null;
$idvendedor = $_GET['idvendedor'] ?? $_POST['idvendedor'] ?? $_SESSION['id_vendedor'] ?? null;
$idconversa = $_GET['idconversa'] ?? $_POST['idconversa'] ?? null;
$remetente_tipo = $_GET['remetente_tipo'] ?? $_POST['remetente_tipo'] ?? 'usuario';

if (!$idusuario || !$idvendedor) {
    die("Erro: usuário ou vendedor não identificado.");
}

// ==== BUSCA DADOS ====
$stmt = $conn->prepare("SELECT * FROM usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM vendedor WHERE idvendedor = ?");
$stmt->execute([$idvendedor]);
$vendedor = $stmt->fetch(PDO::FETCH_ASSOC);

// ==== VERIFICA SE EXISTE CONVERSA ====
$stmt = $conn->prepare("SELECT idconversa FROM conversa WHERE idusuario = ? AND idvendedor = ?");
$stmt->execute([$idusuario, $idvendedor]);
$conversa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conversa) {
    $stmt = $conn->prepare("INSERT INTO conversa (idusuario, idvendedor, status) VALUES (?, ?, 'ativada')");
    $stmt->execute([$idusuario, $idvendedor]);
    $idconversa = $conn->lastInsertId();
} else {
    $idconversa = $conversa['idconversa'];
}

// ==== DEFINE TÍTULO E PÁGINA DE VOLTAR ====
if ($remetente_tipo === 'usuario') {
    $titulo_pagina = "Conversa com o vendedor: " . htmlspecialchars($vendedor['nome_completo']);
    $pagina_voltar = "../perfil-usuario/ver_perfil.php?aba=Chats";
} else {
    $titulo_pagina = "Conversa com o usuário: " . htmlspecialchars($usuario['nome']);
    $pagina_voltar = "../painel-livreiro/lista-chats.php";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title><?= $titulo_pagina ?></title>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .chat-container { max-width: 800px; margin: 40px auto; background: white; border-radius: 10px; box-shadow: 0 0 10px #ccc; padding: 20px; }
        .mensagens { height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 15px; background: #fafafa; }
        .mensagem { margin-bottom: 12px; }
        .mensagem.usuario { text-align: right; }
        .mensagem.vendedor { text-align: left; }
        .mensagem p { display: inline-block; padding: 8px 12px; border-radius: 10px; max-width: 70%; }
        .mensagem.usuario p { background: #d1ffd1; }
        .mensagem.vendedor p { background: #d1e0ff; }
        form textarea { resize: none; height: 60px; }
        .voltar-btn { margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="chat-container">
    <!-- BOTÃO DE VOLTAR -->
    <a href="<?= htmlspecialchars($pagina_voltar) ?>" class="btn btn-secondary voltar-btn">← Voltar</a>

    <!-- CABEÇALHO -->
    <h4><?= $titulo_pagina ?></h4>
    <hr>

    <!-- ÁREA DE MENSAGENS -->
    <div class="mensagens" id="mensagens"></div>

    <!-- FORMULÁRIO -->
    <form id="formMensagem" class="formulario">
        <input type="hidden" name="idconversa" value="<?= $idconversa ?>">
        <input type="hidden" name="idusuario" value="<?= $idusuario ?>">
        <input type="hidden" name="idvendedor" value="<?= $idvendedor ?>">
        <input type="hidden" name="remetente_tipo" value="<?= $remetente_tipo ?>">

        <div class="input-group">
            <textarea name="mensagem" class="form-control" placeholder="Digite sua mensagem..."></textarea>
            <button class="btn btn-primary" type="submit">Enviar</button>
        </div>
    </form>
</div>

<script>
// ========== AJAX ==========
const form = document.getElementById('formMensagem');
const mensagensDiv = document.getElementById('mensagens');
const idconversa = '<?= $idconversa ?>';

// Envia mensagem
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    const res = await fetch('enviar_mensagem.php', { method: 'POST', body: formData });
    const result = await res.text();

    if (result === 'ok') {
        form.mensagem.value = '';
        carregarMensagens();
    } else {
        alert(result);
    }
});

// Carrega mensagens
async function carregarMensagens() {
    const res = await fetch(`carregar_mensagens.php?idconversa=${idconversa}`);
    const mensagens = await res.json();

    mensagensDiv.innerHTML = '';
    mensagens.forEach(msg => {
        const div = document.createElement('div');
        div.classList.add('mensagem', msg.remetente_tipo);
        div.innerHTML = `
            <p>${msg.conteudo}</p>
            <small>${msg.data_envio}</small>
        `;
        mensagensDiv.appendChild(div);
    });
    mensagensDiv.scrollTop = mensagensDiv.scrollHeight;
}

// Atualiza a cada 2 segundos
setInterval(carregarMensagens, 2000);
carregarMensagens();
</script>
</body>
</html>
