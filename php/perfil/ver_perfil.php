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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .perfil-container {
      background: #ffffff;
      padding: 40px;
      width: 100%;
      max-width: 450px;
      text-align: center;
      border-radius: 16px;
    }

    .perfil-foto {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #eee;
      margin-bottom: 25px;
    }

    h1 {
      font-size: 24px;
      color: #333;
      margin-bottom: 30px;
      font-weight: 600;
    }

    .info-container {
      text-align: left;
      margin-bottom: 30px;
    }

    .info-linha {
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .info-linha:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .info-linha strong {
      display: block;
      color: #555;
      font-weight: 500;
      margin-bottom: 5px;
      font-size: 15px;
    }

    .info-linha span {
      font-size: 16px;
      color: #333;
    }

    .info-linha input {
      width: 100%;
      font-size: 16px;
      color: #333;
      border: none;
      background: transparent;
      outline: none;
      padding: 5px 0;
    }

    .btn-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .btn-normie,
    .btn-sair {
      padding: 12px;
      font-size: 15px;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 500;
      width: 100%;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-normie {
      background-color: #5a6b50;
      color: #fff;
    }

    .btn-sair {
      background-color: #dc3545;
      color: #fff;
    }

    .btn-icon {
      margin-right: 8px;
      width: 16px;
      height: 16px;
    }

    @media (max-width: 480px) {
      .perfil-container {
        padding: 30px 20px;
      }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="perfil-container">
    <?php
    if (!empty($usuario['foto_de_perfil'])) {
        $imgData = base64_encode($usuario['foto_de_perfil']);
        echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="Foto do usuário" class="perfil-foto" />';
    } else {
        echo '<img src="../../imgs/imagem-do-usuario-com-fundo-preto.png" alt="Sem foto" class="perfil-foto" />';
    }
    ?>
    <h1><?php echo htmlspecialchars($usuario['nome']); ?></h1>

    <div class="info-container">
      <div class="info-linha">
        <strong>Email:</strong>
        <span><?php echo htmlspecialchars($usuario['email']); ?></span>
      </div>

      <div class="info-linha">
        <strong>Senha:</strong>
        <input type="password" id="senhaInput" value="<?php echo htmlspecialchars($usuario['senha']); ?>" readonly>
      </div>
    </div>

    <div class="btn-container">
      <a href="editar_perfil.php" class="btn-normie">
        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
        </svg>
        Editar Informações
      </a>
      <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">
        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
        </svg>
        Deletar Conta
      </a>
    </div>
  </div>

  <script>
    function toggleSenha() {
      const input = document.getElementById("senhaInput");
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    }
  </script>
</body>
</html>