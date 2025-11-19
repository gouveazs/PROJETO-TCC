<?php
session_start();
include '../conexao.php';

$idusuario  = $_GET['idusuario']  ?? $_POST['idusuario']  ?? $_SESSION['idusuario']  ?? null;
$idvendedor = $_GET['idvendedor'] ?? $_POST['idvendedor'] ?? $_SESSION['id_vendedor'] ?? null;
$idconversa = $_GET['idconversa'] ?? $_POST['idconversa'] ?? null;
$remetente_tipo = $_GET['remetente_tipo'] ?? $_POST['remetente_tipo'] ?? 'usuario';

if (!$idusuario) {
    header('Location: ../login/login.php');
    exit;
}

if (!$idvendedor) {
    header('Location: ../login/loginVendedor.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuario WHERE idusuario = ?");
$stmt->execute([$idusuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM vendedor WHERE idvendedor = ?");
$stmt->execute([$idvendedor]);
$vendedor = $stmt->fetch(PDO::FETCH_ASSOC);

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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --verde: #5a6b50;
            --background: #F4F1EE;
            --text-color: #333;
            --border-radius: 12px;
        }
        
        body { 
            font-family: 'Playfair Display', serif; 
            background: var(--background); 
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .chat-container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            border-radius: var(--border-radius); 
            box-shadow: 0 4px 20px rgba(90, 107, 80, 0.1); 
            overflow: hidden;
            border: 1px solid #e8e0d8;
        }
        
        .chat-header {
            background: var(--verde);
            color: white;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #4a5a40;
        }
        
        .chat-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
            color: white;
        }
        
        .voltar-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }
        
        .voltar-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.4);
            color: white;
            text-decoration: none;
        }
        
        .mensagens { 
            height: 400px; 
            overflow-y: auto; 
            padding: 24px; 
            background: var(--background); 
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .mensagem { 
            display: flex;
            max-width: 80%;
        }
        
        .mensagem.usuario { 
            align-self: flex-end;
        }
        
        .mensagem.vendedor { 
            align-self: flex-start;
        }
        
        .mensagem p { 
            padding: 12px 16px; 
            border-radius: 18px; 
            margin: 0;
            line-height: 1.4;
            font-size: 14px;
            position: relative;
            font-family: 'Playfair Display', serif;
        }
        
        .mensagem.usuario p { 
            background: var(--verde);
            color: white;
            border-bottom-right-radius: 6px;
            border: 1px solid #4a5a40;
        }
        
        .mensagem.vendedor p { 
            background: white;
            border: 1px solid #d6c6b4;
            border-bottom-left-radius: 6px;
            color: #5a4224;
        }
        
        .mensagem small {
            display: block;
            font-size: 11px;
            margin-top: 6px;
            opacity: 0.7;
            font-weight: 400;
        }
        
        .mensagem.usuario small {
            color: rgba(255,255,255,0.8);
        }
        
        .mensagem.vendedor small {
            color: rgba(90, 66, 36, 0.7);
        }
        
        .formulario {
            padding: 20px 24px;
            border-top: 1px solid #e8e0d8;
            background: white;
        }
        
        .input-group {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        
        form textarea { 
            resize: none; 
            height: 60px; 
            border-radius: 8px;
            border: 1px solid #d6c6b4;
            padding: 12px 16px;
            font-family: 'Playfair Display', serif;
            flex: 1;
            background: var(--background);
            font-size: 14px;
            transition: all 0.2s;
        }
        
        form textarea:focus {
            outline: none;
            border-color: var(--verde);
            box-shadow: 0 0 0 2px rgba(90, 107, 80, 0.2);
            background: white;
        }
        
        form textarea::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .btn-primary {
            background: var(--verde);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            transition: all 0.2s;
            font-weight: 600;
            color: white;
            height: 60px;
            font-family: 'Playfair Display', serif;
        }
        
        .btn-primary:hover {
            background: #4a5a40;
            transform: translateY(-1px);
        }
        
        /* Scrollbar personalizada */
        .mensagens::-webkit-scrollbar {
            width: 6px;
        }
        
        .mensagens::-webkit-scrollbar-track {
            background: #e9e2da;
        }
        
        .mensagens::-webkit-scrollbar-thumb {
            background: #c4b19e;
            border-radius: 3px;
        }
        
        .mensagens::-webkit-scrollbar-thumb:hover {
            background: #b4a18e;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            
            .chat-container {
                margin: 0;
                border-radius: 8px;
            }
            
            .mensagem {
                max-width: 90%;
            }
            
            .chat-header {
                padding: 16px 20px;
            }
            
            .mensagens {
                padding: 20px;
                height: 50vh;
            }
            
            .formulario {
                padding: 16px 20px;
            }
        }
        
      
    </style>
</head>
<body>
<div class="chat-container">
    <!-- CABEÇALHO -->
    <div class="chat-header">
        <h4><?= $titulo_pagina ?></h4>
        <a href="<?= htmlspecialchars($pagina_voltar) ?>" class="voltar-btn">← Voltar</a>
    </div>

    <!-- ÁREA DE MENSAGENS -->
    <div class="mensagens" id="mensagens">
        <!-- Mensagens serão carregadas aqui via JavaScript -->
    </div>

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
        
        // Formata a data
        const data = new Date(msg.data_envio);
        const dataFormatada = data.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        div.innerHTML = `
            <p>${msg.conteudo}<small>${dataFormatada}</small></p>
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