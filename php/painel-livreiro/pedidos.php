<?php
session_start();
include '../conexao.php';

$id_vendedor = $_SESSION['id_vendedor'] ?? null;

if (!$id_vendedor) {
    die("Acesso negado. Faça login como vendedor.");
}

// Busca os pedidos relacionados aos produtos do vendedor
$stmt = $conn->prepare("
    SELECT 
        p.*, 
        u.nome AS nome_usuario, 
        u.cidade, u.estado, u.cep, u.rua, u.numero, u.bairro
    FROM pedido p
    JOIN item_pedido i ON p.idpedido = i.idpedido
    JOIN produto pr ON i.idproduto = pr.idproduto
    JOIN usuario u ON u.idusuario = p.idusuario
    WHERE pr.idvendedor = :id_vendedor
    GROUP BY p.idpedido
    ORDER BY p.data_pedido DESC
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar Pedidos</title>
<link rel="stylesheet" href="../../css/bootstrap.min.css">
<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    .pedido {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .pedido h4 { margin-bottom: 10px; }
    .info-cliente, .info-frete { margin-top: 10px; background: #fafafa; padding: 10px; border-radius: 8px; }
    .input-group { margin-top: 10px; }
    label { font-weight: 600; margin-top: 5px; }
</style>
</head>
<body>

    <a href="painel_livreiro.php" class="btn btn-secondary mb-3">Voltar</a>
    <h2>📦 Pedidos dos seus produtos</h2>

    <?php foreach ($pedidos as $p): ?>
        <div class="pedido">
            <h4>Pedido #<?= htmlspecialchars($p['idpedido']) ?> — Cliente: <?= htmlspecialchars($p['nome_usuario']) ?></h4>
            <p><strong>Data do pedido:</strong> <?= htmlspecialchars($p['data_pedido']) ?></p>
            <p><strong>Status do pedido:</strong> <?= htmlspecialchars($p['status']) ?></p>

            <div class="info-cliente">
                <h5>📍 Endereço de entrega</h5>
                <p><?= htmlspecialchars($p['rua'] ?? '') ?>, <?= htmlspecialchars($p['numero'] ?? '') ?> - <?= htmlspecialchars($p['bairro'] ?? '') ?></p>
                <p><?= htmlspecialchars($p['cidade'] ?? '') ?> - <?= htmlspecialchars($p['estado'] ?? '') ?> | CEP: <?= htmlspecialchars($p['cep'] ?? '') ?></p>
            </div>

            <div class="info-frete">
                <h5>🚚 Informações de frete</h5>
                <p><strong>Serviço:</strong> <?= htmlspecialchars($p['servico_frete']) ?: 'Não especificado' ?></p>
                <p><strong>Valor:</strong> R$ <?= number_format($p['frete'], 2, ',', '.') ?></p>
                <p><strong>Prazo estimado:</strong> <?= htmlspecialchars($p['prazo_entrega']) ?> dias</p>
                <p><strong>Status de envio:</strong> <?= htmlspecialchars($p['status_envio']) ?></p>
                <?php if (!empty($p['codigo_rastreio'])): ?>
                    <p>📦 <strong>Código de rastreio:</strong> <?= htmlspecialchars($p['codigo_rastreio']) ?></p>
                <?php endif; ?>
            </div>

            <form action="atualizar_envio.php" method="post" class="form-envio">
                <input type="hidden" name="idpedido" value="<?= $p['idpedido'] ?>">

                <div class="input-group">
                    <label>Status de envio:</label>
                    <select name="status_envio" class="form-select" onchange="mostrarCampoRastreio(this)">
                        <option value="aguardando envio" <?= $p['status_envio'] === 'aguardando envio' ? 'selected' : '' ?>>Aguardando envio</option>
                        <option value="enviado" <?= $p['status_envio'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                        <option value="entregue" <?= $p['status_envio'] === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                    </select>
                </div>

                <div class="input-group codigo-rastreio" style="display: <?= $p['status_envio'] === 'enviado' ? 'block' : 'none' ?>;">
                    <label for="codigo_rastreio">Código de rastreio:</label>
                    <input type="text" name="codigo_rastreio" class="form-control" placeholder="Ex: LX123456789BR" value="<?= htmlspecialchars($p['codigo_rastreio'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-primary mt-3">💾 Salvar alterações</button>
            </form>
        </div>
    <?php endforeach; ?>

<script>
// Mostra o campo de rastreio quando o status é "enviado"
function mostrarCampoRastreio(select) {
    const form = select.closest('.form-envio');
    const campo = form.querySelector('.codigo-rastreio');
    if (select.value === 'enviado') {
        campo.style.display = 'block';
    } else {
        campo.style.display = 'none';
        form.querySelector('input[name="codigo_rastreio"]').value = '';
    }
}
</script>

</body>
</html>
