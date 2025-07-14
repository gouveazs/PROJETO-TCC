<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Entre Linhas - Cadastro de Produto</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --marrom: #5a4224;
      --verde: #5a6b50;
      --background: #F4F1EE;
      --branco: #fff;
      --cinza-claro: #ddd;
      --cinza-escuro: #444;
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
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      color: var(--branco);
      display: flex;
      flex-direction: column;
      padding-top: 20px;
      overflow-y: auto;
      z-index: 1000;
    }

    .sidebar .logo {
      padding: 0 20px 20px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      margin-bottom: 15px;
      font-size: 1.7rem;
      font-weight: 700;
      color: var(--branco);
      user-select: none;
    }

    .sidebar nav ul {
      list-style: none;
      padding: 0 20px;
    }

    .sidebar nav ul li {
      margin-bottom: 15px;
    }

    .sidebar nav ul li a {
      color: var(--branco);
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    /* Área principal */
    .content-area {
      margin-left: 250px;
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
      color: var(--branco);
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

    footer.rodape {
      background-color: var(--marrom);
      color: var(--branco);
      text-align: center;
      padding: 15px 20px;
      font-size: 0.9rem;
      width: 100%;
      max-width: 700px;
      border-radius: 0 0 12px 12px;
      margin-top: 20px;
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
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="logo">Entre Linhas</div>
    <nav>
      <ul>
        <?php if (!$nome): ?>
          <li><a href="../../login/login.php"><i class="fas fa-sign-in-alt"></i> Entrar na conta</a></li>
          <li><a href="../../php/cadastro/cadastroUsuario.php"><i class="fas fa-user-plus"></i> Criar conta</a></li>
          <li><a href="../../php/cadastro/cadastroVendedor.php"><i class="fas fa-store"></i> Quero vender</a></li>
        <?php else: ?>
          <li><a href="../../php/perfil/ver_perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
          <li><a href="../../login/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
        <?php endif; ?>

        <?php if ($nome === 'adm'): ?>
          <li><a href="../../php/consulta/consulta.php"><i class="fas fa-search"></i> Consulta</a></li>
          <li><a href="../../php/consultaFiltro/busca.php"><i class="fas fa-search"></i> Consulta por Nome</a></li>
          <li><a href="../../php/cadastro/cadastroProduto.php"><i class="fas fa-plus"></i> Cadastrar Produto</a></li>
        <?php endif; ?>

        <?php if ($tipo === 'Vendedor'): ?>
          <li><a href="../../php/cadastro/cadastroProduto.php"><i class="fas fa-plus"></i> Cadastrar Produto</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </aside>

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
          <input type="file" id="imagem" name="imagem" accept="image/*" required>
        </div>

        <input type="submit" value="Cadastrar">
      </form>
    </main>

    <footer class="rodape">
      Todos os direitos reservados - 2025
    </footer>
  </div>
</body>
</html>
