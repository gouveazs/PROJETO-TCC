<?php
session_start();
$nome = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Seu Perfil - Entre Linhas</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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

    .perfil-container {
      background: #ffffff;
      padding: 40px;
      width: 100%;
      max-width: 900px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      display: flex;
      gap: 40px;
    }

    .perfil-lateral {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding-right: 30px;
      border-right: 1px solid #eaeaea;
    }

    .perfil-foto {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #eaeaea;
      margin-bottom: 25px;
    }

    .perfil-principal {
      flex: 2;
    }

    h1 {
      font-size: 32px;
      color: #333;
      margin-bottom: 5px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .user-role {
      color: #666;
      font-size: 14px;
      margin-bottom: 30px;
      font-weight: 400;
      font-style: italic;
    }

    .info-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }

    .info-group {
      margin-bottom: 30px;
    }

    .info-group h3 {
      font-size: 18px;
      color: #5a6b50;
      margin-bottom: 20px;
      font-weight: 600;
      padding-bottom: 8px;
      border-bottom: 2px solid #eaeaea;
    }

    .info-linha {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
    }

    .info-linha:last-child {
      margin-bottom: 0;
    }

    .info-linha strong {
      color: #555;
      font-weight: 500;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .info-linha span {
      font-size: 16px;
      color: #333;
      font-weight: 400;
      padding: 6px 0;
    }

    .info-linha input {
      width: 100%;
      font-size: 16px;
      color: #333;
      border: none;
      background: transparent;
      outline: none;
      padding: 6px 0;
      font-weight: 400;
    }

    .btn-container {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 30px;
      width: 100%;
    }

    .btn-normie,
    .btn-sair,
    .btn-voltar {
      padding: 14px;
      font-size: 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 500;
      width: 100%;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .btn-normie {
      background-color: #5a6b50;
      color: #fff;
    }

    .btn-normie:hover {
      background-color: #4a5a40;
    }

    .btn-sair {
      background-color: #c44;
      color: #fff;
    }

    .btn-sair:hover {
      background-color: #b33;
    }

    .btn-voltar {
      background-color: #f0f0f0;
      color: #666;
      border: 1px solid #ddd;
    }

    .btn-voltar:hover {
      background-color: #e5e5e5;
    }

    .btn-icon {
      margin-right: 10px;
      width: 16px;
      height: 16px;
    }

    .empty-field {
      color: #999 !important;
      font-style: italic;
    }

    @media (max-width: 768px) {
      .perfil-container {
        flex-direction: column;
        gap: 30px;
        padding: 30px;
      }
      
      .perfil-lateral {
        padding-right: 0;
        border-right: none;
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 30px;
      }
      
      .info-container {
        grid-template-columns: 1fr;
        gap: 20px;
      }
    }

    @media (max-width: 480px) {
      .perfil-container {
        padding: 25px 20px;
      }
      
      h1 {
        font-size: 26px;
      }
      
      .perfil-foto {
        width: 120px;
        height: 120px;
      }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="perfil-container">
    <div class="perfil-lateral">
      <?php if ($foto_de_perfil): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" class="perfil-foto">
      <?php else: ?>
        <img src="imgs/usuario.jpg" alt="Foto de Perfil" class="perfil-foto">
      <?php endif; ?>

      <h1>marina</h1>
      <div class="user-role">Membro desde 2025</div>
      
      <div class="btn-container">
        <a href="editar_perfil.php" class="btn-normie">
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
          </svg>
          EDITAR PERFIL
        </a>

        <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
          </svg>
          DELETAR CONTA
        </a>

        <a href="../../index.php" class="btn-voltar">
          ⬅ VOLTAR AO INÍCIO
        </a>
      </div>
    </div>

    <div class="perfil-principal">
      <div class="info-container">
        <div class="info-group">
          <h3>INFORMAÇÕES PESSOAIS</h3>
          <div class="info-linha">
            <strong>Nome completo</strong>
            <span>Marina Silva</span>
          </div>

          <div class="info-linha">
            <strong>Email</strong>
            <span>marina@marina</span>
          </div>

          <div class="info-linha">
            <strong>Senha</strong>
            <input type="password" id="senhaInput" value="********" readonly>
          </div>

          <div class="info-linha">
            <strong>CPF</strong>
            <input type="text" name="cpf" id="cpf" value="" readonly class="empty-field">
          </div>
        </div>

        <div class="info-group">
          <h3>ENDEREÇO</h3>
          <div class="info-linha">
            <strong>CEP</strong>
            <span class="empty-field">Não informado</span>
          </div>

          <div class="info-linha">
            <strong>Estado</strong>
            <input type="text" name="estado" id="estado" value="Não informado" readonly class="empty-field">
          </div>

          <div class="info-linha">
            <strong>Cidade</strong>
            <input type="text" name="cidade" id="cidade" value="Não informado" readonly class="empty-field">
          </div>

          <div class="info-linha">
            <strong>Bairro</strong>
            <input type="text" name="bairro" id="bairro" value="Não informado" readonly class="empty-field">
          </div>

          <div class="info-linha">
            <strong>Rua</strong>
            <input type="text" name="rua" id="rua" value="Não informado" readonly class="empty-field">
          </div>
        </div>
      </div>
    </div>

    <div class="btn-container">
      <a href="editar_perfil.php" class="btn-normie">
        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
        </svg>
        Editar Informações
      </a>

      <a href="deletar.php" onclick="return confirm('Tem certeza que deseja deletar sua conta? Isso não poderá ser desfeito!');" class="btn-sair">
        <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
        </svg>
        Desativar Conta
      </a>

      <a href="../../index.php" class="btn-normie">
        ⬅ Voltar ao Início
      </a>
    </div>
  </div>

  <script>
    function toggleSenha() {
      const input = document.getElementById("senhaInput");
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const input = document.getElementById('cpf');
      let cpf = input.value.replace(/\D/g, '');
      if (cpf.length === 11 && cpf !== '') {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        input.value = cpf;
      }
    });
  </script>
</body>
</html>