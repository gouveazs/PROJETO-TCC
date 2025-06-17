<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil do Usuário</title>
  <style>
    :root {
      --primary-color: #4a6bff;
      --danger-color: #ff4a4a;
      --text-color: #333;
      --text-light: #666;
      --bg-color: #f5f7fa;
      --card-bg: #ffffff;
      --border-radius: 8px;
      --box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: var(--bg-color);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: var(--text-color);
    }
    
    .perfil-container {
      background-color: var(--card-bg);
      padding: 40px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      width: 100%;
      max-width: 400px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .perfil-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .perfil-foto {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary-color);
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(74, 107, 255, 0.2);
    }
    
    h1 {
      font-size: 24px;
      margin: 0 0 10px 0;
      color: var(--text-color);
      font-weight: 600;
    }
    
    .user-info {
      margin: 20px 0;
      text-align: left;
      padding: 0 10px;
    }
    
    .info-item {
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
    }
    
    .info-label {
      font-weight: 500;
      color: var(--text-light);
    }
    
    .info-value {
      color: var(--text-color);
    }
    
    hr {
      border: none;
      height: 1px;
      background-color: #eee;
      margin: 25px 0;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: 500;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      margin: 5px;
      border: none;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #3a5aed;
      transform: translateY(-2px);
    }
    
    .btn-danger {
      background-color: var(--danger-color);
      color: white;
    }
    
    .btn-danger:hover {
      background-color: #e04040;
      transform: translateY(-2px);
    }
    
    .btn-group {
      display: flex;
      flex-direction: column;
      margin-top: 20px;
    }
    
    @media (max-width: 480px) {
      .perfil-container {
        padding: 30px 20px;
        margin: 20px;
      }
      
      .btn {
        width: 100%;
        margin: 5px 0;
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
    
    <div class="user-info">
      <div class="info-item">
        <span class="info-label">Email:</span>
        <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Senha:</span>
        <span class="info-value">••••••••</span>
      </div>
    </div>
    
    <hr>
    
    <div class="btn-group">
      <a href="editar_perfil.php" class="btn btn-primary">Editar Informações</a>
      <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn btn-danger">DELETAR CONTA ☠️</a>
    </div>
  </div>
</body>
</html>