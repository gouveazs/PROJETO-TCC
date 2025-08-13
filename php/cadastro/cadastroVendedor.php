<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Vendedor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Playfair Display -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Playfair Display', serif;
      background-color: #F4F1EE;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background-color: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 700px;
      width: 100%;
    }

    .form-header {
      font-size: 22px;
      font-weight: 600;
      color: #5a6b50;
      margin-bottom: 30px;
      text-align: center;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-control {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
    }

    .form-control label {
      margin-bottom: 8px;
      font-weight: 500;
      color: #333;
    }

    .form-control input {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: border-color 0.3s ease;
      font-family: 'Playfair Display', serif;
    }

    .form-control input:focus {
      border-color: #5a6b50;
      outline: none;
    }

    .form-footer {
      text-align: center;
      margin-top: 30px;
    }

   input[type="submit"] {
    background-color: #5a6b50;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    font-family: 'Playfair Display', serif;
    width: 100%; /* <-- Aqui faz o botão ocupar toda a largura */
    }


    input[type="submit"]:hover {
      background-color: #4a5843;
    }

    @media (max-width: 600px) {
      .form-group {
        flex-direction: column;
      }

      .form-control {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="form-header">Preencha com suas informações</div>
    <form method="POST" action="../insercao/insercaoVendedor.php">
      <div class="form-group">
        <div class="form-control">
          <label for="nomeV">Nome completo</label>
          <input type="text" id="nomeV" name="nomeV" required>
        </div>
        <div class="form-control">
          <label for="idade">Data de nascimento</label>
          <input type="date" id="data_nascimento" name="data_nascimento" required>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control">
          <label for="emailV">E-mail</label>
          <input type="email" id="emailV" name="emailV" required>
        </div>
        <div class="form-control">
          <label for="senhaV">Senha</label>
          <input type="password" id="senhaV" name="senhaV" required>
        </div>
      </div>

      <div class="form-group">
        <div class="form-control">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" required>
        </div>
        <div class="form-control">
          <label for="cnpj">CNPJ</label>
          <input type="text" id="cnpj" name="cnpj">
        </div>
      </div>

      <div class="form-group">
        <div class="form-control">
          <input type="file" name="foto_de_perfil" accept="image/*">
        </div>
      </div>

      <div class="form-footer">
        <input type="submit" value="Cadastrar">
      </div>
    </form>
  </div>

</body>
</html>
