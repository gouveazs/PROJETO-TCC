<?php
session_start();
require '../conexao.php';

// Dados do usuário logado
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Usuário';
$foto_de_perfil = isset($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : '../imgs/perfil.png';

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    echo "Produto não encontrado!";
    exit;
}

$id = (int) $_GET['id'];

// Buscar produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND id_usuario = ?");
$stmt->execute([$id, $_SESSION['id_usuario']]);
$produto = $stmt->fetch();

if (!$produto) {
    echo "Produto não encontrado!";
    exit;
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeProd = $_POST['nome'];
    $preco = $_POST['preco'];
    $descricao = $_POST['descricao'];

    // Verifica se enviou nova imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['tmp_name'] != "") {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ?, imagem = ? WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$nomeProd, $preco, $descricao, $imagem, $id, $_SESSION['id_usuario']]);
    } else {
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$nomeProd, $preco, $descricao, $id, $_SESSION['id_usuario']]);
    }

    header("Location: painel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Produto - <?= htmlspecialchars($produto['nome']) ?></title>
  <link rel="stylesheet" href="../css/painel.css">
  <style>
    body { margin: 0; font-family: "Georgia", serif; background: #f7f4f1; display: flex; }
    .sidebar { width: 260px; background: #4f5d42; color: white; height: 100vh; padding: 20px; }
    .sidebar .perfil { text-align: center; margin-bottom: 30px; }
    .sidebar .perfil img { width: 80px; height: 80px; border-radius: 50%; }
    .sidebar h3 { margin: 10px 0 0; }
    .sidebar nav ul { list-style: none; padding: 0; }
    .sidebar nav ul li { margin: 15px 0; }
    .sidebar nav ul li a { text-decoration: none; color: white; font-weight: bold; }
    .content { flex: 1; padding: 20px; }
    .topbar { background: #5a4221; color: white; padding: 10px 20px; margin: -20px -20px 20px -20px; }
    .form-editar { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
    .form-editar h2 { text-align: center; margin-bottom: 20px; }
    .form-editar label { display: block; margin-top: 10px; font-weight: bold; }
    .form-editar input[type="text"], .form-editar input[type="number"], .form-editar textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; }
    .form-editar input[type="file"] { margin-top: 5px; }
    .form-editar button { margin-top: 15px; background: #4f5d42; color: white; padding: 10px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; }
    .imagem-atual { margin-top: 10px; text-align: center; }
    .imagem-atual img { max-width: 200px; border-radius: 8px; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="perfil">
      <img src="<?= $foto_de_perfil ?>" alt="Foto de perfil">
      <h3><?= htmlspecialchars($nome) ?></h3>
    </div>
    <nav>
      <ul>
        <li><a href="painel.php">Início</a></li>
        <li><a href="vendas.php">Vendas publicadas</a></li>
        <li><a href="rendimento.php">Rendimento</a></li>
        <li><a href="cadastrar.php">Cadastrar Produto</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Conteúdo principal -->
  <main class="content">
    <header class="topbar">
      <h1>Editar Produto</h1>
    </header>

    <section class="form-editar">
      <h2><?= htmlspecialchars($produto['nome']) ?></h2>
      <form action="" method="POST" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>

        <label for="preco">Preço:</label>
        <input type="number" step="0.01" id="preco" name="preco" value="<?= $produto['preco'] ?>" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" rows="5" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

        <label for="imagem">Imagem (opcional para atualizar):</label>
        <input type="file" id="imagem" name="imagem" accept="image/*">

        <?php if (!empty($produto['imagem'])): ?>
          <div class="imagem-atual">
            <p>Imagem atual:</p>
            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="Imagem do produto">
          </div>
        <?php endif; ?>

        <button type="submit">Salvar Alterações</button>
      </form>
    </section>
  </main>
</body>
</html>
