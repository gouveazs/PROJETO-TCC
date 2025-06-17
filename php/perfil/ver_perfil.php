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
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f4f1ee;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .perfil-container {
      background: #ffffff;
      padding: 50px 40px;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 400px;
      transition: transform 0.3s;
    }

    .perfil-container:hover {
      transform: translateY(-5px);
    }

    .perfil-foto {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #5a6b50;
      margin-bottom: 20px;
    }

    h1 {
      font-size: 26px;
      color: #333;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .info-linha {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 10px;
      margin: 10px 0;
    }

    .info-linha strong {
      min-width: 70px;
      text-align: right;
      color: #333;
    }

    .info-linha span,
    .info-linha input {
      flex: 1;
      font-size: 16px;
      color: #555;
      border: none;
      background: transparent;
      outline: none;
    }

    .info-linha button {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0 5px;
      display: flex;
      align-items: center;
    }

    .info-linha button svg {
      width: 20px;
      height: 20px;
      fill: #555;
    }

    hr {
      margin: 30px 0;
      border: none;
      border-top: 1px solid #ddd;
    }

    .btn-normie,
    .btn-sair {
      display: inline-block;
      margin: 10px 8px;
      padding: 12px 35px;
      font-size: 15px;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .btn-normie {
      background: #5a6b50;
      color: #fff;
    }

    .btn-normie:hover {
      background: #4a5842;
      transform: translateY(-2px);
    }

    .btn-sair {
      background: #dc3545;
      color: #fff;
    }

    .btn-sair:hover {
      background: #b02a37;
      transform: translateY(-2px);
    }
  </style>
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

    <div class="info-linha">
      <strong>Email:</strong>
      <span><?php echo htmlspecialchars($usuario['email']); ?></span>
    </div>

    <div class="info-linha">
      <strong>Senha:</strong>
      <input type="password" id="senhaInput" value="<?php echo htmlspecialchars($usuario['senha']); ?>" readonly>
      <button onclick="toggleSenha()" aria-label="Mostrar/ocultar senha">
        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M572.52 241.4C518.59 135.45 407.5 64 288 64S57.41 135.45 3.48 241.4a48.13 48.13 0 0 0 0 29.2C57.41 376.55 168.5 448 288 448s230.59-71.45 284.52-177.4a48.13 48.13 0 0 0 0-29.2ZM288 400c-97.2 0-191.72-58.23-240-144 48.28-85.77 142.8-144 240-144s191.72 58.23 240 144c-48.28 85.77-142.8 144-240 144Zm0-272a128 128 0 1 0 128 128A128 128 0 0 0 288 128Zm0 208a80 80 0 1 1 80-80 80 80 0 0 1-80 80Z"/>
        </svg>
      </button>
    </div>

    <hr>

    <a href="editar_perfil.php" class="btn-normie">Editar Informações</a>
    <br>
    <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">DELETAR CONTA ☠️</a>
  </div>

  <script>
    function toggleSenha() {
      var input = document.getElementById("senhaInput");
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    }
  </script>
</body>
</html>
