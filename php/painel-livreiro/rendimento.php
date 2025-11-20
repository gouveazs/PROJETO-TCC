<?php
session_start();
$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;
$foto_de_perfil = $_SESSION['foto_de_perfil_vendedor'] ?? null;
$id_vendedor = $_SESSION['id_vendedor'] ?? null;

if (!$nome_vendedor) {
    header('Location: ../login/loginVendedor.php');
    exit;
}

include '../conexao.php';

/* 1. Faturamento e vendas detalhadas do mês, subtraindo valor do frete */
$stmt = $conn->prepare("
    SELECT 
        i.idpedido,
        p.data_pedido,
        pr.nome AS produto,
        i.quantidade,
        pr.preco AS preco_unitario,
        (i.quantidade * pr.preco) AS total_bruto,
        i.frete_item,
        ((i.quantidade * pr.preco) - IFNULL(i.frete_item,0)) AS total_liquido
    FROM item_pedido i
    JOIN pedido p ON p.idpedido = i.idpedido
    JOIN produto pr ON pr.idproduto = i.idproduto
    WHERE pr.idvendedor = :id_vendedor
      AND i.status_envio = 'entregue'
      AND MONTH(p.data_pedido) = MONTH(CURDATE())
      AND YEAR(p.data_pedido) = YEAR(CURDATE())
    ORDER BY p.data_pedido DESC, i.idpedido DESC
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$vendas_detalhadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Soma total líquido do mês (subtraindo frete) */
$total_liquido = 0;
$total_pedidos = [];
foreach ($vendas_detalhadas as $linha) {
    $total_liquido += $linha['total_liquido'];
    $total_pedidos[$linha['idpedido']] = true;
}

/* Avaliação média do vendedor */
$stmt = $conn->prepare("
    SELECT AVG(nota) AS media, COUNT(*) AS total_aval
    FROM avaliacoes
    WHERE idvendedor = :id_vendedor
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$dados_av = $stmt->fetch(PDO::FETCH_ASSOC);
$media_avaliacao = $dados_av && $dados_av['media'] ? number_format($dados_av['media'], 2, ',', '.') : '—';
$total_avaliacoes = $dados_av['total_aval'] ?? 0;

/* Produtos do vendedor */
$stmt = $conn->prepare("
    SELECT nome, quantidade, status
    FROM produto WHERE idvendedor = :id_vendedor
    ORDER BY idproduto DESC
    LIMIT 10
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Últimas 5 avaliações recebidas */
$stmt = $conn->prepare("
    SELECT a.nota, a.comentario, a.data_avaliacao, u.nome_completo 
    FROM avaliacoes a
    JOIN usuario u ON u.idusuario = a.idusuario
    WHERE a.idvendedor = :id_vendedor
    ORDER BY a.data_avaliacao DESC
    LIMIT 5
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$ultimas_avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Rendimento do Vendedor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    :root { --marrom:#5a4224; --verde:#5a6b50; --background:#F4F1EE; --card-bg:#fff; --card-border:#ddd; --text-dark:#333; --text-muted:#666; --dinheiro:#21724e;}
    body {background: var(--background);min-height:100vh;display:flex;flex-direction:column;padding-left:250px;}
    * { box-sizing: border-box; font-family: 'Playfair Display', serif;}
    .sidebar {position:fixed;top:0;left:0;width:250px;height:100vh;background:var(--verde);color:#fff;display:flex;flex-direction:column;align-items:flex-start;padding-top:20px;overflow-y:auto;}
    .sidebar .logo {display:flex;align-items:center;padding:0 20px 20px 20px;}
    .sidebar .logo img {width:60px;height:60px;border-radius:50%;object-fit:cover;margin-right:15px;}
    .sidebar .user-info {display:flex;flex-direction:column;line-height:1.2;}
    .sidebar .user-info .nome-usuario {font-weight:bold;font-size:0.95rem;color:#fff;}
    .sidebar nav {width:100%;padding:0 20px;}
    .sidebar nav h3 {margin-top:20px;margin-bottom:10px;font-size:1rem;color:#ddd;}
    .sidebar nav ul {list-style:none;padding:0;margin:0 0 10px 0;width:100%;}
    .sidebar nav ul li {width:100%;margin-bottom:10px;}
    .sidebar nav ul li a {color:#fff;text-decoration:none;display:flex;align-items:center;padding:10px;border-radius:8px;transition: background 0.3s;}
    .sidebar nav ul li a img {margin-right:10px;}
    .sidebar nav ul li a:hover {background-color:#6f8562;}
    .topbar {position:fixed;top:0;left:250px;right:0;height:70px;background-color:var(--marrom);color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 30px;z-index:1001;}
    .topbar h1 {font-size:1.5rem;}
    main {padding:30px 30px 0 30px;}
    .header {display:flex;align-items:center;gap:15px;margin-bottom:30px;}
    .header img {width:110px;height:110px;border-radius:50%;object-fit:cover;background:#f8f8f8;border:1px solid #ccc;}
    .header-text {display:flex;flex-direction:column;}
    .header h1 {font-size:26px;color:var(--text-dark);margin:5px 0;}
    .header p {color:var(--text-muted);font-size:15px;margin:0;}
    .cards {display:flex;flex-wrap:wrap;gap:25px;margin-bottom:28px;}
    .dinheiro-card,.dados-card {background:var(--card-bg);border:1px solid var(--card-border);border-radius:10px;padding:24px 18px 18px 18px;box-shadow:0 2px 6px rgba(0,0,0,0.06);}
    .dinheiro-card {max-width:420px;}
    .dados-card {min-width:260px;}
    .grana {color:var(--dinheiro);font-size:2.2rem;letter-spacing:1px;font-weight:700;margin-bottom:10px;}
    .table {width:100%;border-collapse:collapse;margin-bottom:15px;font-size:14px;margin-top:18px;background:#fff;}
    .table th, .table td {border:1px solid var(--card-border);padding:10px 7px;text-align:left;}
    .table th {background-color:#f3ede7;color:var(--text-dark);}
    .table td {color:var(--text-muted);}
    @media (max-width:900px) {body{padding-left:0;}.sidebar{width:100vw;}main{padding:18px;}.cards{flex-direction:column;}}
    @media (max-width:768px) {.sidebar{width:200px;}.topbar, main{margin-left:200px;}}
    @media (max-width:576px) {.sidebar{display:none;}.topbar, main{margin-left:0;}}
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>">
      <?php else: ?>
        <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
      <?php endif; ?>
      <div class="user-info">
        <p class="nome-usuario"><?= htmlspecialchars($nome_vendedor) ?></p>
      </div>
    </div>
    <nav>
      <ul class="menu">
        <li><a href="painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px;"> Início</a></li>
        <li><a href="anuncios.php"><img src="../../imgs/anuncio.png" alt="Vendas" style="width:20px;"> Seus Anúncios</a></li>
        <li><a href="pedidos.php"><img src="../../imgs/file.png" alt="Vendas" style="width:20px;"> Seus Pedidos</a></li>
        <li><a href="rendimento.php"><img src="../../imgs/rendimento.png" alt="Rendimento" style="width:20px;"> Rendimento</a></li>
        <li><a href="lista-chats.php"><img src="../../imgs/chaat.png" alt="Chats" style="width:20px;"> Chats</a></li>
        <li><a href="notificacao.php"><img src="../../imgs/notificacao.png" alt="Notificações" style="width:20px;"> Notificações</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/anuncialivro.png" alt="Cadastro" style="width:20px;"> Anunciar livro</a></li>
      </ul>
      <h3>Conta</h3>
      <ul class="account">
        <li><a href="minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px;"> Editar informações</a></li>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px;"> Sair</a></li>
      </ul>
    </nav>
  </div>
  <div class="topbar">
    <h1>Rendimento do Livreiro</h1>
  </div>
  <main>
    <div class="header">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>">
      <?php else: ?>
        <img src="../../imgs/usuario.jpg" alt="Perfil">
      <?php endif; ?>
      <div class="header-text">
        <h1><?= htmlspecialchars($nome_vendedor) ?></h1>
        <p>Veja suas vendas detalhadas, saldo do mês e avaliações recebidas</p>
      </div>
    </div>
    <div class="cards">
      <div class="dinheiro-card">
        <h2>Total Líquido do mês<br><small style="font-weight:normal;font-size:.98em;">(valor entregue - fretes)</small></h2>
        <div class="grana">R$ <?= number_format($total_liquido,2,',','.') ?></div>
      </div>
      <div class="dados-card">
        <h2>Resumo do mês</h2>
        <b>Pedidos entregues:</b> <?= count($total_pedidos) ?><br>
        <b>Avaliação média:</b>
          <?php if ($total_avaliacoes): ?>
            <span title="<?= $total_avaliacoes ?> avaliações">⭐ <?= $media_avaliacao ?></span>
          <?php else: ?>
            —
          <?php endif; ?>
      </div>
    </div>

    <h2>Vendas Detalhadas do Mês (entregues)</h2>
    <div style="overflow-x:auto;">
      <table class="table">
        <tr>
            <th>Pedido</th>
            <th>Data</th>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Valor Bruto</th>
            <th>Frete</th>
            <th>Valor Líquido</th>
        </tr>
        <?php if ($vendas_detalhadas): ?>
          <?php foreach ($vendas_detalhadas as $v): ?>
            <tr>
                <td><?= (int)$v['idpedido'] ?></td>
                <td><?= date('d/m/Y', strtotime($v['data_pedido'])) ?></td>
                <td><?= htmlspecialchars($v['produto']) ?></td>
                <td><?= (int)$v['quantidade'] ?></td>
                <td>R$ <?= number_format($v['total_bruto'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format($v['frete_item'], 2, ',', '.') ?></td>
                <td><b>R$ <?= number_format($v['total_liquido'], 2, ',', '.') ?></b></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align:center;">Nenhuma venda entregue este mês</td></tr>
        <?php endif; ?>
      </table>
    </div>
    <br>
    <h2>Seus Produtos</h2>
    <div style="overflow-x:auto;">
      <table class="table">
        <tr>
          <th>Produto</th><th>Qtd em Estoque</th><th>Status</th>
        </tr>
        <?php if ($produtos): ?>
          <?php foreach ($produtos as $pr): ?>
            <tr>
                <td><?= htmlspecialchars($pr['nome']) ?></td>
                <td><?= (int)($pr['quantidade'] ?? 0) ?></td>
                <td><?= htmlspecialchars($pr['status'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;">Nenhum produto encontrado.</td></tr>
        <?php endif; ?>
      </table>
    </div>
    <br>
    <h2>Últimas Avaliações Recebidas</h2>
    <div class="avaliacoes-list">
      <?php if ($ultimas_avaliacoes): ?>
        <?php foreach ($ultimas_avaliacoes as $av): ?>
          <div style="margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid #eee;">
            <b><?= htmlspecialchars($av['nome_completo']) ?></b> —
            <span style="color:#feb700;">
              <?php for($i=1;$i<=5;$i++): ?>
                <?= $i <= $av['nota'] ? '★' : '☆' ?>
              <?php endfor; ?>
            </span>
            <br>
            <span><?= htmlspecialchars($av['comentario']) ?></span><br>
            <small style="color:#888;"><?= date('d/m/Y H:i', strtotime($av['data_avaliacao'])) ?></small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Você ainda não recebeu avaliações de clientes.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>