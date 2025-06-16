<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Entre Linhas - Livraria Moderna</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <link rel="stylesheet" href="css/estilo02.css"> <!-- CSS separado -->
</head>
<body>

  <div class="sidebar">
    <div class="profile">
      <img src="imgs/imagem-do-usuario-com-fundo-preto.png" alt="Avatar">
      <div>
          <?php if ($nome): ?>
              <span class="item-description"><?php echo htmlspecialchars($nome); ?></span>
              <span class="item-description">Usuário</span>
          <?php else: ?>
              <span class="item-description">Usuário</span>
              <span class="item-description">Entre ou crie sua conta</span>
          <?php endif; ?>
      </div>
    </div>

    <ul>
      <li><a href="#"><i class="fas fa-home"></i> Início</a></li>
      <li><a href="#"><i class="fas fa-compass"></i> Explorar</a></li>
      <li><a href="#"><i class="fas fa-book"></i> Minha Estante</a></li>
      <li><a href="#"><i class="fas fa-heart"></i> Favoritos</a></li>
      <li><a href="#"><i class="fas fa-history"></i> Histórico</a></li>
    </ul>

    <h2>Conta</h2>
    <ul>
      <?php if (!$nome): ?>
        <li><a href="login/login.php"><i class="fas fa-sign-in-alt"></i> Entrar na conta</a></li>
        <li><a href="php/cadastro/cadastroUsuario.php"><i class="fas fa-user-plus"></i> Criar conta</a></li>
        <li><a href="php/cadastro/cadastroVendedor.php"><i class="fas fa-cogs"></i> Quero vender</a></li>
      <?php else: ?>
                <li><a href="php/perfil/ver_perfil.php">Ver perfil</a></li>
      <?php endif; ?>

      <?php if ($nome === 'adm'): ?>
        <li><a href="php/consulta/consulta.php"><i class="fas fa-search"></i> Consulta</a></li>
        <li><a href="php/consultaFiltro/busca.php"><i class="fas fa-filter"></i> Consulta por Nome</a></li>
        <li><a href="php/cadastro/cadastroProduto.php"><i class="fas fa-plus"></i> Cadastrar Produto</a></li>
      <?php endif; ?>
  </ul>


    <h2><a href="login/logout.php" style="color: #ff7675;">Sair</a></h2>
  </div>

  <div class="main">
    <div class="topbar">
      <h2>Entre Linhas - Livraria Moderna</h2>
      <div class="search-box">
        <input type="text" placeholder="Pesquisar livros, autores...">
      </div>
    </div>

    <div class="content">
      <div class="section">
        <h3>Novidades</h3>
        <div class="cards">
          <div class="card">
            <img src="imgs/capa.jpg" alt="Livro 1">
            <div class="card-content">
              <h4>O Nome do Vento</h4>
              <p>★★★★★</p>
            </div>
          </div>
          <div class="card">
            <img src="imgs/capa.jpg" alt="Livro 2">
            <div class="card-content">
              <h4>A Marcela</h4>
              <p>★★★★☆</p>
            </div>
          </div>
        </div>
      </div>

      <div class="section">
        <h3>Recomendações</h3>
        <div class="cards">
          <div class="card">
            <img src="imgs/capa.jpg" alt="Livro 3">
            <div class="card-content">
              <h4>1984</h4>
              <p>★★★★★</p>
            </div>
          </div>
          <div class="card">
            <img src="imgs/capa.jpg" alt="Livro 4">
            <div class="card-content">
              <h4>Orgulho e Preconceito</h4>
              <p>★★★★☆</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer">
      Todos os direitos reservados - 2025
    </div>
  </div>

</body>
</html>
