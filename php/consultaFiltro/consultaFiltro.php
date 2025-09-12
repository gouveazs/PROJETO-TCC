<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

include '../conexao.php';
$busca = $_POST['nome']; // ou $_GET['q'] se vier do GET

try {
    // Busca em produtos (livros)
    $stmt = $conn->prepare("
    SELECT p.*, i.imagem
    FROM produto p
    LEFT JOIN imagens i 
        ON i.idproduto = p.idproduto
    WHERE (p.nome LIKE :busca OR p.autor LIKE :busca)
    AND i.idimagens = (
        SELECT idimagens
        FROM imagens
        WHERE idproduto = p.idproduto
        ORDER BY idimagens ASC
        LIMIT 1
    )
    ");
    $stmt->bindValue(':busca', "%$busca%");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

$conn = null;
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

    .card-link {
      text-decoration: none;
      color: inherit;
      display: block; /* faz o link ocupar todo o card */
    }
    .card-link .card {
      cursor: pointer; /* indica que é clicável */
      transition: transform 0.2s;
    }
    .card-link .card:hover {
      transform: scale(1.03); /* efeito visual ao passar o mouse */
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

    /* Estilos para a seção Mais Destaques */
    .mais-destaque-container {
      margin: 20px 0 40px 0;
    }

    .mais-destaque-card {
      display: flex;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .mais-destaque-imagem {
      flex: 1;
      max-width: 40%;
    }

    .mais-destaque-imagem img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .mais-destaque-conteudo {
      flex: 2;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .mais-destaque-conteudo h3 {
      font-size: 1.8rem;
      color: var(--marrom);
      margin-bottom: 15px;
    }

    .mais-destaque-conteudo p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .mais-destaque-preco {
      margin-bottom: 20px;
    }

    .preco-atual {
      font-size: 1.4rem;
      color: var(--verde);
      font-weight: bold;
      margin-right: 15px;
    }

    .preco-antigo {
      font-size: 1.1rem;
      color: #999;
      text-decoration: line-through;
    }

    .btn-destaque {
      display: inline-block;
      background-color: var(--verde);
      color: white;
      padding: 12px 25px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      text-align: center;
      transition: background-color 0.3s;
      max-width: 250px;
    }

    .btn-destaque:hover {
      background-color: #4a5a40;
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
      
      /* Responsividade para Mais Destaques */
      .mais-destaque-card {
        flex-direction: column;
      }
      
      .mais-destaque-imagem {
        max-width: 100%;
        height: 200px;
      }
      
      .mais-destaque-conteudo {
        padding: 20px;
      }
      
      .mais-destaque-conteudo h3 {
        font-size: 1.5rem;
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
        <?php if (!$nome): ?>
          <a href="php/cadastro/cadastroUsuario.php" style="text-decoration: none; color: white;">
            <p class="nome-usuario">Entre ou crie sua conta</p>
          </a>
        <?php else: ?>
          <p class="nome-usuario"><?= htmlspecialchars($nome) ?></p>
        <?php endif; ?>
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
      <?php if (!$nome): ?>
        <li><a href="../login/login.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Entrar na conta</a></li>
        <li><a href="../cadastro/cadastroUsuario.php"><img src="../../imgs/criarconta.png" alt="Criar Conta" style="width:20px; margin-right:10px;"> Criar conta</a></li>
        <li><a href="../cadastro/cadastroVendedor.php"><img src="../../imgs/querovende.png" alt="Quero Vender" style="width:20px; margin-right:10px;"> Quero vender</a></li>
        <li><a href="../login/loginVendedor.php"><img src="../../imgs/entrarconta.png" alt="Entrar" style="width:20px; margin-right:10px;"> Painel do Livreiro</a></li>
      <?php else: ?>
        <li><a href="../perfil-usuario/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
      <?php endif; ?>

      <?php if ($nome === 'adm'): ?>
        <li><a href="../consulta/consulta.php"><img src="../../imgs/explorar.png" alt="Consulta" style="width:20px; margin-right:10px;"> Consulta</a></li>
        <li><a href="../consultaFiltro/busca.php"><img src="../../imgs/explorar.png" alt="Consulta Nome" style="width:20px; margin-right:10px;"> Consulta por Nome</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;"> Cadastrar Produto</a></li>
      <?php endif; ?>

      <?php if ($nome): ?>
        <li><a href="../login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>

</div>

<div class="topbar">
  <h1>Entre Linhas - Sebo Moderna</h1>
  <form class="search-form" action="php/consultaFiltro/consultaFiltro.php" method="POST">
    <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
    <input type="submit" value="Buscar">
  </form>
</div>

<div class="main">

<br><br><br>

<!-- produtos do banco -->
<div class="section-header">
    <h2>Resultados para sua busca: <?= htmlspecialchars($busca) ?></h2>
    <a href="#" class="ver-mais">Ver mais</a>
</div>

<div class="cards cards-novidades">
  <?php if (empty($produtos)): ?>
    <p style="font-size: 1.2em; color: #a00; margin: 30px;">
      Sem resultados para "<?= htmlspecialchars($busca) ?>".
    </p>
  <?php else: ?>
    <?php foreach ($produtos as $produto): ?>
      <a href="../produto/pagiproduto.php?id=<?= $produto['idproduto'] ?>" class="card-link">
        <div class="card">
          <?php if (!empty($produto['imagem'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
          <?php else: ?>
            <img src="imgs/usuario.jpg" alt="Foto de Perfil">
          <?php endif; ?>
          <div class="info">
            <h3><?= htmlspecialchars($produto['nome']) ?></h3>
            <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
            <div class="stars">★★★★★</div>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</div>

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>