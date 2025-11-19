<?php
session_start();

$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;
$foto_de_perfil = $_SESSION['foto_de_perfil_vendedor'] ?? null;
$id_vendedor = $_SESSION['id_vendedor'] ?? null;

include '../conexao.php';

// --- FILTRO DE TIPO ---
$filtro_tipo = $_GET['tipo'] ?? 'todas';
$where = "WHERE n.idvendedor = :idvendedor";

if ($filtro_tipo === 'compra') {
    $where .= " AND n.tipo = 'compra'";
} elseif ($filtro_tipo === 'avaliacao' || $filtro_tipo === 'avaliação') {
    $where .= " AND n.tipo = 'avaliação'";
}

$stmt = $conn->prepare("
    SELECT n.*, u.nome_completo AS nome_usuario, u.foto_de_perfil AS foto_usuario, a.comentario AS comentario_avaliacao
    FROM notificacoes n
    INNER JOIN usuario u ON u.idusuario = n.idusuario
    LEFT JOIN avaliacoes a
        ON a.idusuario = n.idusuario AND a.idvendedor = n.idvendedor
        AND n.tipo = 'avaliação'
    $where
    ORDER BY n.lida ASC, n.data_envio DESC
");
$stmt->bindValue(':idvendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificações - Painel do Livreiro</title>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --marrom: #5a4224;
        --verde: #5a6b50;
        --background: #F4F1EE;
        --cinza-claro: #e9e9e9;
        --cinza-escuro: #6c757d;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--background);
        padding: 20px;
        color: #333;
    }
    
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 25px;
    }
    
    a.voltar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        text-decoration: none;
        background: var(--marrom);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    a.voltar:hover {
        background: #4a3820;
        color: white;
        transform: translateY(-2px);
    }
    
    h2 {
        color: var(--marrom);
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .filtros {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    
    .filtros a {
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        border: 1px solid var(--cinza-claro);
        color: var(--cinza-escuro);
        background: white;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .filtros a.active {
        background: var(--verde);
        color: white;
        border-color: var(--verde);
    }
    
    .filtros a:hover:not(.active) {
        background: var(--cinza-claro);
        color: var(--marrom);
    }
    
    .titulo-sec {
        margin-top: 30px;
        color: var(--marrom);
        font-weight: 600;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--cinza-claro);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .notificacao {
        background: white;
        border-left: 4px solid var(--verde);
        border-radius: 8px;
        padding: 18px;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        transition: all 0.3s;
    }
    
    .notificacao:hover {
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .notificacao.lida {
        opacity: 0.7;
        border-left-color: var(--cinza-claro);
    }
    
    .notificacao img {
        border-radius: 50%;
        object-fit: cover;
        width: 50px;
        height: 50px;
        margin-right: 15px;
        border: 2px solid var(--cinza-claro);
    }
    
    .conteudo-notificacao {
        flex: 1;
    }
    
    .cabecalho {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        gap: 8px;
    }
    
    .nome-usuario {
        font-weight: 600;
        color: var(--marrom);
    }
    
    .tipo-badge {
        background: var(--verde);
        color: white;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
    }
    
    .mensagem {
        font-size: 15px;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    .data {
        font-size: 13px;
        color: var(--cinza-escuro);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    label.check {
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--verde);
        font-weight: 500;
        white-space: nowrap;
    }
    
    .sem-notificacoes {
        text-align: center;
        padding: 30px;
        color: var(--cinza-escuro);
        font-style: italic;
    }
    
    .link-pedido {
        display: inline-block;
        margin-top: 8px;
        color: var(--verde);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }
    
    .link-pedido:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container">
    <a href="painel_livreiro.php" class="voltar">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
    
    <h2>Notificações do Livreiro</h2>
    
    <div class="filtros">
        <a href="?tipo=todas" class="<?= $filtro_tipo === 'todas' ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Todas
        </a>
        <a href="?tipo=compra" class="<?= $filtro_tipo === 'compra' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i> Compras
        </a>
        <a href="?tipo=avaliacao" class="<?= ($filtro_tipo === 'avaliacao' || $filtro_tipo === 'avaliação') ? 'active' : '' ?>">
            <i class="fas fa-star"></i> Avaliações
        </a>
    </div>

    <?php
    $nao_lidas = array_filter($notificacoes, fn($n) => !$n['lida']);
    $lidas = array_filter($notificacoes, fn($n) => $n['lida']);
    ?>

    <h3 class="titulo-sec">
        <i class="fas fa-bell"></i> Não lidas
    </h3>
    
    <?php if (empty($nao_lidas)): ?>
        <div class="sem-notificacoes">
            <i class="far fa-bell-slash fa-2x"></i>
            <p>Nenhuma notificação nova.</p>
        </div>
    <?php else: ?>
        <?php foreach ($nao_lidas as $n): ?>
            <div class="notificacao" id="notif-<?= $n['idnotificacoes'] ?>">
                <div class="conteudo-notificacao">
                    <div class="cabecalho">
                        <?php if (!empty($n['foto_usuario'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($n['foto_usuario']) ?>" 
                                 alt="<?= htmlspecialchars($n['nome_usuario']) ?>">
                        <?php else: ?>
                            <img src="../img/default-user.png" alt="Usuário sem foto">
                        <?php endif; ?>
                        
                        <div class="nome-usuario"><?= htmlspecialchars($n['nome_usuario']) ?></div>
                        <div class="tipo-badge">
                            <?php if ($n['tipo'] === 'avaliação' || $n['tipo'] === 'avaliacao'): ?>
                                <i class="fas fa-star"></i> Avaliação
                            <?php else: ?>
                                <i class="fas fa-shopping-bag"></i> Compra
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mensagem">
                        <?php if ($n['tipo'] === 'avaliação' || $n['tipo'] === 'avaliacao'): ?>
                            <strong>Avaliação:</strong> <?= htmlspecialchars($n['comentario_avaliacao'] ?? 'Sem comentário') ?>
                        <?php else: ?>
                            <?= htmlspecialchars($n['mensagem']) ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="data">
                        <i class="far fa-clock"></i> <?= htmlspecialchars($n['data_envio']) ?>
                    </div>
                    
                    <?php if ($n['tipo'] === 'compra'): ?>
                        <a href="pedidos.php" class="link-pedido">
                            <i class="fas fa-external-link-alt"></i> Ver pedido relacionado
                        </a>
                    <?php endif; ?>
                </div>
                
                <label class="check">
                    <input type="checkbox" onchange="marcarComoLida(<?= $n['idnotificacoes'] ?>)">
                    <i class="far fa-check-circle"></i> Marcar como lida
                </label>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3 class="titulo-sec">
        <i class="fas fa-envelope-open"></i> Lidas
    </h3>
    
    <?php if (empty($lidas)): ?>
        <div class="sem-notificacoes">
            <i class="far fa-envelope-open fa-2x"></i>
            <p>Nenhuma notificação lida ainda.</p>
        </div>
    <?php else: ?>
        <?php foreach ($lidas as $n): ?>
            <div class="notificacao lida">
                <div class="conteudo-notificacao">
                    <div class="cabecalho">
                        <?php if (!empty($n['foto_usuario'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($n['foto_usuario']) ?>" 
                                 alt="<?= htmlspecialchars($n['nome_usuario'] ?? 'SEM NOME COMPLETO') ?>">
                        <?php else: ?>
                            <img src="../img/default-user.png" alt="Usuário sem foto">
                        <?php endif; ?>
                        
                        <div class="nome-usuario"><?= htmlspecialchars($n['nome_usuario'] ?? '') ?></div>
                        <div class="tipo-badge">
                            <?php if ($n['tipo'] === 'avaliação' || $n['tipo'] === 'avaliacao'): ?>
                                <i class="fas fa-star"></i> Avaliação
                            <?php else: ?>
                                <i class="fas fa-shopping-bag"></i> Compra
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mensagem">
                        <?php if ($n['tipo'] === 'avaliação' || $n['tipo'] === 'avaliacao'): ?>
                            <strong>Avaliação:</strong> <?= htmlspecialchars($n['comentario_avaliacao'] ?? 'Sem comentário') ?>
                        <?php else: ?>
                            <?= htmlspecialchars($n['mensagem']) ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="data">
                        <i class="far fa-clock"></i> <?= htmlspecialchars($n['data_envio']) ?>
                    </div>
                    
                    <?php if ($n['tipo'] === 'compra'): ?>
                        <a href="pedidos.php" class="link-pedido">
                            <i class="fas fa-external-link-alt"></i> Ver pedido relacionado
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// Marca notificação como lida via AJAX
async function marcarComoLida(id) {
    const res = await fetch('marca_lida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    });
    const result = await res.text();
    if (result === 'ok') {
        const el = document.getElementById('notif-' + id);
        if (el) {
            el.classList.add('lida');
            el.querySelector('.check').innerHTML = '<i class="fas fa-check-circle"></i> Lida';
        }
    } else {
        alert('Erro ao marcar como lida: ' + result);
    }
}
</script>

</body>
</html>