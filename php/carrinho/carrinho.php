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
      <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
      <div class="user-info">
        <a href="php/cadastro/cadastroUsuario.php" style="text-decoration: none; color: white;">
          <p class="nome-usuario">Entre ou crie sua conta</p>
        </a>
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
      <li><a href="../login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
      <li><a href="../cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
      <li><a href="../cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
      <li><a href="../login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
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
      <div class="form-section">
        <h2 class="section-title">Informações de Entrega</h2>
        
        <div class="form-row">
          <div class="form-group">
            <label for="nome">Nome completo *</label>
            <input type="text" id="nome" placeholder="Seu nome completo" required>
          </div>
          <div class="form-group">
            <label for="telefone">Telefone *</label>
            <input type="tel" id="telefone" placeholder="(11) 99999-9999" required>
          </div>
        </div>
        
        <div class="form-group">
          <label for="email">E-mail *</label>
          <input type="email" id="email" placeholder="seu@email.com" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="cep">CEP *</label>
            <input type="text" id="cep" placeholder="00000-000" required>
          </div>
          <div class="form-group">
            <label for="estado">Estado *</label>
            <select id="estado" required>
              <option value="">Selecione</option>
              <option value="AC">Acre</option>
              <option value="AL">Alagoas</option>
              <option value="AP">Amapá</option>
              <option value="AM">Amazonas</option>
              <option value="BA">Bahia</option>
              <option value="CE">Ceará</option>
              <option value="DF">Distrito Federal</option>
              <option value="ES">Espírito Santo</option>
              <option value="GO">Goiás</option>
              <option value="MA">Maranhão</option>
              <option value="MT">Mato Grosso</option>
              <option value="MS">Mato Grosso do Sul</option>
              <option value="MG">Minas Gerais</option>
              <option value="PA">Pará</option>
              <option value="PB">Paraíba</option>
              <option value="PR">Paraná</option>
              <option value="PE">Pernambuco</option>
              <option value="PI">Piauí</option>
              <option value="RJ">Rio de Janeiro</option>
              <option value="RN">Rio Grande do Norte</option>
              <option value="RS">Rio Grande do Sul</option>
              <option value="RO">Rondônia</option>
              <option value="RR">Roraima</option>
              <option value="SC">Santa Catarina</option>
              <option value="SP">São Paulo</option>
              <option value="SE">Sergipe</option>
              <option value="TO">Tocantins</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label for="cidade">Cidade *</label>
          <input type="text" id="cidade" placeholder="Sua cidade" required>
        </div>
        
        <div class="form-group">
          <label for="bairro">Bairro *</label>
          <input type="text" id="bairro" placeholder="Seu bairro" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="endereco">Endereço *</label>
            <input type="text" id="endereco" placeholder="Nome da rua, avenida, etc." required>
          </div>
          <div class="form-group">
            <label for="numero">Número *</label>
            <input type="text" id="numero" placeholder="Nº" required>
          </div>
        </div>
        
        <div class="form-group">
          <label for="complemento">Complemento</label>
          <input type="text" id="complemento" placeholder="Apartamento, bloco, referência, etc.">
        </div>
        
        <div class="form-group">
          <label for="pais">País</label>
          <input type="text" id="pais" value="Brasil" readonly>
        </div>
        
        <div class="form-group">
          <label for="observacoes">Observações para a entrega</label>
          <textarea id="observacoes" rows="3" placeholder="Instruções especiais para a entrega..."></textarea>
        </div>
      </div>

      <!-- Método de Pagamento -->
      <div class="form-section">
        <h2 class="section-title">Método de Pagamento</h2>
        
        <div class="payment-methods">
          <label class="payment-option selected">
            <input type="radio" name="pagamento" value="cartao" checked>
            Cartão de Crédito/Débito
          </label>
          
          <label class="payment-option">
            <input type="radio" name="pagamento" value="pix">
            PIX
          </label>
          
          <label class="payment-option">
            <input type="radio" name="pagamento" value="boleto">
            Boleto Bancário
          </label>
        </div>
        
        <div id="cartao-info" class="form-section">
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
      </div>
    </div>

    <!-- Resumo do Pedido -->
    <div class="order-summary">
      <h2 class="section-title">Resumo do Pedido</h2>
      
      <div class="order-items" id="order-items-container">
        <!-- Itens serão carregados aqui via JavaScript -->
      </div>
      
      <div id="empty-cart-message" class="empty-cart" style="display: none;">
        <h3>Seu carrinho está vazio</h3>
        <p>Adicione alguns livros incríveis ao seu carrinho!</p>
        <a href="../../index.php" class="confirm-btn" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px; margin-top: 15px;">
          Continuar comprando
        </a>
      </div>
      
      <div class="coupon-section">
        <label for="cupom">Cupom de Desconto</label>
        <div class="coupon-input">
          <input type="text" id="cupom" placeholder="Digite seu cupom">
          <button onclick="aplicarCupom()">Aplicar</button>
        </div>
        <div id="cupom-mensagem" style="margin-top: 5px; font-size: 0.9rem;"></div>
      </div>
      
      <div class="summary-totals">
        <div class="total-row">
          <span class="total-label">Subtotal (<span id="items-count">0</span> itens)</span>
          <span class="total-value" id="subtotal-value">R$ 0,00</span>
        </div>
        
        <div class="total-row">
          <span class="total-label">Frete</span>
          <span class="total-value">Grátis</span>
        </div>
        
        <div class="total-row">
          <span class="total-label">Desconto</span>
          <span class="total-value" id="desconto-valor">R$ 0,00</span>
        </div>
        
        <div class="total-row grand-total">
          <span class="total-label">Total</span>
          <span class="total-value" id="total-final">R$ 0,00</span>
        </div>
      </div>
      
      <button class="confirm-btn" id="confirm-button" onclick="finalizarPedido()">Confirmar Pedido</button>
    </div>
  </div>
