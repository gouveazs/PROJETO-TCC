<?php
session_start();
include '../conexao.php';

$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

if (!$nome) {
    header('Location: ../login/login.php');
    exit;
}

// Buscar dados do usuário no banco
$stmt = $conn->prepare("SELECT * FROM usuario WHERE nome = ?");
$stmt->execute([$nome]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Garantir que o carrinho exista
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Calcular valores do carrinho
$subtotalLivros = array_sum(array_map(function($p) {
  return floatval($p['preco']);
}, $_SESSION['carrinho']));
$totalFrete = array_sum(array_map(function($p) {
  return isset($p['frete']['preco']) ? floatval($p['frete']['preco']) : 0;
}, $_SESSION['carrinho']));
$totalGeral = $subtotalLivros + $totalFrete;
$totalItens = count($_SESSION['carrinho']);

if (isset($_POST['finalizar'])) {
    $dados = [
        'nome_completo' => $_POST['nome_completo'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'cep' => $_POST['cep'],
        'estado' => $_POST['estado'],
        'cidade' => $_POST['cidade'],
        'bairro' => $_POST['bairro'],
        'rua' => $_POST['rua'],
        'numero' => $_POST['numero'],
        'nome' => $nome
    ];

    // Verifica se o usuário já tem registro
    $stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE nome = ?");
    $stmt->execute([$nome]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        // Atualiza os dados existentes
        $sql = "UPDATE usuario SET
                    nome_completo = :nome_completo,
                    telefone = :telefone,
                    email = :email,
                    cep = :cep,
                    estado = :estado,
                    cidade = :cidade,
                    bairro = :bairro,
                    rua = :rua,
                    numero = :numero
                WHERE nome = :nome";
        $stmt = $conn->prepare($sql);
        $stmt->execute($dados);
    } else {
        // Insere novo registro
        $sql = "INSERT INTO usuario (nome_completo, telefone, email, cep, estado, cidade, bairro, rua, numero, nome)
                VALUES (:nome_completo, :telefone, :email, :cep, :estado, :cidade, :bairro, :rua, :numero, :nome)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($dados);
    }

    header('Location: finalizarcompra.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
      --borda: #e0d9d0;
      --texto-claro: #777;
      --cinza-claro: #f8f8f8;
      --verde-claro: #e8f5e8;
      --vermelho: #e74c3c;
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

    .page-title {
      color: var(--verde);
      margin-bottom: 30px;
      font-size: 1.8rem;
    }

    .checkout-container {
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 30px;
    }

    .checkout-form {
      background: white;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .form-section {
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 1.2rem;
      color: var(--verde);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--borda);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 15px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: var(--texto-claro);
      font-weight: bold;
    }

    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--borda);
      border-radius: 4px;
      font-size: 1rem;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .payment-methods {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .payment-option {
      display: flex;
      align-items: center;
      padding: 15px;
      border: 1px solid var(--borda);
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .payment-option:hover {
      border-color: var(--verde);
    }

    .payment-option.selected {
      border-color: var(--verde);
      background-color: var(--verde-claro);
    }

    .payment-option input {
      margin-right: 10px;
    }

    .payment-info {
      margin-top: 20px;
      padding: 20px;
      border: 1px solid var(--borda);
      border-radius: 4px;
      background-color: var(--cinza-claro);
    }

    .qr-code {
      text-align: center;
      padding: 20px;
    }

    .qr-code img {
      max-width: 200px;
      margin-bottom: 15px;
    }

    .pix-info {
      background-color: var(--verde-claro);
      padding: 15px;
      border-radius: 4px;
      margin-top: 15px;
    }

    .boleto-info {
      text-align: center;
      padding: 20px;
    }

    .boleto-btn {
      background-color: var(--marrom);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      margin-top: 15px;
    }

    .boleto-btn:hover {
      background-color: #6b4d2a;
    }

    .order-summary {
      background: white;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      height: fit-content;
      position: sticky;
      top: 100px;
    }

    .order-items {
      margin-bottom: 20px;
    }

    .order-item {
      display: flex;
      align-items: flex-start;
      padding: 15px 0;
      border-bottom: 1px solid var(--borda);
      position: relative;
    }

    .item-image {
      width: 60px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 15px;
    }

    .item-info {
      flex: 1;
    }

    .item-name {
      font-weight: bold;
      color: var(--verde);
      margin-bottom: 5px;
    }

    .item-details {
      font-size: 0.9rem;
      color: var(--texto-claro);
      margin-bottom: 10px;
    }

    .item-controls {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      border: 1px solid var(--borda);
      border-radius: 4px;
      overflow: hidden;
    }

    .quantity-btn {
      width: 30px;
      height: 30px;
      background: var(--verde);
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .quantity-btn:hover {
      background: #6f8562;
    }

    .quantity-input {
      width: 40px;
      height: 30px;
      text-align: center;
      border: none;
      border-left: 1px solid var(--borda);
      border-right: 1px solid var(--borda);
    }

    .remove-btn {
      background: none;
      border: none;
      color: var(--vermelho);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 0.9rem;
      padding: 5px;
      border-radius: 4px;
      transition: background 0.2s;
    }

    .remove-btn:hover {
      background: #ffeaea;
    }

    .item-price {
      font-weight: bold;
      color: var(--marrom);
      white-space: nowrap;
      margin-left: 15px;
      text-align: right;
    }

    .item-total {
      font-size: 1rem;
      margin-top: 5px;
    }

    .coupon-section {
      margin: 20px 0;
      padding: 15px;
      background-color: var(--verde-claro);
      border-radius: 4px;
    }

    .coupon-input {
      display: flex;
      gap: 10px;
    }

    .coupon-input input {
      flex: 1;
      padding: 10px;
      border: 1px solid var(--borda);
      border-radius: 4px;
    }

    .coupon-input button {
      padding: 10px 15px;
      background: var(--verde);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .summary-totals {
      border-top: 1px solid var(--borda);
      padding-top: 15px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .total-label {
      color: var(--texto-claro);
    }

    .total-value {
      font-weight: bold;
    }

    .grand-total {
      font-size: 1.2rem;
      color: var(--marrom);
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--borda);
    }

    .confirm-btn {
      width: 100%;
      padding: 15px;
      background: var(--marrom);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20px;
      transition: background 0.3s;
    }

    .confirm-btn:hover {
      background: #6b4d2a;
    }

    .empty-cart {
      text-align: center;
      padding: 40px 20px;
      color: var(--texto-claro);
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: #fff;
      text-align: center;
      padding: 15px;
    }

    @media (max-width: 1024px) {
      .checkout-container {
        grid-template-columns: 1fr;
      }
      
      .order-summary {
        position: static;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .topbar, .main, .footer {
        margin-left: 200px;
      }
      
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .order-item {
        flex-direction: column;
      }
      
      .item-image {
        width: 100%;
        height: 150px;
        object-fit: contain;
        margin-right: 0;
        margin-bottom: 10px;
      }
      
      .item-controls {
        justify-content: space-between;
        width: 100%;
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .topbar, .main, .footer {
        margin-left: 0;
      }
      
      .topbar {
        flex-direction: column;
        height: auto;
        padding: 15px;
      }
      
      .topbar h1 {
        margin-bottom: 15px;
      }
      
      .main {
        padding: 15px;
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
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<div class="topbar">
  <h1>Checkout - Entre Linhas</h1>
  <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
    <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
    <input type="submit" value="Buscar">
  </form>
</div>

<div class="main">
  <h1 class="page-title">Finalizar Compra</h1>

  <div class="checkout-container">
    <div class="checkout-form">
      <!-- Informações de Entrega -->
      <form action="finalizarcompra.php" method="POST" class="form-section">
        <h2 class="section-title">Informações de Entrega</h2>

        <div class="form-row">
            <div class="form-group">
                <label for="nome_completo">Nome completo *</label>
                <input type="text" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone *</label>
                <input type="tel" name="telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">E-mail *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="cep">CEP *</label>
            <input type="text" name="cep" id="cep" value="<?= htmlspecialchars($usuario['cep'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado *</label>
            <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($usuario['estado'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="cidade">Cidade *</label>
            <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($usuario['cidade'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="bairro">Bairro *</label>
            <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($usuario['bairro'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rua">Endereço *</label>
                <input type="text" name="rua" id="rua" value="<?= htmlspecialchars($usuario['rua'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="numero">Número *</label>
                <input type="text" name="numero" id="numero" value="<?= htmlspecialchars($usuario['numero'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="pais">País</label>
            <input type="text" name="pais" value="Brasil" readonly>
        </div>

        <button type="submit" name="finalizar">Completar informações</button>
      </form>

      <!-- Método de Pagamento -->
      <div class="form-section">
        <h2 class="section-title">Método de Pagamento</h2>
        
        <div class="payment-methods">
          <label class="payment-option" id="pix-option">
            <input type="radio" name="pagamento" value="pix">
            PIX
          </label>

          <label class="payment-option selected" id="cartao-option">
            <input type="radio" name="pagamento" value="cartao" checked>
            Cartão de Crédito/Débito
          </label>
          
          <label class="payment-option" id="boleto-option">
            <input type="radio" name="pagamento" value="boleto">
            Boleto Bancário
          </label>
        </div>

        <!-- Informações do PIX -->
        <div id="pix-info" class="payment-info" style="display: none;">
          <div class="qr-code">
            <img src="../../imgs/qr-code-placeholder.png" alt="QR Code PIX">
            <p>Escaneie o QR Code com o aplicativo do seu banco</p>
          </div>
          <div class="pix-info">
            <p><strong>Chave PIX:</strong> contato@entrelinhas.com.br</p>
            <p><strong>Valor:</strong> <span id="pix-valor">R$ 0,00</span></p>
            <p><strong>Vencimento:</strong> 30 minutos</p>
          </div>
        </div>


        <!-- Informações do Cartão -->
        <div id="cartao-info" class="payment-info">
          <div class="form-group">
            <label for="numero-cartao">Número do Cartão *</label>
            <input type="text" id="numero-cartao" placeholder="0000 0000 0000 0000" required>
          </div>
          
          <div class="form-group">
            <label for="nome-cartao">Nome no Cartão *</label>
            <input type="text" id="nome-cartao" placeholder="Como aparece no cartão" required>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="validade">Validade *</label>
              <input type="text" id="validade" placeholder="MM/AA" required>
            </div>
            <div class="form-group">
              <label for="cvv">CVV *</label>
              <input type="text" id="cvv" placeholder="000" required>
            </div>
          </div>
        </div>
        
        <!-- Informações do Boleto -->
        <div id="boleto-info" class="payment-info" style="display: none;">
          <div class="boleto-info">
            <p>Clique no botão abaixo para gerar seu boleto bancário</p>
            <button class="boleto-btn" onclick="gerarBoleto()">Gerar Boleto</button>
            <p><strong>Vencimento:</strong> <span id="vencimento-boleto">DD/MM/AAAA</span></p>
          </div>
        </div>

      </div>
    </div>

    <!-- Resumo do Pedido -->
    <div class="order-summary">
      <h2 class="section-title">Resumo do Pedido</h2>

      <?php if (empty($_SESSION['carrinho'])): ?>
        <div class="empty-cart">
          <h3>Seu carrinho está vazio</h3>
          <p>Adicione alguns livros incríveis ao seu carrinho!</p>
          <a href="../../index.php" class="confirm-btn" style="text-decoration: none; display: inline-block; padding: 10px 20px;">Continuar comprando</a>
        </div>
      <?php else: ?>
        <div class="order-items">
          <?php foreach ($_SESSION['carrinho'] as $item): ?>
            <?php
              $stmt = $conn->prepare("SELECT imagem FROM imagens WHERE idproduto = ?");
              $stmt->execute([$item['id']]);
              $row = $stmt->fetch(PDO::FETCH_ASSOC);
              $src = ($row && !empty($row['imagem'])) 
                ? "data:image/jpeg;base64," . base64_encode($row['imagem']) 
                : "../../imgs/capa.jpg";
            ?>
            <div class="order-item">
              <img src="<?= $src ?>" class="item-image">
              <strong><?= htmlspecialchars($item['nome']) ?></strong>
              <p>R$ <?= number_format(floatval($item['preco']), 2, ',', '.') ?></p>
              <?php if (!empty($item['frete'])): ?>
                <p style="color:#555;">
                  <strong>Frete:</strong> <?= htmlspecialchars($item['frete']['nome'] ?? '-') ?> —
                  R$ <?= number_format(floatval($item['frete']['preco'] ?? 0), 2, ',', '.') ?> 
                  (<?= htmlspecialchars($item['frete']['prazo'] ?? '-') ?> dias úteis)
                </p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="summary-totals">
          <div class="total-row">
            <span class="total-label">Subtotal dos Livros (<?= $totalItens ?> itens)</span>
            <span class="total-value">R$ <?= number_format($subtotalLivros, 2, ',', '.') ?></span>
          </div>

          <div class="total-row">
            <span class="total-label">Frete</span>
            <span class="total-value">R$ <?= number_format($totalFrete, 2, ',', '.') ?></span>
          </div>

          <div class="total-row grand-total">
            <span class="total-label">Total Geral</span>
            <span class="total-value">R$ <?= number_format($totalGeral, 2, ',', '.') ?></span>
          </div>
        </div>

        <form action="processar_pedido.php" method="POST">
          <input type="hidden" name="metodo_pagamento" value="cartao">
          <input type="hidden" name="endereco_id" value="123">
          <button type="submit" class="confirm-btn">Confirmar Compra</button>
        </form>

      <?php endif; ?>
    </div>

  </div>
</div>  

<div class="footer">
  &copy; 2025 Entre Linhas - Todos os direitos reservados.
</div>
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

<script>
  document.getElementById('cep').addEventListener('blur', function() {
      let cep = this.value.replace(/\D/g, ''); // Remove tudo que não é número
      if (cep.length === 8) {
          fetch(`https://viacep.com.br/ws/${cep}/json/`)
          .then(response => response.json())
          .then(data => {
              if (!data.erro) {
                  document.getElementById('estado').value = data.uf;
                  document.getElementById('cidade').value = data.localidade;
                  document.getElementById('bairro').value = data.bairro;
                  document.getElementById('rua').value = data.logradouro;
              } else {
                  alert('CEP não encontrado.');
              }
          })
          .catch(() => {
              alert('Erro ao consultar o CEP.');
          });
      }
  });
</script>
</body>
</html>