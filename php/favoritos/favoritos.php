<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

//produtos
include '../conexao.php';
$stmt = $conn->prepare("
    SELECT p.*, i.imagem
    FROM produto p
    LEFT JOIN imagens i 
        ON i.idproduto = p.idproduto
    WHERE i.idimagens = (
        SELECT idimagens
        FROM imagens
        WHERE idproduto = p.idproduto
        ORDER BY idimagens ASC
        LIMIT 1
    )
");
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
        <p class="nome-usuario"><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>
      </div>
  </div>

  <nav>
    <ul class="menu">
      <li><a href="index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
      <li><a href="php/comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
      <li><a href="#"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
      <li><a href="#"><img src="../../imgs/favoritos.png" alt="Favoritos" style="width:20px; margin-right:10px;"> Favoritos</a></li>
      <li><a href="#"><img src="../../imgs/carrinho.png" alt="Carrinho" style="width:20px; margin-right:10px;"> Carrinho</a></li>
    </ul>


    <h3>Conta</h3>
    <ul class="account">
      <?php if (!$nome): ?>
        <li><a href="php/login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
        <li><a href="php/cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
        <li><a href="php/cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
        <li><a href="php/login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
      <?php else: ?>
        <li><a href="php/perfil-usuario/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
      <?php endif; ?>

      <?php if ($nome === 'adm'): ?>
        <li><a href="php/consulta/consulta.php"><img src="../../imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
        <li><a href="php/produto/pagiproduto.php"><img src="../../imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Pagina Produto</a></li>
        <li><a href="php/consultaFiltro/busca.php"><img src="../../imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
        <li><a href="php/cadastro/cadastroProduto.php"><img src="../../imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      <?php endif; ?>

      <?php if ($nome): ?>
        <li><a href="php/login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
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
<div style="margin-left: 250px; margin-top: 70px; padding: 30px;">

  <!-- Espaço vazio (pode ser removido se não precisar) -->
  <div style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div style="flex: 2; background: #ffffff; padding: 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
      <!-- Conteúdo aqui, se necessário -->
    </div>
  </div>

  <!-- Informações rápidas (frete, garantia, etc) -->
  <div style="display: flex; justify-content: space-around; align-items: center; margin: 30px 0; flex-wrap: wrap; gap: 20px;">
    <div style="text-align: center; min-width: 150px;">
      <img src="../../imgs/entrega-rapida.png" alt="Frete" style="height: 40px;">
      <p>Frete grátis para pedidos <br><small>Acima de R$ 50</small></p>
    </div>
    <div style="text-align: center; min-width: 150px;">
      <img src="../../imgs/garantia-de-devolucao-de-dinheiro.png" alt="Garantia" style="height: 40px;">
      <p>Garantia de devolução do dinheiro<br><small>100% do seu dinheiro de volta</small></p>
    </div>
    <div style="text-align: center; min-width: 150px;">
      <img src="../../imgs/pagamento-com-cartao-de-credito.png" alt="Pagamento" style="height: 40px;">
      <p>Pagamento<br><small>Pix, Crédito e Débito</small></p>
    </div>
    <div style="text-align: center; min-width: 150px;">
      <img src="../../imgs/suporte-online.png" alt="Suporte" style="height: 40px;">
      <p>Ajuda e Suporte<br><small>(11) 4002-8922</small></p>
    </div>
  </div>

  <!-- Cards Destaques e Carrinho lado a lado -->
  <div style="display: flex; gap: 30px; flex-wrap: wrap;">
    
    <div style="flex: 1; background-color: #5A6B50; border-radius: 15px; padding: 20px; display: flex; align-items: center; justify-content: space-between; min-width: 300px;">
      <div>
        <h3 style="color: #ffffff; font-size: 1.4rem;">Destaques</h3>
        <p style="color: #ffffff; font-size: 0.9rem;">Veja todo nosso acervo de livros!</p>
        <a href="php/destaques/destaques.php" style="margin-top: 10px; display: inline-block; background-color: #5a4224; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none;">Clique aqui</a>
      </div>
      <img src="../../imgs/comuni.jpg" alt="Card Destaques" style="height: 150px; border-radius: 8px;">
    </div>

    <div style="flex: 1; background-color: #5A6B50; border-radius: 15px; padding: 20px; display: flex; align-items: center; justify-content: space-between; min-width: 300px;">
      <div>
        <h3 style="color: #ffffff; font-size: 1.4rem;">Carrinho</h3>
        <p style="color: #ffffff; font-size: 0.9rem;">Veja todos os itens adicionados!</p>
        <a href="php/destaques/destaques.php" style="margin-top: 10px; display: inline-block; background-color: #5a4224; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none;">Clique aqui</a>
      </div>
      <img src="../../imgs/comuni.jpg" alt="Card Carrinho" style="height: 150px; border-radius: 8px;">
    </div>
  </div>
</div>

<div class="main">
<!-- Novidades -->
<div class="section-header">
  <h2>Novidades</h2>
</div>

<div class="cards cards-novidades">
  <div class="card">
    <img src="../../imgs/Daisy Jone & The Six.jpg" alt="Livro 1">
    <div class="info">
      <h3>Daisy Jone & The Six</h3>
      <p class="price">R$ 44,90</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>

  <div class="card">
    <img src="../../imgs/A Menina que Roubava Livros.jpg" alt="Livro 2">
    <div class="info">
      <h3>A Menina que Roubava Livros</h3>
      <p class="price">R$ 39,50</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="../../imgs/Extraordinário.jpg" alt="Livro 3">
    <div class="info">
      <h3>Extraordinário</h3>
      <p class="price">R$ 28,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="../../imgs/Relatos de um Gato Viajante.jpg" alt="Livro 4">
    <div class="info">
      <h3>Relatos de um Gato Viajante</h3>
      <p class="price">R$ 33,90</p>
      <div class="stars">★★★☆☆</div>
    </div>
  </div>

  <div class="card">
    <img src="../../imgs/Prisioneiro de Azkaban.jpg" alt="Livro 5">
    <div class="info">
      <h3>Prisioneiro de Azkaban</h3>
      <p class="price">R$ 55,00</p>
      <div class="stars">★★★★★</div>
    </div>
  </div>

  <div class="card">
    <img src="../../imgs/Dom Casmurro.jpg" alt="Livro 6">
    <div class="info">
      <h3>Dom Casmurro</h3>
      <p class="price">R$ 22,00</p>
      <div class="stars">★★★★☆</div>
    </div>
  </div>
</div>
</body>
</html>