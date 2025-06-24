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
      width: 250px; height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 20px;
    }

    .sidebar .logo {
      width: 100%;
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar .logo img {
      width: 80px; height: 80px; border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
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
      position: relative; /* NÃO FIXO */
      margin-left: 250px;
      margin-top: 70px; /* abaixo da topbar */
      overflow: hidden;
    }

    .banner img {
      width: 100%;
      height: 300px; /* altura ajustável */
      object-fit: cover;
      display: block;
      margin-bottom: 20px; /* espaçamento entre imagens se tiver várias */
    }

    .main {
      flex: 1;
      margin-left: 250px;
      padding: 30px;
      margin-top: 20px; /* já compensado pelo banner */
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
      <img src="<?= $foto_de_perfil ? htmlspecialchars($foto_de_perfil) : 'imgs/usuario.jpg' ?>" alt="Foto de Perfil" >
      <p><?= $nome ? htmlspecialchars($nome) : 'Entre ou crie sua conta'; ?></p>
      <p><?= $tipo ? htmlspecialchars($tipo) : 'Usuário'; ?></p>
    </div>
    <nav>
      <ul class="menu">
        <li><a href="#"><img src="imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;"> Início</a></li>
        <li><a href="explorar.php"><img src="imgs/explorar.png" alt="Explorar" style="width:20px; margin-right:10px;"> Explorar</a></li>
        <li><a href="#"><img src="imgs/comunidades.png" alt="Comunidades" style="width:20px; margin-right:10px;"> Comunidades</a></li>
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
          <li><a href="login/logout.php"><img src="imgs/logout.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

  <div class="topbar">
    <h1>Entre Linhas - Livraria Moderna</h1>
    <input type="text" placeholder="Pesquisar livros, autores...">
  </div>

  <div class="banner">
    <img src="imgs/Banner.png" alt="Banner 1">
  </div>

  <div class="main">
  <!-- Novidades -->
  <div class="section-header">
    <h2>Novidades</h2>
    <a href="#" class="ver-mais">Ver mais</a>
  </div>
  <div class="cards cards-novidades">
    <div class="card">
      <img src="imgs/Daisy Jone & The Six.jpg" alt="Livro 1">
      <div class="info">
        <h3>Daisy Jone & The Six</h3>
        <p class="price">R$ 44,90</p>
        <div class="stars">★★★★☆</div>
      </div>
    </div>
    <div class="card">
      <img src="imgs/A Menina que Roubava Livros.jpg" alt="Livro 2">
      <div class="info">
        <h3>A Menina que Roubava Livros</h3>
        <p class="price">R$ 39,50</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>
    <div class="card">
      <img src="imgs/Extraordinário.jpg" alt="Livro 3">
      <div class="info">
        <h3>Extraordinário</h3>
        <p class="price">R$ 28,00</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>
    <div class="card">
      <img src="imgs/Relatos de um Gato Viajante.jpg" alt="Livro 4">
      <div class="info">
        <h3>Relatos de um Gato Viajante</h3>
        <p class="price">R$ 33,90</p>
        <div class="stars">★★★☆☆</div>
      </div>
    </div>
    <div class="card">
      <img src="imgs/Prisioneiro de Azkaban.jpg" alt="Livro 5">
      <div class="info">
        <h3>Prisioneiro de Azkaban</h3>
        <p class="price">R$ 55,00</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>
    <div class="card">
      <img src="imgs/Dom Casmurro.jpg" alt="Livro 6">
      <div class="info">
        <h3>Dom Casmurro</h3>
        <p class="price">R$ 22,00</p>
        <div class="stars">★★★★☆</div>
      </div>
    </div>
  </div>

  <!-- Recomendações -->
  <div class="section-header" style="margin-top: 50px;">
    <h2>Recomendações</h2>
    <a href="#" class="ver-mais">Ver mais</a>
  </div>
  <div class="cards cards-recomendacoes">

    <div class="card">
      <img src="imgs/Orgulho e Preconceito.jpg" alt="Livro 7">
      <div class="info">
        <h3>Orgulho e Preconceito</h3>
        <p class="price">R$ 34,90</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/1984.jpg" alt="Livro 8">
      <div class="info">
        <h3>1984</h3>
        <p class="price">R$ 29,99</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/O Pequeno Príncipe.jpg" alt="Livro 9">
      <div class="info">
        <h3>O Pequeno Príncipe</h3>
        <p class="price">R$ 19,90</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/Romeu e Julieta.jpg" alt="Livro 10">
      <div class="info">
        <h3>Romeu e Julieta</h3>
        <p class="price">R$ 24,00</p>
        <div class="stars">★★★☆☆</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/Senhor dos Anéis.jpg" alt="Livro 11">
      <div class="info">
        <h3>Senhor dos Anéis</h3>
        <p class="price">R$ 59,90</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/A Culpa é das Estrelas.jpg" alt="Livro 12">
      <div class="info">
        <h3>A Culpa é das Estrelas</h3>
        <p class="price">R$ 35,00</p>
        <div class="stars">★★★★☆</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/O Hobbit.jpg" alt="Livro 13">
      <div class="info">
        <h3>O Hobbit</h3>
        <p class="price">R$ 39,90</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/Cem Anos de Solidão.jpg" alt="Livro 14">
      <div class="info">
        <h3>Cem Anos de Solidão</h3>
        <p class="price">R$ 42,00</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/A Revolução dos Bichos.jpg" alt="Livro 15">
      <div class="info">
        <h3>A Revolução dos Bichos</h3>
        <p class="price">R$ 27,50</p>
        <div class="stars">★★★★☆</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/O Conto da Aia.jpg" alt="Livro 16">
      <div class="info">
        <h3>O Conto da Aia</h3>
        <p class="price">R$ 36,00</p>
        <div class="stars">★★★★☆</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/Amor e Azeitonas.jpg" alt="Livro 17">
      <div class="info">
        <h3>Amor e Azeitonas</h3>
        <p class="price">R$ 22,90</p>
        <div class="stars">★★★☆☆</div>
      </div>
    </div>

    <div class="card">
      <img src="imgs/As vantagens de ser invisivel.jpg" alt="Livro 18">
      <div class="info">
        <h3>As Vantagens de Ser Invisível</h3>
        <p class="price">R$ 38,00</p>
        <div class="stars">★★★★★</div>
      </div>
    </div>
  </div>

  <div class="section-header">
    <h2>Ofertas Recem Adicionadas</h2>
    <a href="#" class="ver-mais">Ver mais</a>
  </div>
  <div class="cards cards-novidades">
  <div class="produtos-container">
    <?php foreach ($produtos as $produto): ?>
      <div class="card">
      <img src="<?= htmlspecialchars($produto['imagem'] ?? 'imgs/usuario.jpg') ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
        <div class="info">
          <h3><?= htmlspecialchars($produto['nome']) ?></h3>
          <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
          <div class="stars">★★★★★</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

    </div>
</div>  

  <div class="footer">
    &copy; 2025 Entre Linhas - Todos os direitos reservados.
  </div>
</body>
</html>
