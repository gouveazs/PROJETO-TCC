<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedido Realizado com Sucesso - Entre Linhas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      min-height: 100vh;
      padding: 20px;
    }

    .success-container {
      background-color: #ffffff;
      width: 800px;
      max-width: 100%;
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
      align-items: center;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
    }

    .success-icon {
      font-size: 80px;
      color: #5a6b50;
      margin-bottom: 25px;
      animation: pop 0.4s ease-out;
    }
    
    @keyframes pop {
      0% { transform: scale(0); }
      100% { transform: scale(1); }
    }
    
    h2 {
      color: #5a6b50;
      font-size: 28px;
      margin-bottom: 15px;
      text-align: center;
    }
    
    .text-muted {
      color: #5a6b50 !important;
      font-size: 16px;
      margin-bottom: 10px;
      text-align: center;
    }
    
    a {
      color: #5a6b50;
      transition: color 0.3s;
    }
    
    a:hover {
      color: #3f4e39;
      text-decoration: underline;
    }
    
    .btn-custom {
      background-color: #5a6b50;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 30px;
      font-size: 16px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
    }
    
    .btn-custom:hover {
      background-color: #3f4e39;
      transform: scale(1.05);
      color: white;
    }

    @media (max-width: 768px) {
      .success-container {
        flex-direction: column;
        height: auto;
      }
      
      .left-panel, .right-panel {
        width: 100%;
      }
      
      .right-panel {
        border-radius: 0 0 20px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="success-container">
    <div class="left-panel">
      <h2>Obrigado pela compra!</h2>
      <p>Sua compra foi processada com sucesso e já está sendo preparada.</p>
      <button onclick="window.location.href='../perfil-usuario/ver_perfil.php?aba=pedidos'">ACOMPANHAR PEDIDO</button>
    </div>
    <div class="right-panel">
      <div class="success-icon">✅</div>
      <h2>Pedido realizado com sucesso!</h2>
      <p class="text-muted mt-3">Obrigado por comprar conosco. Você pode acompanhar o status do seu pedido <a href="../perfil-usuario/ver_perfil.php?aba=pedidos">aqui</a>.</p>
      <a href="../../index.php" class="btn btn-custom mt-4">Voltar às compras</a>
    </div>
  </div>

  <!-- VLibras - Widget de Libras -->
  <div vw class="enabled">
      <div vw-access-button class="active"></div>
      <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
      </div>
  </div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script>
      new window.VLibras.Widget('https://vlibras.gov.br/app');
  </script>

</body>
</html>