<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

//produtos
include 'php/conexaoVendedor.php';
$stmt = $conn->prepare("SELECT * FROM produto");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre Linhas - Livraria Moderna</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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

  .banner {
    position: relative;
    margin-left: 250px;
    margin-top: 70px;
    overflow: hidden;
  }

  .banner img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
    margin-bottom: 20px;
  }

  .main {
    flex: 1;
    margin-left: 250px;
    padding: 30px;
    margin-top: 20px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .section-header h2 {
    color: var(--verde);
  }

  .section-header .ver-mais {
    color: var(--marrom);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
  }

  .section-header .ver-mais:hover {
    color: #000;
    text-decoration: underline;
  }

  .cards {
    display: grid;
    gap: 20px;
  }

  .cards-novidades {
    grid-template-columns: repeat(6, 1fr);
  }

  .cards-recomendacoes {
    grid-template-columns: repeat(6, 1fr);
  }

  .card {
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s;
  }

  .card:hover {
    transform: translateY(-5px);
  }

  .card img {
    width: 100%;
    height: 300px;
    object-fit: cover;
  }

  .card .info {
    padding: 15px;
    text-align: center;
  }

  .card .info h3 {
    margin-bottom: 10px;
    font-size: 1rem;
    color: var(--verde);
  }

  .card .info .stars {
    color: #f5c518;
  }

  /* Estilos para a página do produto */
  .product-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  
  .product-title {
    text-align: center;
    margin-bottom: 40px;
  }
  
  .product-title h1 {
    font-size: 2.5rem;
    color: var(--marrom);
    line-height: 1;
    margin-bottom: 10px;
  }
  
  .product-title h2 {
    font-size: 3rem;
    color: var(--verde);
    line-height: 1;
  }
  
  .product-container {
    display: flex;
    gap: 40px;
    margin-bottom: 40px;
  }
  
  .product-image {
    flex: 1;
    display: flex;
    justify-content: center;
  }
  
  .product-image img {
    width: 100%;
    max-width: 400px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  
  .product-details {
    flex: 1;
    padding: 20px;
  }
  
  .product-info h3 {
    font-size: 2rem;
    color: var(--verde);
    margin-bottom: 20px;
  }
  
  .product-info p {
    margin-bottom: 15px;
    font-size: 1.1rem;
  }
  
  .product-conditions {
    margin: 20px 0;
  }
  
  .condition {
    display: inline-block;
    padding: 5px 15px;
    margin-right: 10px;
    border-radius: 20px;
    font-weight: bold;
  }
  
  .used {
    background-color: #f0f0f0;
    color: #555;
    border: 1px solid #ddd;
  }
  
  .new {
    background-color: var(--verde);
    color: white;
    border: 1px solid var(--verde);
  }
  
  .product-price {
    margin: 25px 0;
  }
  
  .product-price strong {
    font-size: 1.8rem;
    color: var(--marrom);
  }
  
  .buy-button {
    background-color: var(--verde);
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 1.2rem;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 20px;
    width: 100%;
    max-width: 300px;
  }
  
  .buy-button:hover {
    background-color: #6f8562;
  }
  
  .product-tabs ul {
    display: flex;
    list-style: none;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-top: 40px;
  }
  
  .product-tabs li {
    margin-right: 30px;
    padding: 10px 0;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.1rem;
  }
  
  .product-tabs li.active {
    color: var(--marrom);
    border-bottom: 3px solid var(--marrom);
  }

  .footer {
    margin-left: 250px;
    background-color: var(--marrom);
    color: #fff;
    text-align: center;
    padding: 15px;
  }

  @media (max-width: 1200px) {
    .cards-novidades, .cards-recomendacoes {
      grid-template-columns: repeat(4, 1fr);
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
    
    .product-container {
      flex-direction: column;
    }
    
    .product-title h1 {
      font-size: 2rem;
    }
    
    .product-title h2 {
      font-size: 2.5rem;
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
      <img src="imgs/usuario.jpg" alt="Foto de Perfil">
    <?php endif; ?>
  <div class="user-info">
    <p class="nome-usuario"><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>
    <p class="tipo-usuario"><?= $tipo ? htmlspecialchars($tipo) : 'Usuário'; ?></p>
  </div>
</div>

    <nav>
     <ul class="menu">
        <li><a href="#"><img src="imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="comunidades/comunidade.php"><img src="imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
        <li><a href="#"><img src="imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
        <li><a href="#"><img src="imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
        <li><a href="#"><img src="imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome): ?>
          <li><a href="login/login.php"><img src="imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><img src="imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><img src="imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
          <li><a href="login/loginVendedor.php"><img src="imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
        <?php else: ?>
          <li><a href="php/perfil/ver_perfil.php"><img src="imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
        <?php endif; ?>

        <?php if ($nome === 'adm'): ?>
          <li><a href="php/consulta/consulta.php"><img src="imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
          <li><a href="php/consultaFiltro/busca.php"><img src="imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
          <li><a href="php/cadastro/cadastroProduto.php"><img src="imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($tipo === 'Vendedor'): ?>
          <li><a href="php/cadastro/cadastroProduto.php"><img src="imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($nome): ?>
          <li><a href="login/logout.php"><img src="imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Livraria Moderna</h1>
    <form action="php/consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
  </div>

  <div class="main">
    <!-- Seção do produto -->
    <div class="product-page">
      <div class="product-title">
        <h1>GEORGE ORWELL</h1>
        <h2>1984</h2>
      </div>
      
      <div class="product-container">
        <div class="product-image">
          <img src="imgs/1984.jpg" alt="Capa do livro 1984">
        </div>
        
        <div class="product-details">
          <div class="product-info">
            <h3>1984</h3>
            <p><strong>Autor:</strong> George Orwell</p>
            <p><strong>Ano:</strong> 1949</p>
            <p><strong>ISBN:</strong> 9788533914849</p>
            
            <div class="product-conditions">
              <span class="condition used">Usado</span>
              <span class="condition new">Novo</span>
            </div>
            
            <div class="product-price">
              <p>Preço: <strong>R$ 35,00</strong></p>
            </div>
            
            <div class="product-description">
              <p>Descrição: Livro com leve desgaste nas bordas. Nenhuma página faltando.</p>
            </div>
            
            <div class="product-seller">
              <p>Vendido por: <strong>Selco Moderno</strong></p>
            </div>
            
            <button class="buy-button">Comprar</button>
          </div>
        </div>
      </div>
      
      <div class="product-tabs">
        <ul>
          <li class="active">Descrição</li>
          <li>Informações Adicionais</li>
          <li>Avaliações</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>