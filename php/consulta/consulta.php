<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

//produtos
include '../conexao.php';
$stmt = $conn->prepare("SELECT * FROM produto");;
$stmt = $conn->prepare("
    SELECT produto.*, vendedor.nome_completo
    FROM produto
    JOIN vendedor ON produto.idvendedor = vendedor.idvendedor
");
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Consulta Geral - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
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
        <li><a href="../../index.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
        <li><a href="../produto/pagiproduto.php"><img src="../../imgs/destaque.png" alt="Destaques" style="width:20px; margin-right:10px;"> Destaques</a></li>
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
          <li><a href="php/perfil/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
          <li><a href="php/login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Consulta Geral - Entre Linhas</h1>
    <form action="php/consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
  </div>

  <div class="main">
  <!-- Novidades -->
  <br><br>
  <h1>Consulta de usuários</h1>
  <?php
      include '../conexao.php';
      $stmt = $conn->query("SELECT * FROM usuario");
      echo '<link rel="stylesheet" href="consulta.css">';
      echo '<table border="1">';
          echo "<tr>";
              echo "<th>Código</th>";
              echo "<th>Nome</th>";
              echo "<th>Email</th>";
              echo "<th>Senha</th>";
              echo "<th>Foto</th>";
          echo "</tr>"; 
      while ($row = $stmt->fetch()) {      
          echo "<tr>";
              echo "<td>".$row['idusuario']."</td>";
              echo "<td>".$row['nome']."</td>";
              echo "<td>".$row['email']."</td>";
              echo "<td>".$row['senha']."</td>";
              if (!empty($row['foto_de_perfil'])) {
              $imgData = base64_encode($row['foto_de_perfil']);
              echo '<td><img src="data:image/jpeg;base64,' . $imgData . '" width="100" height="auto"/></td>';
              } else {
                  echo "<td>Sem imagem</td>";
              }
          echo "</tr>";          
          }
      echo '</table>';
  ?>
  <h1>Consulta de vendedores</h1>
  <?php  
      include '../conexao.php';
      $stmt = $conn->query("SELECT * FROM vendedor");
      echo '';
      echo '<table border="1">';
          echo "<tr>";
              echo "<th>IMAGEM</th>";
              echo "<th>Código</th>";
              echo "<th>Nome</th>";
              echo "<th>DATA DE NASCIMENTO</th>";
              echo "<th>EMAIL</th>";
              echo "<th>SENHA</th>";
              echo "<th>CPF</th>";
              echo "<th>CNPJ</th>";
          echo "</tr>"; 
      while ($row = $stmt->fetch()) {      
          echo "<tr>";
          if (!empty($row['foto_de_perfil'])) {
              $imgData = base64_encode($row['foto_de_perfil']);
              echo '<td><img src="data:image/jpeg;base64,' . $imgData . '" width="100" height="auto"/></td>';
          } else {
              echo "<td>Sem imagem</td>";
          }
              echo "<td>".$row['idvendedor']."</td>";
              echo "<td>".$row['nome_completo']."</td>";
              echo "<td>".$row['data_nascimento']."</td>";
              echo "<td>".$row['email']."</td>";
              echo "<td>".$row['senha']."</td>";
              echo "<td>".$row['cpf']."</td>";
              echo "<td>".$row['cnpj']."</td>";
          echo "</tr>";          
          }
      echo '</table>';  
  ?>

  <h1>Consulta de produto</h1>
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

<h1>Consulta de comunidades</h1>
  <?php  
      include '../conexao.php';
      $stmt = $conn->query("SELECT * FROM comunidades");
      echo '<table border="1">';
          echo "<tr>";
              echo "<th>IMAGEM</th>";
              echo "<th>Código</th>";
              echo "<th>Nome</th>";
              echo "<th>Descrição</th>";
          echo "</tr>"; 

      while ($row = $stmt->fetch()) {
          echo "<tr>";

          // Verifica se há imagem e converte para base64
          if (!empty($row['imagem'])) {
              $imgData = base64_encode($row['imagem']);
              echo '<td><img src="data:image/jpeg;base64,' . $imgData . '" width="100" height="auto"/></td>';
          } else {
              echo "<td>Sem imagem</td>";
          }

              echo "<td>".$row['id']."</td>";
              echo "<td>".htmlspecialchars($row['nome'])."</td>";
              echo "<td>".$row['descricao']."</td>";
          echo "</tr>";          
      }
      echo '</table>';
  ?>

  </div>
</div>  

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>