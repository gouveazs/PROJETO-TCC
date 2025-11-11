<?php
session_start();
$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil_vendedor']) ? $_SESSION['foto_de_perfil_vendedor'] : null;
$id_vendedor = isset($_SESSION['id_vendedor']) ? $_SESSION['id_vendedor'] : null; 

if (!isset($_SESSION['nome_vendedor'])) {
  header('Location: ../login/loginVendedor.php');
  exit;
}

include '../conexao.php';

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM produto WHERE idvendedor = ?");
$stmt->execute([$id_vendedor]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$anuncios_publicadas = $result['total'];

$stmt = $conn->prepare("
    SELECT COUNT(i.iditem_pedido) AS total
    FROM item_pedido i
    INNER JOIN pedido p ON p.idpedido = i.idpedido
    INNER JOIN produto pr ON pr.idproduto = i.idproduto
    WHERE pr.idvendedor = :id_vendedor
      AND p.status_envio = 'entregue'
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$vendas_concluidas = $result['total'];

$stmt = $conn->prepare("SELECT reputacao FROM vendedor WHERE idvendedor = ?");
$stmt->execute([$id_vendedor]);
$dados_vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
$reputacao = $dados_vendedor ? $dados_vendedor['reputacao'] : 0;

$stmt = $conn->prepare("
    SELECT 
        i.iditem_pedido,
        i.idpedido,
        p.data_pedido,
        i.status_envio,
        i.idproduto,
        pr.nome AS produto_nome,
        u.nome_completo AS comprador_nome,
        i.codigo_rastreio_item,
        i.servico_frete_item,
        i.prazo_item
    FROM item_pedido i
    INNER JOIN pedido p ON p.idpedido = i.idpedido
    INNER JOIN produto pr ON pr.idproduto = i.idproduto
    INNER JOIN usuario u ON u.idusuario = p.idusuario
    WHERE pr.idvendedor = :id_vendedor
    ORDER BY p.data_pedido DESC, i.iditem_pedido DESC
    LIMIT 5
");
$stmt->bindValue(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notificacoes = [];
$sql = "
    SELECT 
        n.idnotificacoes,
        n.mensagem,
        n.lida,
        n.data_envio,
        u.nome_completo AS usuario_nome
    FROM notificacoes n
    JOIN usuario u ON n.idusuario = u.idusuario
    WHERE n.idvendedor = :id
    ORDER BY n.data_envio DESC
    LIMIT 5
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id_vendedor]);
$notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Início - Painel do Livreiro</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
      --card-bg: #fff;
      --card-border: #ddd;
      --text-dark: #333;
      --text-muted: #666;
    }

    * {
      margin: 0; 
      padding: 0; 
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: var(--background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding-left: 250px; /* espaço pro conteúdo não invadir a sidebar */
    }

    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 20px;
      overflow-y: auto; /* SCROLL HABILITADO */
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
    } 

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background-color: #ccc;
      border-radius: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar .logo {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      width: 100%;
      padding: 0 20px;
      margin-bottom: 20px;
    }

    .sidebar .logo img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .sidebar .user-info {
      display: flex;
      flex-direction: column;
      line-height: 1.2;
    }

    .sidebar .user-info .nome-usuario {
      font-weight: bold;
      font-size: 0.95rem; 
      color: #fff;
    }

    .sidebar .user-info .tipo-usuario {
      font-size: 0.8rem;
      color: #ddd;
    }

    .sidebar .logo p {
      font-weight: bold;
    }

    .sidebar nav {
      width: 100%;
      padding: 0 20px;
    }

    .sidebar nav h3 {
      margin-top: 20px;
      margin-bottom: 10px;
      font-size: 1rem;
      color: #ddd;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0;
      margin: 0 0 10px 0;
      width: 100%;
    }

    .sidebar nav ul li {
      width: 100%;
      margin-bottom: 10px;
    }

    .sidebar nav ul li a {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .sidebar nav ul li a i {
      margin-right: 10px;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    .topbar {
      position: fixed;
      top: 0; left: 250px; right: 0;
      height: 70px;
      background-color: var(--marrom);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      z-index: 1001;
    }

    .topbar h1 {
      font-size: 1.5rem;
    }

    .topbar input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 20px;
      width: 250px;
    }

    main {
      padding: 30px;
      flex: 1;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 30px;
      text-align: left; /* garante que texto interno também fique à esquerda */
    }

    .header img {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
    }

    .header-text {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .header h1 {
        font-size: 26px;
        color: var(--text-dark);
        margin: 5px 0;
    }

    .header p {
        color: var(--text-muted);
        font-size: 15px;
        margin: 0;
    }

    .cards {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 25px;
    }

    .card {
      background-color: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .card h2 {
      margin-top: 0;
      font-size: 20px;
      margin-bottom: 15px;
      color: var(--text-dark);
    }

    /* Barra de progresso */
    .progress-bar {
      background-color: #eee;
      border-radius: 5px;
      overflow: hidden;
      height: 20px;
      margin-bottom: 10px;
    }

    .progress {
      background-color: var(--verde);
      width: 35%;
      height: 100%;
      text-align: center;
      font-size: 12px;
      color: #fff;
      line-height: 20px;
      font-weight: bold;
    }

    .info {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* Tabela */
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .table th, .table td {
      border: 1px solid var(--card-border);
      padding: 10px;
      text-align: left;
    }

    .table th {
      background-color: #f5f5f5;
      color: var(--text-dark);
    }

    .table td {
      color: var(--text-muted);
    }

    /* Grid inferior */
    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    @media (max-width: 900px) {
      body {
        padding-left: 0; /* sidebar vira topo em telas pequenas */
      }
      .cards, .grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .topbar, .banner, .main, .footer {
        margin-left: 200px;
      }
      .cards-novidades, .cards-recomendacoes {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .topbar, .banner, .main, .footer {
        margin-left: 0;
      }
    }
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
          <p class="nome-usuario"><?= $nome_vendedor ? htmlspecialchars($nome_vendedor) : 'Entre ou crie sua conta'; ?></p>
        </div>
    </div>

    <nav>
      <ul class="menu">
        <li><a href="painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="anuncios.php"><img src="../../imgs/anuncio.png" alt="Vendas" style="width:20px; margin-right:10px;"> Seus Anúncios</a></li>
        <li><a href="pedidos.php"><img src="../../imgs/file.png" alt="Vendas" style="width:20px; margin-right:10px;"> Seus Pedidos</a></li>
        <li><a href="rendimento.php"><img src="../../imgs/rendimento.png" alt="Rendimento" style="width:20px; margin-right:10px;"> Rendimento</a></li>
        <li><a href="lista-chats.php"><img src="../../imgs/chaat.png" alt="Chats" style="width:20px; margin-right:10px;"> Chats</a></li>
        <li><a href="notificacao.php"><img src="../../imgs/notificacao.png" alt="Chats" style="width:20px; margin-right:10px;"> Notificações</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/anuncialivro.png" alt="Cadastro" style="width:20px; margin-right:10px;"> Anunciar livro</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <li><a href="minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Editar informações</a></li>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Painel do Livreiro</h1>
  </div>

  <main>
    <br><br><br>
    <div class="header">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>">
      <?php else: ?>
        <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
      <?php endif; ?>
      <div class="header-text">
        <h1>Bem-vindo, <?= $nome_vendedor ? htmlspecialchars($nome_vendedor) : 'Usuário'; ?></h1>
        <p>Acompanhe seu desempenho como vendedor</p>
      </div>
    </div>
    
    <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
    
    <div class="cards">
      <div class="card">
        <h2>Reputação</h2>
        <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
        <div class="progress-bar">
          <div class="progress" style="width: <?= $reputacao ?>%;">
            <?= $reputacao ?>%
          </div>
        </div>
        <p class="info">Ainda é só o começo, com boas avaliações, entregas no prazo e feedback dos clientes, sua reputação aumenta e consegue aumentar sua clientela!</p>
      </div>
      <div class="card">
        <h2>Taxa de Vendas</h2>
        <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
          <p><strong>Taxa de sucesso:</strong> 0%</p>
          <p><strong>Vendas concluídas:</strong> <?php echo $vendas_concluidas; ?></p>
          <p><strong>Anúncios publicadas:</strong> <?php echo $anuncios_publicadas; ?></p>
        </div>
    </div>

    <div class="card">
      <h2>Pedidos para Entregar</h2>
      <table class="table">
        <tr>
          <th>#</th>
          <th>Produto</th>
          <th>Comprador</th>
          <th>Status</th>
          <th>Data do Pedido</th>
        </tr>

        <?php if (!empty($pedidos)): ?>
            <?php $contador = 1; ?>
            <?php foreach ($pedidos as $item): ?>         
                <tr>
                    <td><?= $contador ?></td>
                    <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td><?= htmlspecialchars($item['comprador_nome'] ?? '') ?></td>
                    <td><?= strtoupper(htmlspecialchars($item['status_envio'] ?? '')) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['data_pedido'])) ?></td>
                </tr>
                <?php $contador++; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center;">Nenhum pedido encontrado</td>
            </tr>
        <?php endif; ?>
      </table>
    </div>
          <br>
    <div class="grid">
      <div class="card">
        <h2>Avaliações de Clientes</h2>
        <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
        <p>Nenhuma avaliação recebida ainda.</p>
      </div>

      <div class="card"> 
          <h2>Notificações</h2>
          <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>

          <?php if (!empty($notificacoes)): ?>
              <?php foreach ($notificacoes as $notif): ?>
                  <p style="margin-bottom: 8px;">
                      <?= $notif['lida'] == 0 ? '🔔' : '✅' ?>
                      <?= htmlspecialchars($notif['mensagem']) ?>
                      <br>
                      <small style="color: #777;">De: <?= htmlspecialchars($notif['usuario_nome'] ?? '  ') ?> | <?= date('d/m/Y H:i', strtotime($notif['data_envio'])) ?></small>
                  </p>
              <?php endforeach; ?>
          <?php else: ?>
              <p>Nenhuma notificação no momento.</p>
          <?php endif; ?>
      </div>
    </div>
  </main>
  
  <!-- VLibras - Widget de Libras -->
<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>
</body>
</html>