<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cadastro</title>
  <!-- Fonte Playfair Display -->
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

    form input[type="text"],
    form input[type="email"],
    form input[type="password"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    form input[type="file"] {
      font-family: inherit; 
      font-size: 14px;
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
      <h2>Bem-vindo de volta!</h2>
      <p>Para se manter conectado,<br>faça login com suas informações pessoais</p>
      <button onclick="window.location.href='../../login/login.php'">ENTRAR</button>
    </div>
    <div class="right-panel">
      <h2>Criar Conta</h2>
      <form method="POST" action="../insercao/insercao.php" enctype="multipart/form-data">
        <input type="text" name="nome" placeholder="Nome de usuário" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <!-- Botão de arquivo nativo, sem estilização extra -->
        <input type="file" name="foto_de_perfil" accept="image/*">
        <input type="submit" value="Cadastrar">
      </form>
      <?php if (isset($_GET['erro']) && $_GET['erro'] == 'nome_ou_email'): ?>
        <div class="erro">Nome ou e-mail já cadastrados!</div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
