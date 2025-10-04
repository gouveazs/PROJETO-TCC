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
    z-index: 1100;
  }

  .sidebar::-webkit-scrollbar { width: 6px; }
  .sidebar::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }

  .sidebar .logo { display: flex; align-items: center; justify-content: flex-start; width: 100%; padding: 0 20px; margin-bottom: 20px; }
  .sidebar .logo img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 15px; }
  .sidebar .user-info { display: flex; flex-direction: column; line-height: 1.2; }
  .sidebar .user-info .nome-usuario { font-weight: bold; font-size: 0.95rem; color: #fff; }

  .sidebar nav { width: 100%; padding: 0 20px; }
  .sidebar nav h3 { margin-top: 20px; margin-bottom: 10px; font-size: 1rem; color: #ddd; }
  .sidebar nav ul { list-style: none; padding: 0; margin: 0 0 10px 0; width: 100%; }
  .sidebar nav ul li { width: 100%; margin-bottom: 10px; }
  .sidebar nav ul li a { color: #fff; text-decoration: none; display: flex; align-items: center; padding: 10px; border-radius: 8px; transition: background 0.3s; }
  .sidebar nav ul li a img { margin-right: 10px; }
  .sidebar nav ul li a:hover { background-color: #6f8562; }

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
    z-index: 1200;
  }

  .topbar-left { display: flex; align-items: center; gap: 15px; }
  .topbar-left .logo { height: 50px; }
  .topbar-left h1 { font-size: 22px; color: #fff; margin: 0; font-weight: bold; }

  .search-form { display: flex; align-items: center; }
  .search-form input[type="text"] { padding: 10px 15px; border: none; border-radius: 30px 0 0 30px; outline: none; width: 300px; font-size: 0.9rem; }
  .search-form input[type="submit"] { padding: 10px 15px; border: none; background-color: #6f8562; color: #fff; border-radius: 0 30px 30px 0; cursor: pointer; width: 90px; }

  .main {
    flex: 1;
    margin-left: 250px;
    padding: 30px;
    margin-top: 70px;
    padding-bottom: 100px; /* espaço para o rodapé fixo */
  }

  .section-header h2 { color: var(--verde); }

  /* GRID e CARDS */
  .cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 25px;
    margin-top: 20px;
    align-items: start;
  }

  .card {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%; /* obrigatório para stretch funcionar */
      box-sizing: border-box;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      align-items: stretch; /* força mesma altura */
    }

     .cards-novidades {
      grid-template-columns: repeat(6, 1fr);
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
      display: -webkit-box;
      -webkit-line-clamp: 2; /* no máximo 2 linhas */
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    .card-link .card {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .card-link .card:hover {
      transform: scale(1.03);
    }

    .card .info .stars {
      color: #f5c518;
    }

  /* === REMOVER SUBLINHADOS DOS LINKS DENTRO DOS CARDS / ÁREA PRINCIPAL ===
     Aplica-se apenas aos links dentro da área principal (.main / .cards)
     para não afetar o menu lateral. */
  .main .cards a,
  .main .cards a:link,
  .main .cards a:visited,
  .main .cards a:hover,
  .main .cards a:active {
    text-decoration: none !important; /* garante que não fique sublinhado */
    color: inherit !important;        /* evita cor roxa de visited */
  }

  /* garantir também pra classe card-link */
  .card-link,
  .card-link:link,
  .card-link:visited,
  .card-link:hover,
  .card-link:active {
    text-decoration: none !important;
    color: inherit !important;
    display: block;
  }

  /* prevenir sublinhado em elementos internos do card */
  .card-link * { text-decoration: none !important; color: inherit !important; }

  /* Ações do card (botões Remover / Adicionar) */
  /* Ações do card (botões Remover / Adicionar) */
.card-actions {
  display: flex;
  justify-content: space-around;
  gap: 10px;
  padding: 10px;
  color: #eee;
  border-top: 1px solid #eee;
  background: #fff;
}

.card-actions a {
  flex: 1;
  text-align: center;
  background-color: var(--verde) !important;
  color: #fff !important;   /* força branco */
  text-decoration: none !important;
  padding: 10px 0;
  border-radius: 8px;
  font-weight: bold;
  font-size: 0.80rem;
  transition: all 0.2s ease-in-out;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card-actions a:hover {
  background-color: #4a5a42 !important;
  color: #fff !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

  /* RODAPÉ (mantive como estava) */
  .footer {
    margin-left: 250px;
    width: calc(100% - 250px);
    background-color: var(--marrom);
    color: #fff;
    text-align: center;
    padding: 15px;
    margin-top: auto;
  }

  /* responsivo */
  @media (max-width: 900px) {
    .cards-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    .topbar { left: 0; }
    .sidebar { transform: translateX(-100%); position: fixed; z-index: 1300; }
    .footer { left: 0; width: 100%; margin-left: 0; }
    .main { margin-left: 0; padding-bottom: 120px; }
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
        <li><a href="../../php/perfil-usuario/ver_perfil.php"><img src="../../imgs/criarconta.png" alt="Perfil" style="width:20px; margin-right:10px;"> Ver perfil</a></li>
        <li><a href="../../php/login/logout.php"><img src="../../imgs/sair.png" alt="Sair" style="width:20px; margin-right:10px;"> Sair</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>

<div class="topbar">
    <div class="topbar-left">
    <img src="../../imgs/logotipo.png" alt="Entre Linhas" class="logo">
        <h1>Entre Linhas - Favoritos</h1>
    </div>
    <form class="search-form" action="../consultaFiltro/consultaFiltro.php" method="POST">
      <input type="text" name="nome" placeholder="Pesquisar livros, autores...">
      <input type="submit" value="Buscar">
    </form>
</div>


<div class="main">
  <div class="section-header">
    <h2>Seus Livros Favoritos</h2> 
  </div>

<div class="cards cards-novidades">
<div class="cards">
  <?php if(count($favoritos) > 0): ?>
    <div class="cards-grid">
      <?php foreach($favoritos as $produto): ?>
        <div class="card">
          <a href="../produto/pagiproduto.php?id=<?= $produto['idproduto'] ?>" class="card-link">
            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" 
                 alt="<?= htmlspecialchars($produto['nome']) ?>">
            <div class="info">
              <h3><?= htmlspecialchars($produto['nome']) ?></h3>
              <p class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
              <div class="stars">★★★★★</div>
            </div>
          </a>
          <div class="card-actions">
            <a href="remover_favorito.php?idproduto=<?= $produto['idproduto'] ?>" 
               onclick="return confirm('Tem certeza que deseja remover este produto dos favoritos?')">Remover</a>
            <a href="#">Adicionar ao carrinho</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
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

<div class="footer">
&copy; 2025 Entre Linhas - Todos os direitos reservados.
</div>

</body>
</html>
