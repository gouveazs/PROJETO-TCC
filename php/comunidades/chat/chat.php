<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_comunidade = $_GET['id_comunidade'] ?? null;
if (!$id_usuario || !$id_comunidade) die("Acesso inválido.");

// Pega info da comunidade
$stmt = $conn->prepare("SELECT nome, idusuario AS id_criador FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([':id' => $id_comunidade]);
$comunidade = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$comunidade) die("Comunidade não encontrada.");

// Verifica se usuário participa
$stmtVer = $conn->prepare("SELECT papel FROM membros_comunidade WHERE idcomunidades = :id AND idusuario = :usuario");
$stmtVer->execute([':id' => $id_comunidade, ':usuario' => $id_usuario]);
$participa = $stmtVer->fetch(PDO::FETCH_ASSOC);
if (!$participa) die("Você não participa desta comunidade.");

$usuario_papel = $participa['papel'];
$admin = ($id_usuario == $comunidade['id_criador']);
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
<?php if($admin): ?>
    <a href="membros/ver_membros.php?id_comunidade=<?= $id_comunidade ?>">Ver membros</a>
<?php endif; ?>

<div id="chat-box"></div>

<form id="chat-form">
    <textarea name="mensagem" id="mensagem" rows="3" placeholder="Digite sua mensagem..." required></textarea><br>
    <label>
        <input type="checkbox" id="spoiler" name="spoiler"> Mensagem contém spoiler
    </label><br>
    <button type="submit">Enviar</button>
    <p id="msg-erro" style="color:red; font-weight:bold;"></p>
</form>

<script>
const papelUsuario = "<?= $usuario_papel ?>"; // 'membro', 'moderador' ou 'dono'

async function carregarMensagens() {
    let resp = await fetch("buscar_mensagens.php?id_comunidade=<?= $id_comunidade ?>");
    let mensagens = await resp.json();

    let chatBox = document.getElementById("chat-box");
    chatBox.innerHTML = "";

    mensagens.forEach(msg => {
        let conteudo = msg.mensagem;

        // Detecta spoiler
        if (conteudo.includes("[spoiler]")) {
            conteudo = conteudo.replace("[spoiler]", "").replace("[/spoiler]", "");
            conteudo = `
                <div class="spoiler">
                    <button onclick="this.nextElementSibling.style.display='block'; this.style.display='none';">
                        Mostrar Spoiler
                    </button>
                    <div style="display:none; margin-top:5px; background:#eee; padding:5px; border-radius:5px;">
                        ${conteudo}
                    </div>
                </div>
            `;
        }

        // Botão de excluir
        let botaoExcluir = "";
        if (papelUsuario === "moderador" || papelUsuario === "dono") {
            botaoExcluir = `<br><button onclick="excluirMensagem(${msg.idmensagens_chat})">Excluir</button>`;
        }

        chatBox.innerHTML += `
            <div class="mensagem">
                <img src="data:image/jpeg;base64,${msg.foto_de_perfil}" alt="Perfil">
                <div class="conteudo">
                    <strong>${msg.nome} (${msg.papel}):</strong><br>
                    ${conteudo}${botaoExcluir}<br>
                    <small>${msg.enviada_em}</small>
                </div>
            </div>
        `;
    });

    chatBox.scrollTop = chatBox.scrollHeight;
}

setInterval(carregarMensagens, 2000);
carregarMensagens();

document.getElementById("chat-form").addEventListener("submit", async function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append("id_comunidade", <?= $id_comunidade ?>);
    formData.append("spoiler", document.getElementById("spoiler").checked ? 1 : 0);

    let resp = await fetch("enviar_mensagem.php", { method: "POST", body: formData });
    let result = await resp.json();
    let msgErro = document.getElementById("msg-erro");

    if(resp.ok && !result.erro) {
        document.getElementById("mensagem").value = "";
        document.getElementById("spoiler").checked = false;
        msgErro.innerText = "";
        carregarMensagens();
    } else {
        msgErro.innerText = result.erro || "Erro ao enviar mensagem.";
    }
});

async function excluirMensagem(id_mensagem) {
    if(!confirm("Deseja realmente apagar esta mensagem?")) return;

    let formData = new FormData();
    formData.append('id_comunidade', <?= $id_comunidade ?>);
    formData.append('id_mensagem', id_mensagem);

    let resp = await fetch("excluir_mensagem.php", { method: "POST", body: formData });
    let result = await resp.json();

    if(result.sucesso){
        carregarMensagens();
    } else {
        alert(result.mensagem);
    }
}
</script>

</body>
</html>
