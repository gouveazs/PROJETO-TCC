<?php
session_start();
$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil-vendedor']) ? $_SESSION['foto_de_perfil-vendedor'] : null;

if (!isset($_SESSION['nome_vendedor'])) {
  header('Location: ../login/loginVendedor.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Produto</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
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

    .content-area {
      margin-left: 100px;
      width: calc(100% - 250px);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 40px 20px;
      flex-direction: column;
    }

    main.conteudo {
      width: 100%;
      max-width: 700px;
      background-color: var(--branco);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 40px 30px;
    }

    main.conteudo h1 {
      margin-bottom: 25px;
      text-align: center;
      color: var(--verde);
      font-weight: 700;
    }

    form {
      width: 100%;
    }

    .form-group {
      display: flex;
      align-items: center;
      margin-bottom: 18px;
    }

    .form-group label {
      width: 180px;
      font-weight: 600;
      color: var(--marrom);
      text-align: right;
      margin-right: 15px;
      user-select: none;
    }

    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="date"],
    .form-group textarea,
    .form-group input[type="file"] {
      flex-grow: 1;
      padding: 8px 12px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: inherit;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--verde);
      box-shadow: 0 0 5px var(--verde);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 14px;
      background-color: var(--verde);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1.15rem;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 20px;
    }

    input[type="submit"]:hover {
      background-color: #48603b;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .content-area {
        margin-left: 200px;
        width: calc(100% - 200px);
      }
    }

    @media (max-width: 576px) {
      .sidebar {
        display: none;
      }
      .content-area {
        margin-left: 0;
        width: 100%;
      }

      .form-group {
        flex-direction: column;
        align-items: flex-start;
      }

      .form-group label {
        width: 100%;
        text-align: left;
        margin-bottom: 6px;
      }
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
        <li><a href="../painel-livreiro/painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="../painel-livreiro/vendas.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Vendas e Anúncios</a></li>
        <li><a href="../painel-livreiro/rendimento.php"><img src="../../imgs/explorar.png.png" alt="Rendimento" style="width:20px; margin-right:10px;"> Rendimento</a></li>
        <li><a href="cadastroProduto.php"><img src="../../imgs/explorar.png.png" alt="Cadastro" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome_vendedor): ?>
          <li><a href="php/login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
          <li><a href="php/login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
        <?php else: ?>
          <li><a href="../painel-livreiro/minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Editar informações</a></li>
          <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
    <div class="content-area">
    <main class="conteudo">
        <h1>Cadastro de Produto</h1>
        <form action="../insercao/insercaoProduto.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome do produto:</label>
            <input type="text" id="nome" name="nome" required>
        </div>

        <div class="form-group">
            <label for="numero_paginas">Número de páginas:</label>
            <input type="number" id="numero_paginas" name="numero_paginas" min="1" required>
        </div>

        <div class="form-group">
            <label for="editora">Editora:</label>
            <input type="text" id="editora" name="editora" required>
        </div>

        <div class="form-group">
            <label for="autor">Autor:</label>
            <input type="text" id="autor" name="autor" required>
        </div>

        <div class="form-group">
            <label for="classificacao_idade">Classificação etária:</label>
            <input type="number" id="classificacao_idade" name="classificacao_idade" min="0" required>
        </div>

        <div class="form-group">
            <label for="data_publicacao">Data de publicação:</label>
            <input type="date" id="data_publicacao" name="data_publicacao" required>
        </div>

        <div class="form-group">
            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="quantidade">Quantidade em estoque:</label>
            <input type="number" id="quantidade" name="quantidade" min="0" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem do produto:</label>
            <input type="file" id="imagem" name="imagens[]" accept="image/*" multiple required>
        </div>

        <input type="submit" value="Cadastrar">
        </form>
    </main>
    </div>
</body>
</html>