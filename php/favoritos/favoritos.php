<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../login/login.php');
    exit;
}

include '../conexao.php';
$idusuario = (int)$_SESSION['idusuario'];
$stmt = $conn->prepare("
    SELECT 
        p.idproduto,
        p.nome,
        p.preco,
        (SELECT i.imagem 
           FROM imagens i 
          WHERE i.idproduto = p.idproduto 
          ORDER BY i.idimagens ASC 
          LIMIT 1) AS imagem
    FROM favoritos f
    JOIN produto p ON f.idproduto = p.idproduto
    WHERE f.idusuario = :idusuario
");
$stmt->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
$stmt->execute();
$favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Favoritos - Entre Linhas</title>
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

.search-form input {
    padding: 8px 12px;
    border: none;
    border-radius: 20px 0 0 20px;
    outline: none;
    width: 220px;
}

.search-form button {
    padding: 8px 16px;
    border: none;
    background-color: #465b3a; /* verde escuro */
    color: #fff;
    font-weight: bold;
    border-radius: 0 20px 20px 0;
    cursor: pointer;
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
      <li><a href="../../index.php"><img src="../../imgs/inicio.png" style="width:20px;"> Início</a></li>
      <li><a href="../comunidades/comunidade.php"><img src="../../imgs/comunidades.png" style="width:20px;"> Comunidades</a></li>
      <li><a href="../destaques/destaques.php"><img src="../../imgs/destaque.png" style="width:20px;"> Destaques</a></li>
      <li><a href="favoritos.php"><img src="../../imgs/favoritos.png" style="width:20px;"> Favoritos</a></li>
      <li><a href="../carrinho/carrinho.php"><img src="../../imgs/carrinho.png" style="width:20px;"> Carrinho</a></li>
    </ul>

    <h3>Conta</h3>
    <ul class="account">
      <?php if (!$nome): ?>
        <li><a href="../login/login.php">Entrar na conta</a></li>
        <li><a href="../cadastro/cadastroUsuario.php">Criar conta</a></li>
        <li><a href="../cadastro/cadastroVendedor.php">Quero vender</a></li>
        <li><a href="../login/loginVendedor.php">Painel do Livreiro</a></li>
      <?php else: ?>
        <li><a href="../perfil_usuario/ver_perfil.php"><img src="../../imgs/criarconta.png" style="width:20px;">Ver perfil</a></li>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" style="width:20px;">Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<div class="topbar">
    <div class="topbar-left">
    <img src="../../imgs/logotipo.png" alt="Entre Linhas" class="logo">
        <h1>Entre Linhas - Favoritos</h1>
    </div>
    <form class="search-form">
        <input type="text" placeholder="Pesquisar livros, autores...">
        <button type="submit">Buscar</button>
    </form>
</div>


<div class="main">
  <div class="section-header">
    <h2>Seus Livros Favoritos</h2> 
  </div>

  <div class="cards">
    <?php if(count($favoritos) > 0): ?>
      <?php foreach($favoritos as $produto): ?>
        <a href="../produto/pagiproduto.php?id=<?= $produto['idproduto'] ?>" class="card-link">
          <div class="card">
            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
            <div class="info">
              <h3><?= htmlspecialchars($produto['nome']) ?></h3>
              <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
              <div class="stars">★★★★☆</div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Só mensagem + botões, SEM repetir o título -->
      <p style="margin: 10px 0 20px; font-size: 1rem; color: #333;">
        Você ainda não adicionou nenhum produto aos favoritos.
      </p>

      <div style="display: flex; gap: 30px; margin-top: 10px; flex-wrap: wrap; justify-content: flex-start; align-items: flex-start;">
        <a href="../destaques/destaques.php" 
          style="display: flex; flex-direction: column; align-items: center; justify-content: center;
                 width: 150px; height: 150px; background-color: #5a4224; color: #fff; 
                 font-weight: bold; font-size: 0.95rem; border-radius: 10px; 
                 text-decoration: none; gap: 8px;">
          <img src="../../imgs/book.png" alt="Adicionar Livro" style="width:50px; height:50px;">
          Adicionar Livro
        </a>

        <a href="../carrinho/carrinho.php" 
          style="display: flex; flex-direction: column; align-items: center; justify-content: center;
                 width: 150px; height: 150px; background-color: #5a4224; color: #fff; 
                 font-weight: bold; font-size: 0.95rem; border-radius: 10px; 
                 text-decoration: none; gap: 8px;">
          <img src="../../imgs/listades.png" alt="Ir Para Carrinho" style="width:50px; height:50px;">
          Ir para Carrinho
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>


<div class="footer">
&copy; 2025 Entre Linhas - Todos os direitos reservados.
</div>

</body>
</html>
