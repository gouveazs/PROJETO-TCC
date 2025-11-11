<?php
session_start();
include '../conexao.php';

$id_vendedor = $_SESSION['id_vendedor'] ?? null;

if (!$id_vendedor) {
    die("Acesso negado. Faça login como vendedor.");
}

// Captura o filtro selecionado
$filtro_status = $_GET['status_envio'] ?? 'todos';

// Buscar pedidos com base no filtro
$sql = "
    SELECT DISTINCT 
        p.idpedido,
        p.data_pedido,
        p.valor_total,
        u.nome_completo AS nome_usuario,
        u.cidade, u.estado, u.cep, u.rua, u.numero, u.bairro
    FROM pedido p
    JOIN item_pedido i ON p.idpedido = i.idpedido
    JOIN produto pr ON i.idproduto = pr.idproduto
    JOIN usuario u ON u.idusuario = p.idusuario
    WHERE pr.idvendedor = :id_vendedor
";

if ($filtro_status !== 'todos') {
    $sql .= " AND i.status_envio = :status_envio";
}

$sql .= " ORDER BY p.data_pedido DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
if ($filtro_status !== 'todos') {
    $stmt->bindValue(':status_envio', $filtro_status);
}
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar Pedidos - Vendedor</title>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<style>
    body { background: #f7f7f7; padding: 20px; font-family: Arial, sans-serif; }
    .pedido { background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px; }
    .produto-item { border-top: 1px solid #eee; margin-top: 15px; padding-top: 10px; }
    .produto-header { display: flex; align-items: center; gap: 15px; }
    .produto-imagem { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
    label { font-weight: 600; margin-top: 5px; }
    .btn { margin-top: 10px; }
    h4 { margin-bottom: 10px; }
    .info-cliente { background: #fafafa; padding: 10px; border-radius: 8px; margin-top: 10px; }

    /* ===== ESTILO DOS FILTROS (igual às notificações) ===== */
    .filtros {
        margin-bottom: 20px;
        background: white;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }
    .filtros a {
        margin-right: 10px;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid #ccc;
        color: black;
        background: white;
    }
    .filtros a.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .filtros a:hover {
        background: #0056b3;
        color: white;
    }
</style>
</head>
<body>

<a href="painel_livreiro.php" class="btn btn-secondary mb-3">Voltar</a>
<h2>📦 Pedidos com seus produtos</h2>

<!-- 🔍 Filtros no estilo das notificações -->
<div class="filtros">
    <a href="?status_envio=todos" class="<?= $filtro_status === 'todos' ? 'active' : '' ?>">Todos</a>
    <a href="?status_envio=aguardando envio" class="<?= $filtro_status === 'aguardando envio' ? 'active' : '' ?>">Aguardando envio</a>
    <a href="?status_envio=enviado" class="<?= $filtro_status === 'enviado' ? 'active' : '' ?>">Enviados</a>
    <a href="?status_envio=entregue" class="<?= $filtro_status === 'entregue' ? 'active' : '' ?>">Entregues</a>
</div>

<?php if ($pedidos): ?>
    <?php foreach ($pedidos as $p): ?>
        <div class="pedido">
            <h4>Pedido #<?= htmlspecialchars($p['idpedido']) ?> — Cliente: <?= htmlspecialchars($p['nome_usuario']) ?></h4>
            <p><strong>Data:</strong> <?= htmlspecialchars($p['data_pedido']) ?></p>

            <div class="info-cliente">
                <h5>📍 Endereço de entrega</h5>
                <p><?= htmlspecialchars($p['rua'] ?? '') ?>, <?= htmlspecialchars($p['numero'] ?? '') ?> — <?= htmlspecialchars($p['bairro'] ?? '') ?></p>
                <p><?= htmlspecialchars($p['cidade'] ?? '') ?> - <?= htmlspecialchars($p['estado'] ?? '') ?> | CEP <?= htmlspecialchars($p['cep'] ?? '') ?></p>
            </div>

            <?php
            $itensStmt = $conn->prepare("
                SELECT 
                    i.iditem_pedido,
                    i.quantidade,
                    i.status_envio,
                    i.codigo_rastreio_item,
                    i.frete_item,
                    i.servico_frete_item,
                    i.prazo_item,
                    pr.nome,
                    (SELECT img.imagem FROM imagens img WHERE img.idproduto = pr.idproduto LIMIT 1) AS imagem
                FROM item_pedido i
                JOIN produto pr ON i.idproduto = pr.idproduto
                WHERE pr.idvendedor = :id_vendedor AND i.idpedido = :idpedido
            ");
            $itensStmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
            $itensStmt->bindValue(':idpedido', $p['idpedido'], PDO::PARAM_INT);
            $itensStmt->execute();
            $itens = $itensStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach ($itens as $item): ?>
                <div class="produto-item">
                    <div class="produto-header">
                        <?php
                        $img = $item['imagem'] 
                            ? 'data:image/jpeg;base64,' . base64_encode($item['imagem'])
                            : '../../imgs/capa.jpg';
                        ?>
                        <img src="<?= $img ?>" alt="Produto" class="produto-imagem">
                        <div>
                            <p><strong><?= htmlspecialchars($item['nome']) ?></strong></p>
                            <p>Quantidade: <?= htmlspecialchars($item['quantidade']) ?></p>
                            <p>Frete: R$ <?= number_format($item['frete_item'], 2, ',', '.') ?> — <?= htmlspecialchars($item['servico_frete_item']) ?></p>
                            <p>Prazo: <?= htmlspecialchars($item['prazo_item']) ?> dias úteis</p>
                        </div>
                    </div>

                    <form action="atualizar_envio.php" method="post">
                        <input type="hidden" name="iditem_pedido" value="<?= $item['iditem_pedido'] ?>">

                        <label>Status de envio:</label>
                        <select name="status_envio" class="form-select" onchange="mostrarCampoRastreio(this)">
                            <option value="aguardando envio" <?= $item['status_envio'] === 'aguardando envio' ? 'selected' : '' ?>>Aguardando envio</option>
                            <option value="enviado" <?= $item['status_envio'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="entregue" <?= $item['status_envio'] === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                        </select>

                        <div class="input-group codigo-rastreio" style="display: <?= $item['status_envio'] === 'enviado' ? 'block' : 'none' ?>;">
                            <label for="codigo_rastreio_item">Código de rastreio:</label>
                            <input type="text" name="codigo_rastreio_item" class="form-control" placeholder="Ex: LX123456789BR" value="<?= htmlspecialchars($item['codigo_rastreio_item'] ?? '') ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">💾 Atualizar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nenhum pedido encontrado para esse filtro.</p>
<?php endif; ?>

<script>
function mostrarCampoRastreio(select) {
    const form = select.closest('form');
    const campo = form.querySelector('.codigo-rastreio');
    if (select.value === 'enviado') {
        campo.style.display = 'block';
    } else {
        campo.style.display = 'none';
        form.querySelector('input[name="codigo_rastreio_item"]').value = '';
    }
}
</script>

</body>
</html>
