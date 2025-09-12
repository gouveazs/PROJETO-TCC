<?php
session_start();
$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil-vendedor']) ? $_SESSION['foto_de_perfil-vendedor'] : null;
$id_vendedor = isset($_SESSION['id_vendedor']) ? $_SESSION['id_vendedor'] : null; 

if (!$nome_vendedor) {
    header('Location: ../login/loginVendedor.php');
    exit;
}

include '../conexao.php';
$stmt = $conn->prepare("
   SELECT 
    p.*, 
    v.nome_completo, 
    i.imagem
    FROM produto p
    JOIN vendedor v 
        ON p.idvendedor = v.idvendedor
    LEFT JOIN imagens i 
        ON i.idproduto = p.idproduto 
        AND i.idimagens = (
            SELECT idimagens 
            FROM imagens 
            WHERE idproduto = p.idproduto 
            ORDER BY idimagens ASC 
            LIMIT 1
        )
    WHERE v.nome_completo = :nome_vendedor;
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
  <title>Painel do Livreiro - Meus Anúncios</title>
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
      padding-left: 250px;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: var(--verde);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding-top: 20px;
      overflow-y: auto;
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

    .sidebar nav ul li a:hover {
      background-color: #6f8562;
    }

    .topbar {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
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

    main {
      padding: 30px;
      flex: 1;
    }

    .header {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 30px;
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

    .produtos-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 15px;
    }

    .produto-card {
      width: 250px;
      background: #fff;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: 0.3s;
    }

    .produto-card:hover {
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .produto-img {
      height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f9f9f9;
      border-radius: 8px;
      margin-bottom: 10px;
      overflow: hidden;
    }

    .produto-img img {
      max-height: 100%;
      max-width: 100%;
      object-fit: cover;
      border-radius: 8px;
    }

    .sem-imagem {
      color: #999;
      font-size: 14px;
    }

    .btn-toggle {
      background: #556B2F;
      color: #fff;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      padding: 8px 12px;
      margin-top: 8px;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
    }

    .btn-toggle:hover {
      background: #445522;
    }

    .produto-detalhes {
      display: none;
      margin-top: 10px;
      border-top: 1px solid #ddd;
      padding-top: 8px;
      font-size: 14px;
      text-align: left;
    }

    .produto-actions {
      display: flex;
      flex-direction: column; /* Alinha os botões um embaixo do outro */
      margin-top: 10px;
      gap: 10px; /* Espaçamento entre os botões */
    }

    .btn-action {
        padding: 10px 15px;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
        width: 100%; /* Faz os botões ocuparem toda a largura do card */
    }

    .btn-toggle.mostrar-detalhes {
        background-color: var(--verde); /* Cor da sidebar */
        color: white;
    }

    .btn-toggle.mostrar-detalhes:hover {
        background-color: #4e5c43; /* Cor mais escura */
    }

    .btn-action.editar {
        background-color: var(--verde); /* Cor da sidebar */
        color: white;
    }

    .btn-action.editar:hover {
        background-color: #4e5c43; /* Cor mais escura */
    }

    .btn-action.excluir {
        background-color: var(--marrom); /* Cor da barra de título "Entre Linhas" */
        color: white;
    }

    .btn-action.excluir:hover {
        background-color: #6f4f28; /* Cor mais escura */
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" alt="Foto de Perfil">
      <?php else: ?>
        <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
      <?php endif; ?>
      <div class="user-info">
        <p class="nome-usuario"><?= htmlspecialchars($nome_vendedor); ?></p>
      </div>
    </div>

    <nav>
      <ul class="menu">
        <li><a href="painel_livreiro.php"><img src="../../imgs/inicio.png" alt="Início" style="width:20px; margin-right:10px;">Início</a></li>
        <li><a href="anuncios.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;">Vendas publicadas</a></li>
        <li><a href="rendimento.php"><img src="../../imgs/explorar.png.png" alt="Vendas" style="width:20px; margin-right:10px;">Rendimento</a></li>
        <li><a href="../cadastro/cadastroProduto.php"><img src="../../imgs/explorar.png" alt="Cadastrar Produto" style="width:20px; margin-right:10px;">Cadastrar Produto</a></li>
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

  <div class="topbar">
    <h1>Entre Linhas - Painel do Livreiro</h1>
  </div>

  <main>
  <br><br><br>
  <div class="header">
    <?php if ($foto_de_perfil): ?>
      <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" alt="Foto de Perfil">
    <?php else: ?>
      <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
    <?php endif; ?>
    <div class="header-text">
      <h1>Bem-vindo, <?= htmlspecialchars($nome_vendedor) ?></h1>
      <p>Acompanhe seu desempenho como vendedor</p>
    </div>
  </div>

  <hr style="border: 0; height: 1px; background-color: #afafafff; margin-bottom: 20px;">

  <h2><b>Vendas publicadas</b></h2>

  <div class="produtos-container">
    <?php if (!empty($produtos)): ?>
        <?php foreach ($produtos as $index => $produto): ?>
            <div class="produto-card">
                <div class="produto-img">
                    <?php if (!empty($produto['imagem'])): ?>
                      <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                    <?php else: ?>
                      <img src="../../imgs/usuario.jpg" alt="Foto de Perfil">
                    <?php endif; ?>
                </div>

                <button class="btn-toggle mostrar-detalhes" onclick="toggleDetalhes('detalhes-<?= $index ?>')">
                    Mostrar detalhes
                </button>

                <div id="detalhes-<?= $index ?>" class="produto-detalhes">
                    <p><strong>Título:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
                    <p><strong>Editora:</strong> <?= htmlspecialchars($produto['editora']) ?></p>
                    <p><strong>Autor:</strong> <?= htmlspecialchars($produto['autor']) ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                    <p><strong>Quantidade:</strong> <?= (int)$produto['quantidade'] ?></p>
                    <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                </div>

                <!-- Botões organizados um embaixo do outro -->
                <div class="produto-actions">
                    <a href="editarProduto.php?id=<?= $produto['idproduto'] ?>" class="btn-action editar">Editar Produto</a>
                    <a href="excluirProduto.php?id=<?= $produto['idproduto'] ?>" class="btn-action excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir Produto</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nenhum produto cadastrado.</p>
        <a href="../cadastro/cadastroProduto.php" class="btn-toggle">Cadastrar Novo Produto</a>
    <?php endif; ?>
</div>

</main>

<script>
function toggleDetalhes(id) {
    const detalhes = document.getElementById(id);
    if (detalhes.style.display === 'none' || detalhes.style.display === '') {
        detalhes.style.display = 'block';
    } else {
        detalhes.style.display = 'none';
    }
}
</script>

</body>
</html>
