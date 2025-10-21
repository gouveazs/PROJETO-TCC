<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedido Realizado com Sucesso</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Poppins', sans-serif;
    }
    .success-container {
      max-width: 600px;
      margin: 100px auto;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 40px;
      text-align: center;
    }
    .success-icon {
      font-size: 80px;
      color: #28a745;
      margin-bottom: 20px;
      animation: pop 0.4s ease-out;
    }
    @keyframes pop {
      0% { transform: scale(0); }
      100% { transform: scale(1); }
    }
    .btn-custom {
      background-color: #28a745;
      color: white;
      border-radius: 30px;
      padding: 10px 25px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      background-color: #218838;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <div class="success-container">
    <div class="success-icon">✅</div>
    <h2>Pedido realizado com sucesso!</h2>
    <p class="text-muted mt-3">Obrigado por comprar conosco. Você pode acompanhar o status do seu pedido <a href="../perfil-usuario/ver_perfil.php?aba=pedidos">aqui</a>.</p>
    <a href="../../index.php" class="btn btn-custom mt-4">Voltar às compras</a>
  </div>

</body>
</html>
