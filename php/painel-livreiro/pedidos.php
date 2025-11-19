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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --marrom: #5a4224;
        --verde: #5a6b50;
        --background: #F4F1EE;
        --cinza-claro: #e8e3df;
        --cinza-escuro: #8a7f75;
    }
    
    body { 
        background: var(--background); 
        padding: 20px; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--cinza-claro);
    }
    
    .btn-voltar {
        background: var(--marrom);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-voltar:hover {
        background: #4a3820;
        color: white;
        text-decoration: none;
    }
    
    h2 {
        color: var(--marrom);
        font-weight: 600;
        margin: 0;
    }
    
    .filtros {
        display: flex;
        background: white;
        border-radius: 10px;
        padding: 0;
        margin-bottom: 25px;
        overflow: hidden;
        border: 1px solid var(--cinza-claro);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .filtros a {
        padding: 12px 20px;
        text-decoration: none;
        color: var(--cinza-escuro);
        font-weight: 500;
        border-right: 1px solid var(--cinza-claro);
        transition: all 0.3s;
        flex: 1;
        text-align: center;
    }
    
    .filtros a:last-child {
        border-right: none;
    }
    
    .filtros a.active {
        background: var(--verde);
        color: white;
    }
    
    .filtros a:hover:not(.active) {
        background: var(--cinza-claro);
        color: var(--marrom);
    }
    
    .pedido {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--cinza-claro);
    }
    
    .pedido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--cinza-claro);
    }
    
    .pedido-info h4 {
        margin: 0;
        color: var(--marrom);
        font-weight: 600;
    }
    
    .pedido-info p {
        margin: 5px 0 0;
        color: var(--cinza-escuro);
        font-size: 0.9rem;
    }
    
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .badge-aguardando {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-enviado {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .badge-entregue {
        background: #d4edda;
        color: #155724;
    }
    
    .info-cliente {
        background: var(--background);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .info-cliente h5 {
        margin: 0 0 10px;
        color: var(--verde);
        font-weight: 600;
        font-size: 1rem;
    }
    
    .info-cliente p {
        margin: 5px 0;
        color: var(--cinza-escuro);
    }
    
    .produto-item {
        border-top: 1px solid var(--cinza-claro);
        margin-top: 15px;
        padding-top: 15px;
    }
    
    .produto-header {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .produto-imagem {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--cinza-claro);
    }
    
    .produto-detalhes {
        flex: 1;
    }
    
    .produto-detalhes p {
        margin: 5px 0;
    }
    
    .produto-nome {
        font-weight: 600;
        color: var(--marrom);
        margin-bottom: 5px;
    }
    
    .produto-info {
        color: var(--cinza-escuro);
        font-size: 0.9rem;
    }
    
    .form-envio {
        background: var(--background);
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
    }
    
    label {
        font-weight: 600;
        margin-top: 5px;
        color: var(--marrom);
        font-size: 0.9rem;
    }
    
    .form-select, .form-control {
        border: 1px solid var(--cinza-claro);
        border-radius: 6px;
        padding: 8px 12px;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: var(--verde);
        box-shadow: 0 0 0 0.2rem rgba(90, 107, 80, 0.25);
    }
    
    .btn-atualizar {
        background: var(--verde);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s;
        margin-top: 10px;
    }
    
    .btn-atualizar:hover {
        background: #4a5a42;
        color: white;
    }
    
    .sem-pedidos {
        text-align: center;
        padding: 40px 20px;
        color: var(--cinza-escuro);
    }
    
    .sem-pedidos i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: var(--cinza-claro);
    }
    
    .codigo-rastreio {
        margin-top: 10px;
    }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <a href="painel_livreiro.php" class="btn-voltar">
            <i class="fas fa-arrow-left"></i> Voltar ao Painel
        </a>
        <h2><i class="fas fa-box"></i> Pedidos com seus produtos</h2>
    </div>

    <!-- 🔍 Filtros no estilo das imagens -->
    <div class="filtros">
        <a href="?status_envio=todos" class="<?= $filtro_status === 'todos' ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Todos
        </a>
        <a href="?status_envio=aguardando envio" class="<?= $filtro_status === 'aguardando envio' ? 'active' : '' ?>">
            <i class="fas fa-clock"></i> Aguardando envio
        </a>
        <a href="?status_envio=enviado" class="<?= $filtro_status === 'enviado' ? 'active' : '' ?>">
            <i class="fas fa-shipping-fast"></i> Enviados
        </a>
        <a href="?status_envio=entregue" class="<?= $filtro_status === 'entregue' ? 'active' : '' ?>">
            <i class="fas fa-check-circle"></i> Entregues
        </a>
    </div>

    <?php if ($pedidos): ?>
        <?php foreach ($pedidos as $p): ?>
            <div class="pedido">
                <div class="pedido-header">
                    <div class="pedido-info">
                        <h4>Pedido #<?= htmlspecialchars($p['idpedido']) ?></h4>
                        <p>Cliente: <?= htmlspecialchars($p['nome_usuario']) ?> • Data: <?= htmlspecialchars($p['data_pedido']) ?></p>
                    </div>
                    <div>
                        <?php
                        // Determinar o status geral do pedido (baseado no primeiro item)
                        $itensStmt = $conn->prepare("
                            SELECT i.status_envio
                            FROM item_pedido i
                            JOIN produto pr ON i.idproduto = pr.idproduto
                            WHERE pr.idvendedor = :id_vendedor AND i.idpedido = :idpedido
                            LIMIT 1
                        ");
                        $itensStmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
                        $itensStmt->bindValue(':idpedido', $p['idpedido'], PDO::PARAM_INT);
                        $itensStmt->execute();
                        $statusGeral = $itensStmt->fetchColumn();
                        
                        $badgeClass = '';
                        if ($statusGeral === 'aguardando envio') {
                            $badgeClass = 'badge-aguardando';
                        } elseif ($statusGeral === 'enviado') {
                            $badgeClass = 'badge-enviado';
                        } elseif ($statusGeral === 'entregue') {
                            $badgeClass = 'badge-entregue';
                        }
                        ?>
                        <span class="badge-status <?= $badgeClass ?>">
                            <?= htmlspecialchars(ucfirst($statusGeral)) ?>
                        </span>
                    </div>
                </div>

                <div class="info-cliente">
                    <h5><i class="fas fa-map-marker-alt"></i> Endereço de entrega</h5>
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
                            <div class="produto-detalhes">
                                <p class="produto-nome"><?= htmlspecialchars($item['nome']) ?></p>
                                <p class="produto-info">Quantidade: <?= htmlspecialchars($item['quantidade']) ?></p>
                                <p class="produto-info">Frete: R$ <?= number_format($item['frete_item'], 2, ',', '.') ?> — <?= htmlspecialchars($item['servico_frete_item']) ?></p>
                                <p class="produto-info">Prazo: <?= htmlspecialchars($item['prazo_item']) ?> dias úteis</p>
                            </div>
                        </div>

                        <form action="atualizar_envio.php" method="post" class="form-envio">
                            <input type="hidden" name="iditem_pedido" value="<?= $item['iditem_pedido'] ?>">

                            <div class="mb-3">
                                <label>Status de envio:</label>
                                <select name="status_envio" class="form-select" onchange="mostrarCampoRastreio(this)">
                                    <option value="aguardando envio" <?= $item['status_envio'] === 'aguardando envio' ? 'selected' : '' ?>>Aguardando envio</option>
                                    <option value="enviado" <?= $item['status_envio'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option value="entregue" <?= $item['status_envio'] === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                </select>
                            </div>

                            <div class="codigo-rastreio" style="display: <?= $item['status_envio'] === 'enviado' ? 'block' : 'none' ?>;">
                                <label for="codigo_rastreio_item">Código de rastreio:</label>
                                <input type="text" name="codigo_rastreio_item" class="form-control" placeholder="Ex: LX123456789BR" value="<?= htmlspecialchars($item['codigo_rastreio_item'] ?? '') ?>">
                            </div>

                            <button type="submit" class="btn-atualizar">
                                <i class="fas fa-save"></i> Atualizar
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="sem-pedidos">
            <i class="fas fa-box-open"></i>
            <h4>Nenhum pedido encontrado para esse filtro.</h4>
            <p>Quando houver pedidos, eles aparecerão aqui.</p>
        </div>
    <?php endif; ?>
</div>

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