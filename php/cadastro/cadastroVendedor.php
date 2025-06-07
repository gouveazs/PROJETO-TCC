<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Vendedor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #F4F1EE;
      display: flex;
      flex-direction: column;
    }

    .cabecalho {
      background-color: #402718;
      color: white;
      padding: 15px 20px;
      font-size: 24px;
      font-weight: bold;
      border-bottom: 2px solid #402718;
    }

    .conteudo {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
    }

    .container {
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 30px;
      max-width: 800px;
      width: 100%;
    }

    .form-header {
      background-color: #5a6b50;
      color: white;
      padding: 15px;
      font-weight: bold;
      border-radius: 4px 4px 0 0;
      margin: -30px -30px 30px -30px;
      text-align: center;
    }

    .form-group {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group label {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .form-control {
      flex: 1 1 200px;
      display: flex;
      flex-direction: column;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .form-footer {
      text-align: right;
    }

    input[type="submit"] {
      padding: 10px 20px;
      background-color: #5a6b50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }

    input[type="submit"]:hover {
      background-color: #495b3e;
    }

    .rodape {
      background-color: #402718;
      color: white;
      text-align: center;
      padding: 15px;
      font-weight: bold;
    }

    @media (max-width: 600px) {
      .form-group {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

  <!-- Cabeçalho -->
  <header class="cabecalho">
    Cadastro de Vendedor
  </header>

  <!-- Conteúdo principal -->
  <main class="conteudo">
    <div class="container">
      <div class="form-header">Preencha com suas informações</div>
      <form method="POST" action="../insercao/insercaoVendedor.php">
        <div class="form-group">
          <div class="form-control">
            <label for="nomeV">Nome completo</label>
            <input type="text" id="nomeV" name="nomeV">
          </div>
          <div class="form-control">
            <label for="idade">Idade</label>
            <input type="text" id="idade" name="idade">
          </div>
        </div>

        <div class="form-group">
          <div class="form-control">
            <label for="emailV">E-mail</label>
            <input type="email" id="emailV" name="emailV">
          </div>
          <div class="form-control">
            <label for="senhaV">Senha</label>
            <input type="password" id="senhaV" name="senhaV">
          </div>
        </div>

        <div class="form-group">
          <div class="form-control">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf">
          </div>
          <div class="form-control">
            <label for="cnpj">CNPJ</label>
            <input type="text" id="cnpj" name="cnpj">
          </div>
        </div>

        <div class="form-footer">
          <input type="submit" value="Cadastrar">
        </div>
      </form>
    </div>
  </main>

  <!-- Rodapé -->
  <footer class="rodape">
    Todos os direitos reservados - 2025
  </footer>

</body>
</html>
