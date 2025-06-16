<?php
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header('Location: ../../login/login.php');
    exit;
}

include '../conexao.php';

$nome = $_SESSION['nome_usuario'];

$stmt = $conn->prepare("SELECT * FROM cadastro_usuario WHERE nome = ?");
$stmt->execute([$nome]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil do Usuário</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .perfil-container {
      background-color: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 350px;
      text-align: center;
    }
    .perfil-foto {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solidrgb(0, 0, 0);
      margin-bottom: 20px;
    }
    h1 {
      font-size: 24px;
      margin-bottom: 8px;
      color: #333;
    }
    p {
      margin: 4px 0;
      color: #555;
      font-size: 16px;
    }
    .btn-sair {
      margin-top: 25px;
      display: inline-block;
      padding: 10px 25px;
      font-size: 16px;
      background-color: #dc3545;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }
    .btn-sair:hover {
      background-color: #b02a37;
    }

    .btn-normie {
      margin-top: 25px;
      display: inline-block;
      padding: 10px 25px;
      font-size: 16px;
      background-color: #3567dc;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }
    .btn-normie:hover {
      background-color:rgb(33, 64, 138);
    }
  </style>
</head>
<body>
  <div class="perfil-container">
    <?php
    // agr ta funfano
    if (!empty($usuario['foto_de_perfil'])) {
        $imgData = base64_encode($usuario['foto_de_perfil']);
        echo '<img src="data:image/png;base64,' . $imgData . '" alt="Foto do usuário" class="perfil-foto" />';
    } else {
        echo '<img src="../../imgs/imagem-do-usuario-com-fundo-preto.png" alt="Sem foto" class="perfil-foto" />';
    }
    ?>
    <h1><?php echo htmlspecialchars($usuario['nome']); ?></h1>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
    <p><strong>Senha:</strong> <?php echo htmlspecialchars($usuario['senha']); ?></p>
    
    <hr>

    <a href="editar_perfil.php" class="btn-normie">Editar Informações</a>
    
    <br>
    
    <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">DELETAR CONTA ☠️</a>
  </div>
</body>
</html>