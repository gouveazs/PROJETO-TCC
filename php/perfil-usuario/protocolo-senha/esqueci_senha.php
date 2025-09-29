<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Senha - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Playfair Display', serif;
    }

    body {
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #F4F1EE;
      width: 800px;
      height: 500px;
      border-radius: 20px;
      display: flex;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .left-panel {
      background-color: #5a6b50;
      color: white;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }

    .left-panel h2 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    .left-panel p {
      font-size: 16px;
      margin-bottom: 20px;
      text-align: center;
    }

    .left-panel button {
      padding: 10px 30px;
      border: 2px solid white;
      background-color: transparent;
      color: white;
      border-radius: 20px;
      font-size: 14px;
      cursor: pointer;
      transition: 0.3s;
    }

    .left-panel button:hover {
      background-color: white;
      color: #5a6b50;
    }

    .right-panel {
      flex: 1;
      background-color: #ffffff;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
    }

    .right-panel h2 {
      color: #5a6b50;
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    form input {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      font-family: 'Playfair Display', serif;
    }

    form input[type="submit"] {
      padding: 12px;
      background-color: #5a6b50;
      color: white;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    form input[type="submit"]:hover {
      background-color: #3f4e39;
    }

    .message {
      margin-top: 10px;
      text-align: center;
      padding: 10px;
      border-radius: 8px;
    }

    .success {
      background-color: #e6f7ee;
      color: #0a7c42;
      border: 1px solid #a3e9c1;
    }

    .error {
      background-color: #fde8e8;
      color: #c53030;
      border: 1px solid #fbb6b6;
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
      color: #5a6b50;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <?php
  include '../../conexao.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'];

      $user = null;
      $vendedor = null;

      // Procurar no usuario
      $stmt = $conn->prepare("SELECT idusuario FROM usuario WHERE email = ?");
      $stmt->execute([$email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // Procurar no vendedor se não achou em usuario
      if (!$user) {
          $stmt = $conn->prepare("SELECT idvendedor FROM vendedor WHERE email = ?");
          $stmt->execute([$email]);
          $vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
      }

      if ($user || $vendedor) {
          // Gerar token seguro
          $token = bin2hex(random_bytes(32));
          $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

          if ($user) {
              $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idusuario) VALUES (?, ?, ?)");
              $stmt->execute([$token, $expira, $user['idusuario']]);
          } else {
              $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idvendedor) VALUES (?, ?, ?)");
              $stmt->execute([$token, $expira, $vendedor['idvendedor']]);
          }
          
         
          $link = "http://localhost/PROJETO-TCC/php/perfil-usuario/protocolo-senha/resetar_senha.php?token=$token";

          mail($email, "Recuperação de senha", "Clique aqui para redefinir sua senha: $link");
          echo $link;

          $mensagem = "Um link de recuperação foi enviado para seu email.";
          $tipoMensagem = "success";
      } else {
          $mensagem = "Email não encontrado.";
          $tipoMensagem = "error";
      }
  }
  ?>

  <div class="container">
    <div class="left-panel">
      <h2>Olá, novo usuário!</h2>
      <p>Cadastre-se agora<br>para aproveitar todos os recursos</p>
      <button onclick="window.location.href='../login/login.php'">CADASTRAR</button>
    </div>
    <div class="right-panel">
      <h2>Recuperar Senha</h2>
      <form action="" method="post">
        <input type="email" name="email" placeholder="Digite seu e-mail" required>
        <input type="submit" value="Enviar link de recuperação">
        <a href="login.php" class="back-link">Voltar para o login</a>
      </form>

      <?php if (!empty($mensagem)) : ?>
        <div class="message <?php echo $tipoMensagem; ?>">
          <?= htmlspecialchars($mensagem) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>