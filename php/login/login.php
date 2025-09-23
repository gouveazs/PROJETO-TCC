<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Entre Linhas</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../../imgs/logotipo.png"/>
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

    .erro {
      color: red;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <h2>Olá, novo usuário!</h2>
      <p>Cadastre-se agora<br>para aproveitar todos os recursos</p>
      <button onclick="window.location.href='../cadastro/cadastroUsuario.php'">CADASTRAR</button>
    </div>
    <div class="right-panel">
      <h2>Entrar na Conta</h2>
      <form action="processaLogin.php" method="post">
        <input type="text" name="usuario" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="submit" value="Login">
      </form>

      <?php if (isset($_GET['error']) == 1): ?>
        <p class="erro">Usuário ou senha incorretos</p>
      <?php endif; ?>

      <?php if (isset($_GET['error']) == 2): ?>
        <p class="erro">
            Sua conta foi desativada.
            <a href="reativar.php?usuario=<?php echo urlencode($_GET['usuario']); ?>">
                Deseja reativar?
            </a>
        </p>
      <?php endif; ?>

      <?php if (isset($_GET['reativado']) && $_GET['reativado'] == 1): ?>
        <p style="color: green; text-align:center; margin-top:10px;">
            Sua conta foi reativada com sucesso! Faça login normalmente.
        </p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>