<?php
session_start();
include '../conexao.php';

// Adicionar produto ao carrinho (pela URL)
if (isset($_GET['id']) && isset($_GET['nome']) && isset($_GET['preco'])) {
    $id = intval($_GET['id']);
    $nome = htmlspecialchars($_GET['nome']);
    $preco = floatval($_GET['preco']);

    // Pega frete se houver
    $frete = isset($_GET['frete']) ? json_decode(base64_decode($_GET['frete']), true) : null;

    // Cria o item
    $item = [
        'id' => $id,
        'nome' => $nome,
        'preco' => $preco,
        'frete' => $frete // adiciona info do frete
    ];

    // Verifica se o produto já está no carrinho
    $existe = false;
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    foreach ($_SESSION['carrinho'] as $p) {
        if ($p['id'] == $id) {
            $existe = true;
            break;
        }
    }

    if (!$existe) {
        $_SESSION['carrinho'][] = $item;
    }

    header("Location: carrinho.php");
    exit;
}

// Remover produto
if (isset($_GET['remover_id'])) {
    $idRemover = intval($_GET['remover_id']);
    $_SESSION['carrinho'] = array_filter($_SESSION['carrinho'], function($p) use ($idRemover) {
      return $p['id'] != $idRemover;
  });
  
    header("Location: carrinho.php");
    exit;
}

// Garante que o carrinho exista
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

if (!$nome) {
    header('Location: ../login/login.php');
}

