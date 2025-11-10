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
    SELECT n.*, u.nome_completo AS nome_usuario, u.foto_de_perfil AS foto_usuario
    FROM notificacoes n
    INNER JOIN usuario u ON u.idusuario = n.idusuario
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
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    a.voltar {
        display: inline-block; margin-bottom: 20px; text-decoration: none;
        background: #007bff; color: white; padding: 8px 16px; border-radius: 6px;
    }
    a.voltar:hover { background: #0056b3; }
    .filtros a {
        margin-right: 10px; padding: 6px 12px; border-radius: 6px; text-decoration: none;
        border: 1px solid #ccc; color: black; background: white;
    }
    .filtros a.active { background: #007bff; color: white; border-color: #007bff; }
    .filtros a:hover { background: #0056b3; color: white; }

    h3.titulo-sec { margin-top: 30px; color: #333; }

    .notificacao {
        background: white; border: 1px solid #ddd; border-radius: 10px;
        padding: 15px; margin-bottom: 15px; box-shadow: 0 0 8px rgba(0,0,0,0.1);
        display: flex; align-items: flex-start; justify-content: space-between;
    }
    .notificacao.lida { opacity: 0.6; }
    .notificacao img {
        border-radius: 50%; object-fit: cover; width: 50px; height: 50px;
        margin-right: 10px;
    }
    .cabecalho { display: flex; align-items: center; margin-bottom: 10px; }
    .mensagem { font-size: 15px; margin-bottom: 8px; }
    .data { font-size: 13px; color: gray; }
    label.check {
        font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px;
    }
</style>
</head>
<body>

<a href="painel_livreiro.php" class="voltar">Voltar</a>
<h2>Notificações do Livreiro</h2>
<hr>

<div class="filtros">
    <a href="?tipo=todas" class="<?= $filtro_tipo === 'todas' ? 'active' : '' ?>">Todas</a>
    <a href="?tipo=compra" class="<?= $filtro_tipo === 'compra' ? 'active' : '' ?>">Compras</a>
    <a href="?tipo=avaliacao" class="<?= ($filtro_tipo === 'avaliacao' || $filtro_tipo === 'avaliação') ? 'active' : '' ?>">Avaliações</a>
</div>

<?php
$nao_lidas = array_filter($notificacoes, fn($n) => !$n['lida']);
$lidas = array_filter($notificacoes, fn($n) => $n['lida']);
?>

<h3 class="titulo-sec">🔔 Não lidas</h3>
<?php if (empty($nao_lidas)): ?>
    <p>Nenhuma notificação nova.</p>
<?php else: ?>
    <?php foreach ($nao_lidas as $n): ?>
        <div class="notificacao" id="notif-<?= $n['idnotificacoes'] ?>">
            <div style="display:flex;">
                <?php if (!empty($n['foto_usuario'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($n['foto_usuario']) ?>" 
                         alt="<?= htmlspecialchars($n['nome_usuario']) ?>">
                <?php else: ?>
                    <img src="../img/default-user.png" alt="Usuário sem foto">
                <?php endif; ?>

                <div>
                    <div class="cabecalho">
                        <strong><?= htmlspecialchars($n['nome_usuario']) ?></strong>
                    </div>
                    <div class="mensagem"><?= htmlspecialchars($n['mensagem']) ?></div>
                    <div class="data"><?= htmlspecialchars($n['data_envio']) ?></div>
                    <small>Tipo: <?= htmlspecialchars($n['tipo']) ?></small>
                </div>
            </div>
            <label class="check">
                <input type="checkbox" onchange="marcarComoLida(<?= $n['idnotificacoes'] ?>)"> Marcar como lida
            </label>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h3 class="titulo-sec">📨 Lidas</h3>
<?php if (empty($lidas)): ?>
    <p>Nenhuma notificação lida ainda.</p>
<?php else: ?>
    <?php foreach ($lidas as $n): ?>
        <div class="notificacao lida">
            <div style="display:flex;">
                <?php if (!empty($n['foto_usuario'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($n['foto_usuario']) ?>" 
                         alt="<?= htmlspecialchars($n['nome_usuario'] ?? 'SEM NOME COMPLETO') ?>">
                <?php else: ?>
                    <img src="../img/default-user.png" alt="Usuário sem foto">
                <?php endif; ?>

                <div>
                    <div class="cabecalho">
                        <strong><?= htmlspecialchars($n['nome_usuario'] ?? '') ?></strong>
                    </div>
                    <div class="mensagem"><?= htmlspecialchars($n['mensagem']) ?></div>
                    <div class="data"><?= htmlspecialchars($n['data_envio']) ?></div>
                    <small>Tipo: <?= htmlspecialchars($n['tipo']) ?></small><br>
                    <a href="pedidos.php">Ver pedido relacionado</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

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
        if (el) el.remove();
    } else {
        alert('Erro ao marcar como lida: ' + result);
    }
}
</script>

</body>
</html>
