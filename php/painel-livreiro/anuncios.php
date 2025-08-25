<?php
session_start();
$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil-vendedor']) ? $_SESSION['foto_de_perfil-vendedor'] : null;

if (!isset($_SESSION['nome_vendedor'])) {
  header('Location: ../login/loginVendedor.php');
  exit;
}

//produtos
include '../conexao.php';
$stmt = $conn->prepare("
    SELECT p.*, v.nome_completo
    FROM produto p
    JOIN cadastro_vendedor v ON p.idvendedor = v.idvendedor
    WHERE v.nome_completo = :nome_vendedor
");
$stmt->bindParam(':nome_vendedor', $nome_vendedor, PDO::PARAM_STR);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel do Livreiro</title>
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

    /* Tabela */
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      font-size: 14px;
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
          <p class="nome-usuario"><?= $nome_vendedor ? htmlspecialchars($nome_vendedor) : 'Entre ou crie sua conta'; ?></p>
        </div>
    </div>

    <nav>
      <ul class="menu">
        <li><a href="painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="anuncios.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;"> Anúncios</a></li>
        <li><a href="rendimento.php"><img src="../../imgs/explorar.png.png" alt="Rendimento" style="width:20px; margin-right:10px;"> Rendimento</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/explorar.png.png" alt="Cadastro" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      </ul>

      <h3>Conta</h3>
      <ul class="account">
        <?php if (!$nome_vendedor): ?>
          <li><a href="php/login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
          <li><a href="php/cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
          <li><a href="php/cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
          <li><a href="php/login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
        <?php else: ?>
          <li><a href="minhas_informacoes.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Editar informações</a></li>
          <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Painel do Livreiro</h1>
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
        <h1>Bem-vindo, <?= $nome_vendedor ? htmlspecialchars($nome_vendedor) : 'Usuário'; ?></h1>
        <p>Acompanhe seu desempenho como vendedor</p>
      </div>
    </div>
        <hr style="border: 0; height: 1px; background-color: #afafafff;"> <br>
    <?php  
    include '../conexao.php';
    $stmt = $conn->prepare("
      SELECT p.*,
            (
                  SELECT i2.imagem
                  FROM imagens i2
                  WHERE i2.idproduto = p.idproduto
                  ORDER BY i2.idimagens ASC
                  LIMIT 1
            ) AS imagem
      FROM produto p
    ");
    $stmt->execute();
    echo '<table border="1">';
    echo "<tr>
      <th>IMAGEM</th>
      <th>Código</th>
      <th>Nome DO LIVRO</th>
      <th>NÚMERO DE PÁGINAS</th>
      <th>EDITORA</th>
      <th>AUTOR</th>
      <th>CLASSIFICAÇÃO ETÁRIA</th>
      <th>DATA DE PUBLICAÇÃO</th>
      <th>PREÇO</th>
      <th>QUANTIDADE EM ESTOQUE</th>
      <th>DESCRIÇÃO</th>
      <th>ID VENDEDOR</th>
    </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr>";
        if (!empty($row['imagem'])) {
            $imgData = base64_encode($row['imagem']);
            echo '<td><img src="data:image/jpeg;base64,' . $imgData . '" width="50"/></td>';
        } else {
            echo "<td>Sem imagem</td>";
        }
          echo "<td>{$row['idproduto']}</td>";
          echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
          echo "<td>{$row['numero_paginas']}</td>";
          echo "<td>" . htmlspecialchars($row['editora']) . "</td>";
          echo "<td>" . htmlspecialchars($row['autor']) . "</td>";
          echo "<td>{$row['classificacao_etaria']}</td>";
          echo "<td>{$row['data_publicacao']}</td>";
          echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
          echo "<td>{$row['quantidade']}</td>";
          echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
          echo "<td>{$row['idvendedor']}</td>";
        echo "</tr>";
        }
    echo '</table>';
  ?>
  </main>
</body>
</html>