// Calcular subtotal
$subtotal = array_sum(array_column($_SESSION['carrinho'], 'preco'));
$total = $subtotal;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seu carrinho - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
      --branco: #ffffff;
      --cinza-claro: #f8f8f8;
      --cinza-medio: #e0e0e0;
      --cinza-escuro: #777777;
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
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
      z-index: 1000;
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background-color: #ccc;
      border-radius: 4px;
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

    .sidebar nav ul li a img {
      margin-right: 10px;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #5a4226;
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 70px;
      z-index: 999;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .topbar-left .logo {
      height: 50px;
    }

    .topbar-left h1 {
      font-size: 22px;
      color: #fff;
      margin: 0;
      font-weight: bold;
    }

    .search-form {
      display: flex;
      align-items: center;
    }

    .search-form input[type="text"] {
      padding: 10px 15px;
      border: none;
      border-radius: 30px 0 0 30px;
      outline: none;
      width: 300px;
      font-size: 0.9rem;
    }

    .search-form input[type="submit"] {
      padding: 10px 15px;
      border: none;
      background-color: #6f8562;
      color: #fff;
      border-radius: 0 30px 30px 0;
      cursor: pointer;
      width: 90px;
    }

    .main {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      margin-top: 70px;
    }

    .page-title {
      color: var(--verde);
      margin-bottom: 30px;
      font-size: 1.8rem;
    }

    .cart-container {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
    }

    .cart-items {
      flex: 2;
      min-width: 300px;
    }

    .cart-summary {
      flex: 1;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: fit-content;
      min-width: 300px;
      position: sticky;
      top: 100px;
    }

    .cart-item {
      display: flex;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .cart-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .cart-item-image {
      width: 120px;
      height: 160px;
      object-fit: cover;
    }

    .cart-item-details {
      flex: 1;
      padding: 15px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .cart-item-title {
      font-size: 1.1rem;
      color: var(--verde);
      margin-bottom: 5px;
    }

    .cart-item-price {
      font-weight: bold;
      color: var(--marrom);
      margin-bottom: 10px;
      font-size: 1.1rem;
    }

    .cart-item-actions {
      display: flex;
      gap: 15px;
      margin-top: 10px;
    }

    .action-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      padding: 5px 0;
      transition: color 0.3s;
    }

    .remove-btn {
      color: #e74c3c;
    }

    .remove-btn:hover {
      color: #c0392b;
    }

    .product-btn {
      color: var(--verde);
    }

    .product-btn:hover {
      color: var(--marrom);
    }

    .action-btn a {
      color: inherit;
      text-decoration: none;
    }

    .summary-title {
      font-size: 1.2rem;
      color: var(--verde);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .summary-total {
      font-size: 1.1rem;
      color: var(--marrom);
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #eee;
      font-weight: bold;
    }

    .checkout-btn {
      width: 100%;
      padding: 15px;
      background: var(--marrom);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20px;
      transition: background 0.3s;
    }

    .checkout-btn:hover {
      background: #6b4d2a;
    }

    .continue-shopping {
      display: inline-block;
      margin-top: 20px;
      color: var(--marrom);
      text-decoration: none;
      font-weight: bold;
      padding: 10px 15px;
      border: 1px solid var(--marrom);
      border-radius: 8px;
      transition: all 0.3s;
    }

    .continue-shopping:hover {
      background: var(--marrom);
      color: white;
    }

    .empty-cart {
      text-align: center;
      padding: 40px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .empty-cart-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      color: var(--cinza-escuro);
    }

    .empty-cart p {
      color: var(--cinza-escuro);
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: #fff;
      text-align: center;
      padding: 15px;
    }

    @media (max-width: 992px) {
      .sidebar {
        width: 200px;
      }
      
      .topbar, .main, .footer {
        margin-left: 200px;
      }
      
      .cart-container {
        flex-direction: column;
      }
      
      .cart-summary {
        position: static;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 0;
        overflow: hidden;
      }
      
      .topbar, .main, .footer {
        margin-left: 0;
      }
      
      .topbar-left h1 {
        font-size: 18px;
      }
      
      .search-form input[type="text"] {
        width: 200px;
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
        <p class="nome-usuario"><?= htmlspecialchars($nome) ?></p>
      </div>
  </div>

  <nav>
    <ul class="menu">
      <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px;"> Início</a></li>
      <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px;"> Comunidades</a></li>
      <li><a href="../destaques/destaques.php"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px;"> Destaques</a></li>
      <li><a href="../favoritos/favoritos.php"><img src="../../imgs/favoritos.png" alt="Favoritos" style="width:20px;"> Favoritos</a></li>
      <li><a href="../carrinho/carrinho.php"><img src="../../imgs/carrinho.png" alt="Carrinho" style="width:20px;"> Carrinho</a></li>
    </ul>

    <h3>Conta</h3>
    <ul class="account">
      <?php if (!$nome): ?>
        <li><a href="../login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
        <li><a href="../cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
        <li><a href="../cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
        <li><a href="../login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
      <?php else: ?>
        <li><a href="../perfil-usuario/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<div class="topbar">
  <div class="topbar-left">
    <img src="../../imgs/logotipo.png" alt="Entre Linhas" class="logo">
    <h1>Entre Linhas - Carrinho</h1>
  </div>
  <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
    <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
    <input type="submit" value="Buscar">
  </form>
</div>

<div class="main">
  <h1 class="page-title">Meu Carrinho</h1>

  <div class="cart-container">
    <div class="cart-items">
      <?php if (empty($_SESSION['carrinho'])): ?>
        <div class="empty-cart">
          <div class="empty-cart-icon">📚</div>
          <p>Seu carrinho está vazio 😔</p>
          <a href="../../index.php" class="continue-shopping">Continuar comprando</a>
        </div>
      <?php else: ?>
        <?php foreach ($_SESSION['carrinho'] as $item): ?>
          <?php
            $stmt = $conn->prepare("SELECT imagem FROM imagens WHERE idproduto = ?");
            $stmt->execute([$item['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $src = ($row && !empty($row['imagem'])) 
              ? "data:image/jpeg;base64," . base64_encode($row['imagem']) 
              : "../../imgs/capa.jpg";
          ?>
          
          <div class="cart-item">
            <img src="<?= $src ?>" class="cart-item-image" alt="<?= htmlspecialchars($item['nome']) ?>">

            <div class="cart-item-details">
              <div>
                <h3 class="cart-item-title"><?= htmlspecialchars($item['nome']) ?></h3>

                <p class="cart-item-price">R$ <?= number_format($item['preco'], 2, ',', '.') ?></p>

                <p class="cart-item-frete" style="margin-top:5px; color:#555;">
                    <strong>Frete:</strong> <?= htmlspecialchars($item['frete']['nome'] ?? '-') ?> — 
                    R$ <?= number_format(floatval(str_replace(',', '.', $item['frete']['preco'] ?? 0)), 2, ',', '.') ?> 
                    (<?= htmlspecialchars($item['frete']['prazo'] ?? '-') ?> dias úteis)
                </p>

              </div>
              
              <div class="cart-item-actions">
                <button class="action-btn remove-btn">
                  <a href="carrinho.php?remover_id=<?= $item['id'] ?>">Remover</a>
                </button>
            
                <button class="action-btn product-btn">
                  <a href="../produto/pagiproduto.php?id=<?= $item['id']?>">Ver produto</a>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        
        <a href="../../index.php" class="continue-shopping">← Continuar comprando</a>
      <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['carrinho'])): ?>
    <div class="cart-summary">
      <?php
        $subtotalLivros = array_sum(array_map(function($p) {
          return floatval($p['preco']);
        }, $_SESSION['carrinho']));
      
        $totalFrete = array_sum(array_map(function($p) {
          return isset($p['frete']['preco']) ? floatval($p['frete']['preco']) : 0;
        }, $_SESSION['carrinho']));
      
        $totalGeral = $subtotalLivros + $totalFrete;
      ?>
      <h2 class="summary-title">Resumo do Pedido</h2>

      <div class="summary-row">
        <span class="summary-label">Subtotal dos Livros</span>
        <span class="summary-value">R$ <?= number_format($subtotalLivros, 2, ',', '.') ?></span>
      </div>

      <div class="summary-row">
        <span class="summary-label">Total do Frete</span>
        <span class="summary-value">R$ <?= number_format($totalFrete, 2, ',', '.') ?></span>
      </div>

      <div class="summary-row summary-total">
        <span class="summary-label">Total Geral</span>
        <span class="summary-value">R$ <?= number_format($totalGeral, 2, ',', '.') ?></span>
      </div>

      <button class="checkout-btn" onclick="window.location.href='finalizarcompra.php'">Finalizar Compra</button>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="footer">&copy; 2025 Entre Linhas - Todos os direitos reservados.</div>

</body>
</html>