</div>  

<div class="footer">
  &copy; 2025 Entre Linhas - Todos os direitos reservados.
</div>

<script>
  // Dados dos produtos
  const produtos = [
    {
      id: 1,
      nome: "Extraordinário",
      detalhes: "Capa comum - Edição padrão",
      preco: 42.29,
      quantidade: 1,
      imagem: "../../imgs/Extraordinário.jpg"
    },
    {
      id: 2,
      nome: "A Menina que Roubava Livros",
      detalhes: "Capa comum - Edição padrão",
      preco: 42.29,
      quantidade: 1,
      imagem: "../../imgs/A Menina que Roubava Livros.jpg"
    },
    {
      id: 3,
      nome: "Alice no País das Maravilhas",
      detalhes: "Capa dura - Classic Edition",
      preco: 48.93,
      quantidade: 1,
      imagem: "../../imgs/alice.jpg"
    }
  ];

  let descontoAplicado = 0;
  let cupomAtivo = '';

  // Carregar os itens do pedido
  function carregarItensPedido() {
    const container = document.getElementById('order-items-container');
    const emptyMessage = document.getElementById('empty-cart-message');
    const confirmButton = document.getElementById('confirm-button');
    
    if (produtos.length === 0) {
      container.style.display = 'none';
      emptyMessage.style.display = 'block';
      confirmButton.disabled = true;
      return;
    }
    
    container.style.display = 'block';
    emptyMessage.style.display = 'none';
    confirmButton.disabled = false;
    
    let html = '';
    
    produtos.forEach(produto => {
      const totalItem = produto.preco * produto.quantidade;
      html += `
        <div class="order-item" data-id="${produto.id}">
          <img src="${produto.imagem}" alt="${produto.nome}" class="item-image">
          <div class="item-info">
            <div class="item-name">${produto.nome}</div>
            <div class="item-details">${produto.detalhes}</div>
            <div class="item-controls">
              <div class="quantity-control">
                <button class="quantity-btn" onclick="alterarQuantidade(${produto.id}, -1)">-</button>
                <input type="number" class="quantity-input" value="${produto.quantidade}" min="1" id="qty-${produto.id}">
                <button class="quantity-btn" onclick="alterarQuantidade(${produto.id}, 1)">+</button>
              </div>
              <button class="remove-btn" onclick="removerItem(${produto.id})">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="2"/>
                </svg>
                Remover
              </button>
            </div>
          </div>
          <div class="item-price">
            R$ ${produto.preco.toFixed(2).replace('.', ',')}
            <div class="item-total">R$ ${totalItem.toFixed(2).replace('.', ',')}</div>
          </div>
        </div>
      `;
    });
    
    container.innerHTML = html;
    atualizarResumo();
  }

  // Alterar quantidade do produto
  function alterarQuantidade(produtoId, alteracao) {
    const produto = produtos.find(p => p.id === produtoId);
    if (!produto) return;
    
    const input = document.getElementById(`qty-${produtoId}`);
    let novaQuantidade = parseInt(input.value) + alteracao;
    
    if (novaQuantidade < 1) novaQuantidade = 1;
    
    input.value = novaQuantidade;
    produto.quantidade = novaQuantidade;
    
    atualizarResumo();
  }

  // Remover item do pedido
  function removerItem(produtoId) {
    if (confirm('Tem certeza que deseja remover este item do pedido?')) {
      const index = produtos.findIndex(p => p.id === produtoId);
      if (index !== -1) {
        produtos.splice(index, 1);
        carregarItensPedido();
      }
    }
  }

  // Atualizar resumo do pedido
  function atualizarResumo() {
    let subtotal = 0;
    let totalItens = 0;
    
    produtos.forEach(produto => {
      subtotal += produto.preco * produto.quantidade;
      totalItens += produto.quantidade;
    });
    
    const frete = 0; // Frete grátis
    const desconto = (subtotal * descontoAplicado) / 100;
    const total = subtotal + frete - desconto;
    
    document.getElementById('items-count').textContent = totalItens;
    document.getElementById('subtotal-value').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('desconto-valor').textContent = `-R$ ${desconto.toFixed(2).replace('.', ',')}`;
    document.getElementById('total-final').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
  }

  // Aplicar cupom de desconto
  function aplicarCupom() {
    const cupomInput = document.getElementById('cupom');
    const mensagem = document.getElementById('cupom-mensagem');
    
    const cupom = cupomInput.value.trim().toUpperCase();
    
    // Cupons válidos (exemplo)
    const cuponsValidos = {
      'ENTRELINHAS10': 10,
      'LIVRO15': 15,
      'PRIMEIRACOMPRA': 20
    };
    
    if (cupom in cuponsValidos) {
      descontoAplicado = cuponsValidos[cupom];
      cupomAtivo = cupom;
      
      mensagem.textContent = `Cupom "${cupom}" aplicado! ${descontoAplicado}% de desconto.`;
      mensagem.style.color = 'green';
    } else {
      descontoAplicado = 0;
      cupomAtivo = '';
      
      mensagem.textContent = 'Cupom inválido ou expirado.';
      mensagem.style.color = 'red';
    }
    
    atualizarResumo();
  }

  // Finalizar pedido
  function finalizarPedido() {
    if (produtos.length === 0) {
      alert('Seu carrinho está vazio!');
      return;
    }
    
    // Validação básica dos campos obrigatórios
    const camposObrigatorios = [
      'nome', 'telefone', 'email', 'cep', 'estado', 'cidade', 
      'bairro', 'endereco', 'numero'
    ];
    
    let camposVazios = [];
    
    camposObrigatorios.forEach(campo => {
      const elemento = document.getElementById(campo);
      if (!elemento.value.trim()) {
        camposVazios.push(campo);
        elemento.style.borderColor = 'red';
      } else {
        elemento.style.borderColor = '';
      }
    });
    
    if (camposVazios.length > 0) {
      alert('Por favor, preencha todos os campos obrigatórios marcados com *');
      return;
    }
    
    alert('Pedido confirmado com sucesso! Redirecionando...');
    // Aqui você pode redirecionar para a página de confirmação
    // window.location.href = 'confirmacao.php';
  }

  // Formatação automática de campos
  document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) value = value.substring(0, 11);
    
    if (value.length > 6) {
      value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length > 2) {
      value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    } else if (value.length > 0) {
      value = value.replace(/(\d{0,2})/, '($1');
    }
    
    e.target.value = value;
  });

  document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 8) value = value.substring(0, 8);
    
    if (value.length > 5) {
      value = value.replace(/(\d{5})(\d{0,3})/, '$1-$2');
    }
    
    e.target.value = value;
  });

  // Carregar os itens quando a página for carregada
  document.addEventListener('DOMContentLoaded', carregarItensPedido);
</script>
</body>
</html>