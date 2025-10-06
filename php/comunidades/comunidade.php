<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

include '../conexao.php';
$result = $conn->query("SELECT idcomunidades, nome, descricao, imagem FROM comunidades ORDER BY criada_em DESC");

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comunidades - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #f4f1ee;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: var(--background);
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
      background-color: #5a4226; /* marrom */
      padding: 10px 20px;
      position: fixed;
      top: 0;
      left: 250px; /* respeita a sidebar */
      right: 0;
      height: 70px;
      z-index: 1000;
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
      border-radius: 30px 0 0 30px; /* arredondado à esquerda */
      outline: none;
      width: 300px; /* campo maior */
      font-size: 0.9rem;
      margin: 0;
    }

    .search-form input[type="submit"] {
      padding: 10px 15px;
      border: none;
      background-color: #6f8562; /* verde escuro */
      color: #fff;
      font-weight: none;
      border-radius: 0 30px 30px 0; /* arredondado à direita */
      cursor: pointer;
      margin: 0;
      width: 90px; /* botão mais estreito */
    }

    .search-form input[type="submit"]:hover {
      background-color: #6f8562;
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

    .topbar h1 {
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .main {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      margin-top: 70px;
    }

    .section-header h2 {
      color: var(--verde);
    }

    .categorias-barra {
      margin-left: 250px;
      margin-top: 70px;
      background-color: #9a8c7c;
      padding: 10px 40px;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .categorias-barra a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .categorias-barra a:hover {
      background-color: var(--marrom);
    }

    .content {
      margin-left: 250px;
      margin-top: 130px;
      padding: 30px;
    }

    .content h2 {
      font-size: 2rem;
      color: var(--marrom);
      margin-bottom: 10px;
    }

    .content p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #333;
    }

    .comunidade-box {
      display: flex;
      background-color: var(--marrom);
      border-radius: 10px;
      padding: 20px;
      color: white;
      margin-bottom: 30px;
      align-items: center;
    }

    .comunidade-box {
      display: flex;
      background-color: var(--marrom);
      border-radius: 10px;
      padding: 20px;
      color: white; /* textos brancos */
      margin-bottom: 30px;
      align-items: flex-start; /* textos mais para cima */
    }

    .comunidade-box img {
      width: 180px;
      height: 130px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }

    .comunidade-box .descricao h3 {
      font-size: 1.2rem;
      margin-bottom: 8px;
      color: #fff; /* título branco */
    }

    .comunidade-box .descricao p {
      font-size: 1rem;
      line-height: 1.4;
      color: #fff; /* descrição branca */
      margin-top: 0; /* tira espaço extra acima */
    }

    .footer {
      margin-left: 250px;
      background-color: var(--marrom);
      color: white;
      text-align: center;
      padding: 15px;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      .topbar, .categorias-barra, .content, .footer {
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
        <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
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
          <li><a href="../php/perfil/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
          <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <!-- Topo -->
  <div class="topbar">
    <div class="topbar-left">
    <img src="../../imgs/logotipo.png" alt="Entre Linhas" class="logo">
        <h1>Entre Linhas - Comunidades e Chats Online</h1>
    </div>
    <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
</div>
  <!-- Barra de categorias -->
  <div class="categorias-barra">
    <a href="#">Terror</a>
    <a href="#">Suspense</a>
    <a href="#">Romance</a>
    <a href="#">Fantasia</a>
    <a href="#">Biográfico</a>
    <a href="#">Ficção Científica</a>
    <a href="#">Infantil</a>
    <a href="#">Ficção Literária</a>

    <a href="criar_comunidade.php">Criar Comunidades</a>
  </div>

  <!-- Conteúdo -->
  <div class="content">
     <!-- Conteúdo Principal -->
    <div class="main-content">
      <div class="welcome-section">
        <h2>Bem-vindo às Comunidades</h2>
        <p>Conecte-se com outros leitores, compartilhe suas opiniões e descubra novos livros através das nossas comunidades temáticas.</p>
      </div>
      
      <div class="comunidade-box">
      <img src="../../imgs/iconromance.jpg" alt="Ficção Científica">
      <div class="descricao">
        <h3>Descrição</h3>
        <p>
          Neste canto digital onde o tempo dobra e as realidades se entrelaçam, mentes inquietas se reúnem para tecer o futuro com palavras. <br><br>
          Entre mensagens que viajam mais rápido que a luz, exploramos juntos os enigmas dos multiversos, os segredos das civilizações estelares e os dilemas éticos das inteligências artificiais.
        </p>
      </div>
    </div>

    <?php while ($com = $result->fetch(PDO::FETCH_ASSOC)): ?>
      <div class="comunidade-box">
      <img src="data:image/jpeg;base64,<?= base64_encode($com['imagem']) ?>" alt="<?= htmlspecialchars($com['nome']) ?>">
        <div class="descricao">
          <h3><?= htmlspecialchars($com['nome']) ?></h3>
          <p>
            <?= nl2br(htmlspecialchars($com['descricao'])) ?>
          </p>
          <a href="ver_comunidade.php?id=<?= $com['idcomunidades'] ?>">Entrar</a>
        </div>
      </div>
    <?php endwhile; ?>
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
</body>
</html>
