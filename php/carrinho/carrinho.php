<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

// Dados de exemplo para o carrinho
$carrinho_itens = [
    [
        'id' => 1,
        'imagem' => '../../imgs/Extraordinário.jpg',
        'titulo' => 'Extraordinário',
        'descricao' => 'Capa comum - Edição padrão, 3/janeiro 2013',
        'preco' => 42.29,
        'quantidade' => 1
    ],
    [
        'id' => 2,
        'imagem' => '../../imgs/A Menina que Roubava Livros.jpg',
        'titulo' => 'A Menina que Roubava Livros',
        'descricao' => 'Capa comum - Edição padrão, 10 junho 2013',
        'preco' => 42.29,
        'quantidade' => 1
    ],
    [
        'id' => 3,
        'imagem' => '../../imgs/alice.jpg',
        'titulo' => 'Alice no País das Maravilhas (Classic Edition)',
        'descricao' => 'Capa dura - 4 outubro 2019',
        'preco' => 48.93,
        'quantidade' => 1
    ]
];

$subtotal = 0;
foreach ($carrinho_itens as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}
$frete = 0; // Frete grátis
$total = $subtotal + $frete;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Carrinho - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
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
      z-index: 1000;
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
      width: 20px;
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

    .search-form {
      display: flex;
      align-items: center;
    }
    
    .topbar input[type="text"] {
      padding: 10px 15px;
      border: none;
      border-radius: 20px 0 0 20px;
      width: 250px;
      font-size: 0.9rem;
    }
    
    .topbar input[type="submit"] {
      padding: 10px 15px;
      background: var(--verde);
      color: white;
      border: none;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
    }

    .main {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      margin-top: 70px;
    }

    .breadcrumb {
      margin-bottom: 20px;
      font-size: 0.9rem;
      color: #777;
    }

    .breadcrumb a {
      color: var(--marrom);
      text-decoration: none;
    }

    .breadcrumb span {
      margin: 0 8px;
    }

    .page-title {
      color: var(--verde);
      margin-bottom: 30px;
      font-size: 1.8rem;
    }

    .checkout-steps {
      display: flex;
      justify-content: center;
      margin-bottom: 40px;
      gap: 40px;
    }

    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--verde);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .step.active .step-number {
      background-color: var(--marrom);
    }

    .step-label {
      font-size: 0.9rem;
      color: #777;
    }

    .step.active .step-label {
      color: var(--marrom);
      font-weight: bold;
    }

    .cart-container {
      display: flex;
      gap: 30px;
    }

    .cart-items {
      flex: 2;
    }

    .cart-summary {
      flex: 1;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: fit-content;
    }

    .cart-item {
      display: flex;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .cart-item-image {
      width: 120px;
      height: 160px;
      object-fit: cover;
    }

    .cart-item-details {
      flex: 1;
      padding: 15px;
    }

    .cart-item-title {
      font-size: 1.1rem;
      color: var(--verde);
      margin-bottom: 5px;
    }

    .cart-item-desc {
      font-size: 0.9rem;
      color: #777;
      margin-bottom: 10px;
    }

    .cart-item-price {
      font-weight: bold;
      color: var(--marrom);
      margin-bottom: 10px;
    }

    .cart-item-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .quantity-control {
      display: flex;
      align-items: center;
    }

    .quantity-btn {
      width: 30px;
      height: 30px;
      background: var(--verde);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .quantity-input {
      width: 40px;
      height: 30px;
      text-align: center;
      margin: 0 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .remove-btn {
      background: none;
      border: none;
      color: #e74c3c;
      cursor: pointer;
      font-size: 0.9rem;
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

    .summary-label {
      color: #777;
    }

    .summary-value {
      font-weight: bold;
    }

    .summary-total {
      font-size: 1.1rem;
      color: var(--marrom);
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #eee;
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
    }

    .continue-shopping {
      display: inline-block;
      margin-top: 20px;
      color: var(--marrom);
      text-decoration: none;
      font-weight: bold;
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: #fff;
      text-align: center;
      padding: 15px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .topbar, .main, .footer {
        margin-left: 200px;
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .topbar, .main, .footer {
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
        <?php if (!$nome): ?>
          <a href="php/cadastro/cadastroUsuario.php" style="text-decoration: none; color: white;">
            <p class="nome-usuario">Entre ou crie sua conta</p>
          </a>
        <?php else: ?>
          <p class="nome-usuario"><?= htmlspecialchars($nome) ?></p>
        <?php endif; ?>
      </div>
  </div>

  <nav>
    <ul class="menu">
      <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
      <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
      <li><a href="../destaques/destaques.php"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
      <li><a href="../favoritos/favoritos.php"><img src="../../imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
      <li><a href="../carrinho/carrinho.php"><img src="../../imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
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
      <?php endif; ?>

      <?php if ($nome === 'adm'): ?>
        <li><a href="../consulta/consulta.php"><img src="../../imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
        <li><a href="../consultaFiltro/busca.php"><img src="../../imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      <?php endif; ?>

      <?php if ($nome): ?>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>

</div>

<div class="topbar">
  <h1>Entre Linhas - Sebo Moderna</h1>
  <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
    <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
    <input type="submit" value="Buscar">
  </form>
</div>

<div class="main">
  <h1 class="page-title">Meu Carrinho</h1>

  <div class="checkout-steps">
    <div class="step active">
      <div class="step-number">1</div>
      <div class="step-label">CARRINHO</div>
    </div>
    <div class="step">
      <div class="step-number">2</div>
      <div class="step-label">CHECKOUT</div>
    </div>
    <div class="step">
      <div class="step-number">3</div>
      <div class="step-label">PEDIDO FINALIZADO</div>
    </div>
  </div>

  <div class="cart-container">
    <div class="cart-items">
      <?php foreach ($carrinho_itens as $item): ?>
        <div class="cart-item">
          <img src="<?= $item['imagem'] ?>" alt="<?= $item['titulo'] ?>" class="cart-item-image">
          <div class="cart-item-details">
            <h3 class="cart-item-title"><?= $item['titulo'] ?></h3>
            <p class="cart-item-desc"><?= $item['descricao'] ?></p>
            <p class="cart-item-price">R$ <?= number_format($item['preco'], 2, ',', '.') ?></p>
            <div class="cart-item-actions">
              <div class="quantity-control">
                <button class="quantity-btn" onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                <input type="number" class="quantity-input" value="<?= $item['quantidade'] ?>" min="1" id="quantity-<?= $item['id'] ?>">
                <button class="quantity-btn" onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
              </div>
              <button class="remove-btn" onclick="removeItem(<?= $item['id'] ?>)">
                Remover
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      
      <a href="../../index.php" class="continue-shopping">
        ← Continuar comprando
      </a>
    </div>
    
    <div class="cart-summary">
      <h2 class="summary-title">Resumo do Pedido</h2>
      
      <div class="summary-row">
        <span class="summary-label">Subtotal</span>
        <span class="summary-value">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
      </div>
      
      <div class="summary-row">
        <span class="summary-label">Frete</span>
        <span class="summary-value">Grátis</span>
      </div>
      
      <div class="summary-row summary-total">
        <span class="summary-label">Total</span>
        <span class="summary-value">R$ <?= number_format($total, 2, ',', '.') ?></span>
      </div>
      
      <button class="checkout-btn" onclick="window.location.href='checkout.php'">
        Finalizar Compra
      </button>
    </div>
  </div>
</div>  

<div class="footer">
  &copy; 2025 Entre Linhas - Todos os direitos reservados.
</div>

<script>
  function updateQuantity(itemId, change) {
    const input = document.getElementById('quantity-' + itemId);
    let newValue = parseInt(input.value) + change;
    
    if (newValue < 1) newValue = 1;
    
    input.value = newValue;
    console.log('Quantidade atualizada:', newValue);
  }
  
  function removeItem(itemId) {
    if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
      console.log('Item removido:', itemId);
    }
  }
</script>
</body>
</html>