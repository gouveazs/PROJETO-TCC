<?php
  session_start();
  $adm = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
  $foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

  if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../login/login.php');
    exit;
  }

  include '../conexao.php';

  $stmt_produtos = $conn->query("
        SELECT p.*, 
        v.nome_completo AS nome_vendedor,
        c.nome AS nome_categoria,
        (SELECT i.imagem
          FROM imagens i 
         WHERE i.idproduto = p.idproduto 
         LIMIT 1) AS imagem_produto
        FROM produto p
        JOIN vendedor v ON p.idvendedor = v.idvendedor
        JOIN categoria c ON p.idcategoria = c.idcategoria;
    ");
    $produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consulta de Produtos - Painel do Adm</title>
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

    .table-responsive {
      width: 100%;
      overflow-x: auto;
    }
  
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      font-size: 14px;
      min-width: 1200px; /* garante que colunas não fiquem muito estreitas */
      border-collapse: collapse;
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
          <p class="nome-usuario"><?= $adm ? htmlspecialchars($adm) : 'Entre ou crie sua conta'; ?></p>
        </div>
    </div>

    <nav>
      <ul class="menu">
      <li><a href="adm.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="consulta-usuarios.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Usuários</a></li>
        <li><a href="consulta-vendedores.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Vendedores</a></li>
        <li><a href="consulta-produtos.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Produtos</a></li>
        <li><a href="rendimento.php"><img src="../../imgs/explorar.png.png" alt="Rendimento" style="width:20px; margin-right:10px;"> Buscador 2000</a></li>
        <li><a href="#"><img src="../../imgs/explorar.png.png" alt="Cadastro" style="width:20px; margin-right:10px;"> Sei la</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <li><a href="minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Editar informações</a></li>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Painel do Administrador</h1>
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
        <h1>Bem-vindo, <?= $adm ? htmlspecialchars($adm) : 'Adm'; ?></h1>
        <p>Acompanhe o desempenho do site fodão</p>
      </div>
    </div>
    
    <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
    
    <div class="card">
    <h2>Consulta de Produtos</h2>
        <div class="table-responsive">
        <table class="table">
            <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Nome Vendedor</th>
            <th>Categoria</th>
            <th>Nome</th>
            <th>N° de Páginas</th>
            <th>Editora</th>
            <th>Class. Etária</th>
            <th>Data Publicação</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Autor</th>
            <th>ISBN</th>
            <th>Dimensões</th>
            <th>Idioma</th>
            <th>Estado</th>
            <th>Descrição</th>
            <th>Editar</th>
            <th>Expurgar</th>
          </tr>

            <?php if ($produtos): ?>
            <?php foreach ($produtos as $produto): ?>
              <tr>
                <td><?= htmlspecialchars($produto['idproduto']) ?></td>
                <td>
                    <?php if (!empty($produto['imagem_produto'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem_produto']) ?>" width="50" height="50" alt="Produto">
                    <?php else: ?>
                        Sem imagem
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($produto['nome_vendedor']) ?></td>
                <td><?= htmlspecialchars($produto['nome_categoria']) ?></td>
                <td><?= htmlspecialchars($produto['nome'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['numero_paginas'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['editora'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['classificacao_etaria'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['data_publicacao'] ?? 'Vazio') ?></td>
                <td>R$ <?= htmlspecialchars(number_format($produto['preco'] ?? 0, 2, ',', '.')) ?></td>
                <td><?= htmlspecialchars($produto['quantidade'] ?? 0) ?></td>
                <td><?= htmlspecialchars($produto['autor'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['isbn'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['dimensoes'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['idioma'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['estado_livro'] ?? 'Vazio') ?></td>
                <td><?= htmlspecialchars($produto['descricao'] ?? 'Vazio') ?></td>
                <td>
                    <button><a style="text-decoration: none;" href="editar_produto.php?id=<?= $produto['idproduto'] ?>">📝</a></button>
                </td>
                <td>
                    <button><a style="text-decoration: none;" href="desativar_produto.php?id=<?= $produto['idproduto'] ?>">❌</a></button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="19">Nenhum produto cadastrado.</td>
            </tr>
            <?php endif; ?>
        </table>
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