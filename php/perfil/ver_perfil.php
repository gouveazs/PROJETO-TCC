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
      background: linear-gradient(135deg, #F4F1EE 0%, #c3cfe2 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .perfil-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 24px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 100%;
      max-width: 450px;
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
      overflow: hidden;
    }

    .perfil-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, #5a6b50, #8a9a76);
    }

    .perfil-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .perfil-foto {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #f0f0f0;
      margin-bottom: 25px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    .perfil-foto:hover {
      transform: scale(1.05);
    }

    h1 {
      font-size: 28px;
      color: #333;
      margin-bottom: 25px;
      font-weight: 600;
      position: relative;
      display: inline-block;
    }

    .info-container {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 25px;
      text-align: left;
    }

    .info-linha {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 15px 0;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    .info-linha:last-child {
      border-bottom: none;
      padding-bottom: 0;
      margin-bottom: 0;
    }

    .info-linha strong {
      color: #555;
      font-weight: 500;
      min-width: 80px;
    }

    .info-linha span {
      font-size: 16px;
      color: #333;
      word-break: break-word;
      text-align: right;
      flex: 1;
      margin-left: 15px;
    }

    .info-linha input {
      flex: 1;
      font-size: 16px;
      color: #333;
      border: none;
      background: transparent;
      outline: none;
      text-align: right;
      padding: 5px;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .info-linha input:focus {
      background: rgba(90, 107, 80, 0.1);
    }

    .info-linha button {
      background: none;
      border: none;
      cursor: pointer;
      padding: 5px;
      margin-left: 10px;
      border-radius: 50%;
      transition: background 0.3s;
    }

    .info-linha button:hover {
      background: rgba(0, 0, 0, 0.05);
    }

    .info-linha button svg {
      width: 20px;
      height: 20px;
      fill: #5a6b50;
      transition: fill 0.3s;
    }

    .info-linha button:hover svg {
      fill: #4a5842;
    }

    .btn-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .btn-normie,
    .btn-sair {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 25px;
      font-size: 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-weight: 500;
      width: 100%;
    }

    .btn-normie {
      background: linear-gradient(135deg, #5a6b50 0%, #8a9a76 100%);
      color: #fff;
      box-shadow: 0 4px 6px rgba(90, 107, 80, 0.2);
    }

    .btn-normie:hover {
      background: linear-gradient(135deg, #4a5842 0%, #7a8a66 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(90, 107, 80, 0.3);
    }

    .btn-sair {
      background: linear-gradient(135deg, #dc3545 0%, #e35d6a 100%);
      color: #fff;
      box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
    }

    .btn-sair:hover {
      background: linear-gradient(135deg, #b02a37 0%, #c94a56 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 480px) {
      .perfil-container {
        padding: 30px 20px;
      }
      
      .perfil-foto {
        width: 120px;
        height: 120px;
      }
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

    <div class="info-container">
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
    </div>

    <div class="btn-container">
      <a href="editar_perfil.php" class="btn-normie">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
        </svg>
        Editar Informações
      </a>
      <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
        </svg>
        Deletar Conta
      </a>
    </div>
  </div>

  <script>
    function toggleSenha() {
      const input = document.getElementById("senhaInput");
      const eyeIcon = document.querySelector("#eyeIcon");
      
      if (input.type === "password") {
        input.type = "text";
        eyeIcon.innerHTML = '<path d="M288 144a110.94 110.94 0 0 0-31.24 5 55.4 55.4 0 0 1 7.24 27 56 56 0 0 1-56 56 55.4 55.4 0 0 1-27-7.24A111.71 111.71 0 1 0 288 144zm284.52 97.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400c-98.65 0-189.09-55-237.93-144C98.91 167 189.34 112 288 112s189.09 55 237.93 144C477.1 345 386.66 400 288 400z"/>';
      } else {
        input.type = "password";
        eyeIcon.innerHTML = '<path d="M572.52 241.4C518.59 135.45 407.5 64 288 64S57.41 135.45 3.48 241.4a48.13 48.13 0 0 0 0 29.2C57.41 376.55 168.5 448 288 448s230.59-71.45 284.52-177.4a48.13 48.13 0 0 0 0-29.2ZM288 400c-97.2 0-191.72-58.23-240-144 48.28-85.77 142.8-144 240-144s191.72 58.23 240 144c-48.28 85.77-142.8 144-240 144Zm0-272a128 128 0 1 0 128 128A128 128 0 0 0 288 128Zm0 208a80 80 0 1 1 80-80 80 80 0 0 1-80 80Z"/>';
      }
    }
  </script>
</body>
</html>