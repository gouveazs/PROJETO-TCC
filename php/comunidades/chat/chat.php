<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario']; 
$id_comunidade = $_GET['id_comunidade'];

// Verifica se participa
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

// Nome da comunidade
$stmtCom = $conn->prepare("SELECT nome FROM comunidades WHERE idcomunidades = :id");
$stmtCom->execute([':id' => $id_comunidade]);
$comunidade = $stmtCom->fetch(PDO::FETCH_ASSOC);
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
    overflow-y: auto;
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
</style>
</head>
<body>

<h2>Chat da Comunidade: <?= htmlspecialchars($comunidade['nome']) ?></h2>

<div id="chat-box"></div>

<form id="chat-form">
    <textarea name="mensagem" id="mensagem" rows="3" placeholder="Digite sua mensagem..." required></textarea><br>
    <button type="submit">Enviar</button>
</form>

<script>
// Função para carregar mensagens
async function carregarMensagens() {
    let resp = await fetch("buscar_mensagens.php?id_comunidade=<?= $id_comunidade ?>");
    let mensagens = await resp.json();

    let chatBox = document.getElementById("chat-box");
    chatBox.innerHTML = "";

    mensagens.forEach(msg => {
        chatBox.innerHTML += `
            <div class="mensagem">
                <img src="data:image/jpeg;base64,${msg.foto_de_perfil}" alt="Perfil">
                <div class="conteudo">
                    <strong>${msg.nome}:</strong><br>
                    ${msg.mensagem}<br>
                    <small>${msg.enviada_em}</small>
                </div>
            </div>
        `;
    });

    chatBox.scrollTop = chatBox.scrollHeight; // rolar pro fim
}

// Atualiza mensagens a cada 2s
setInterval(carregarMensagens, 2000);
carregarMensagens();

// Envio da mensagem via AJAX
document.getElementById("chat-form").addEventListener("submit", async function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("id_comunidade", <?= $id_comunidade ?>);

    let resp = await fetch("enviar_mensagem.php", {
        method: "POST",
        body: formData
    });

    if (resp.ok) {
        document.getElementById("mensagem").value = "";
        carregarMensagens();
    }
});
</script>

</body>
</html>
