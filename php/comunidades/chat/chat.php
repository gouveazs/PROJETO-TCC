<?php
session_start();
include '../../conexao.php';

$id_usuario = $_SESSION['idusuario'] ?? null;
$id_comunidade = $_GET['id_comunidade'] ?? null;
if (!$id_usuario || !$id_comunidade) die("Acesso inválido.");

// Pega info da comunidade
$stmt = $conn->prepare("SELECT nome, idusuario AS id_criador, status FROM comunidades WHERE idcomunidades = :id");
$stmt->execute([':id' => $id_comunidade]);
$comunidade = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$comunidade) die("Comunidade não encontrada.");

// Verifica se usuário participa
$stmtVer = $conn->prepare("SELECT papel FROM membros_comunidade WHERE idcomunidades = :id AND idusuario = :usuario");
$stmtVer->execute([':id' => $id_comunidade, ':usuario' => $id_usuario]);
$participa = $stmtVer->fetch(PDO::FETCH_ASSOC);
if (!$participa) die("Você não participa desta comunidade.");

$usuario_papel = $participa['papel'];

// Administrador da comunidade = criador
$admin = ($id_usuario == $comunidade['id_criador']);

$comunidadeAtiva = ($comunidade['status'] === 'ativa');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Chat - <?= htmlspecialchars($comunidade['nome']) ?></title>

<style>
    * { font-family: Arial, sans-serif !important; }
    body {
        background: #f8f6f2;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
    }
    .container {
        width: 85%;
        max-width: 900px;
        background: #ffffff;
        margin-top: 40px;
        padding: 0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .chat-header {
        background: #4d5f3a;
        color: white;
        padding: 18px 25px;
        font-size: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .admin-menu-btn {
        background: transparent;
        border: 1px solid #fff;
        padding: 6px 14px;
        border-radius: 6px;
        color: white;
        cursor: pointer;
    }
    .admin-menu {
        display: none;
        position: absolute;
        right: 60px;
        top: 60px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        width: 180px;
        z-index: 20;
    }
    .admin-menu a, .admin-menu div {
        padding: 12px;
        font-size: 14px;
        cursor: pointer;
        display: block;
        text-decoration: none;
        color: #333;
    }
    .admin-menu a:hover, .admin-menu div:hover {
        background: #f0f0f0;
    }
    #chat-box {
        height: 480px;
        overflow-y: auto;
        background: #fcfbf9;
        padding: 25px;
    }
    .mensagem {
        display: flex;
        margin-bottom: 20px;
        width: 100%;
        align-items: flex-start;
        position: relative;
    }
    .eu { justify-content: flex-end; }
    .bubble {
        background: #f1f1f1;
        padding: 12px 15px;
        border-radius: 12px;
        max-width: 65%;
        position: relative;
        border: 1px solid #e8e8e8;
        font-size: 14px;
    }
    .eu .bubble {
        background: #4d5f3a;
        color: white;
        border: none;
    }
    .delete-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #c0392b;
        color: white;
        border: none;
        padding: 3px 6px;
        font-size: 10px;
        border-radius: 4px;
        cursor: pointer;
        display: none;
    }
    .mensagem:hover .delete-btn {
        display: block;
    }
    .timestamp {
        font-size: 11px;
        opacity: 0.6;
        display: block;
        margin-top: 5px;
    }
    .avatar-esq, .avatar-dir {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
    .avatar-esq { margin-right: 12px; }
    .avatar-dir { margin-left: 12px; }
    .input-area {
        padding: 15px 20px;
        background: #f0eee9;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    textarea {
        width: 100%;
        resize: none;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #bbbbbb;
        background: white;
        font-size: 14px;
    }
    button {
        background: #4d5f3a;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
    }
</style>
</head>
<body>

<div class="container">

    <div class="chat-header">
        <span>Chat da Comunidade: <?= htmlspecialchars($comunidade['nome']) ?></span>

        <div style="display:flex; gap:10px; align-items:center;">
            <?php if($admin): ?>
            <button class="admin-menu-btn" onclick="toggleAdminMenu()">Gerenciar</button>
            <?php endif; ?>

            <a href="../comunidade.php" style="color:white; border:1px solid #fff; padding:6px 14px; border-radius:6px; text-decoration:none;">
                ← Voltar
            </a>
        </div>
    </div>

    <!-- MENU ADM -->
    <div class="admin-menu" id="adminMenu">
        <a href="membros.php?id_comunidade=<?= $id_comunidade ?>">👥 Ver membros</a>
        <a href="editar.php?id_comunidade=<?= $id_comunidade ?>">⚙ Editar comunidade</a>

        <?php if($admin): ?>
        <div onclick="excluirComunidade()">🗑 Excluir comunidade</div>
        <?php endif; ?>
    </div>

    <div id="chat-box"></div>

    <?php if($comunidadeAtiva): ?>
    <form id="chat-form" class="input-area">
        <textarea name="mensagem" id="mensagem" rows="2" placeholder="Digite sua mensagem..." required></textarea>
        <label style="font-size: 12px;">
            <input type="checkbox" id="spoiler" name="spoiler"> Spoiler
        </label>
        <button type="submit">Enviar</button>
    </form>
    <?php endif; ?>

</div>

<script>
const idUsuario = <?= $id_usuario ?>;
const admin = <?= $admin ? "true" : "false" ?>;

function toggleAdminMenu() {
    let menu = document.getElementById("adminMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function excluirComunidade() {
    if (confirm("Tem certeza que deseja excluir esta comunidade?")) {
        window.location.href = "excluir.php?id_comunidade=<?= $id_comunidade ?>";
    }
}

async function carregarMensagens() {
    let resp = await fetch("buscar_mensagens.php?id_comunidade=<?= $id_comunidade ?>");
    if (!resp.ok) return;

    let mensagens = await resp.json();
    let chatBox = document.getElementById("chat-box");
    chatBox.innerHTML = "";

    mensagens.forEach(msg => {
        let eu = msg.idusuario == idUsuario;
        let podeExcluir = admin || eu;
        let conteudo = msg.mensagem || "";

        chatBox.innerHTML += `
            <div class="mensagem ${eu ? 'eu' : ''}">
                ${!eu ? `<img class="avatar-esq" src="data:image/jpeg;base64,${msg.foto_de_perfil}">` : ""}

                <div class="bubble">
                    <strong>${msg.nome}</strong><br>
                    ${conteudo}
                    <span class="timestamp">${msg.enviada_em}</span>

                    ${podeExcluir ? `
                        <button class="delete-btn" onclick="excluirMsg(${msg.idmensagem})">X</button>
                    ` : ""}
                </div>

                ${eu ? `<img class="avatar-dir" src="data:image/jpeg;base64,${msg.foto_de_perfil}">` : ""}
            </div>
        `;
    });

    chatBox.scrollTop = chatBox.scrollHeight;
}

async function excluirMsg(id) {
    if (!confirm("Excluir mensagem?")) return;
    await fetch("excluir_mensagem.php?id=" + id);
    carregarMensagens();
}

setInterval(carregarMensagens, 2000);
carregarMensagens();
</script>

</body>
</html>
