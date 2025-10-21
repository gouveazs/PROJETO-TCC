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
    // Exibir todos os erros e exceções de forma explícita
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    $tipo = $_GET['tipo'];

    try {
        include '../../conexao.php';

        if (!$conn) {
            throw new Exception("Erro na conexão com o banco de dados");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("Erro: Email inválido.");
            }

            $user = null;
            $vendedor = null;

            // Procurar no usuário
            $stmt = $conn->prepare("SELECT idusuario, nome FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Procurar no vendedor se não achou no usuário
            if (!$user) {
                $stmt = $conn->prepare("SELECT idvendedor, nome_completo FROM vendedor WHERE email = ?");
                $stmt->execute([$email]);
                $vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($user || $vendedor) {
                $token = bin2hex(random_bytes(32));
                $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

                if ($user) {
                    $id_usuario = $user['idusuario'];
                    $nome = $user['nome'];
                    $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idusuario, idvendedor) VALUES (?, ?, ?, NULL)");
                    $stmt->execute([$token, $expira, $id_usuario]);
                } else {
                    $id_vendedor = $vendedor['idvendedor'];
                    $nome = $vendedor['nome_completo'];
                    $stmt = $conn->prepare("INSERT INTO recuperacao_senha (token, expira_em, idusuario, idvendedor) VALUES (?, ?, NULL, ?)");
                    $stmt->execute([$token, $expira, $id_vendedor]);
                }

                if ($stmt->rowCount() > 0) {
                    if ($db == 1) {
                        $link = "http://localhost/PROJETO-TCC/php/perfil-usuario/protocolo-senha/resetar_senha.php?token=" . urlencode($token);
                        echo "<p><strong>Link de recuperação (modo local):</strong> <a href='$link'>$link</a></p>";
                    } else {
                        $link = "https://projetosetim.com.br/2025/php3/php/perfil-usuario/protocolo-senha/resetar_senha.php?token=" . urlencode($token);

                        $to = $email;
                        $subject = "Recuperação de Senha - Entre Linhas";
                        $message = "Olá $nome,\n\nVocê solicitou a recuperação de senha.\n\n";
                        $message .= "Clique no link abaixo para redefinir sua senha:\n$link\n\n";
                        $message .= "Este link é válido por 1 hora.\n\nSe você não solicitou, ignore este email.\n\nEquipe Entre Linhas";

                        $headers = "From: noreply@entrelinhas.com\r\n";
                        $headers .= "Reply-To: noreply@entrelinhas.com\r\n";
                        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                        if (mail($to, $subject, $message, $headers)) {
                            echo "✅ Email de recuperação enviado para: $email";
                        } else {
                            echo "⚠️ Falha ao enviar email. Link manual: <a href='$link'>$link</a>";
                        }
                    }
                } else {
                    die("Erro: falha ao salvar token de recuperação no banco.");
                }
            } else {
                die("Erro: email não encontrado em nosso sistema.");
            }
        }
    } catch (PDOException $e) {
        // Mostra o erro PDO diretamente
        die("ERRO PDO: " . $e->getMessage());
    } catch (Exception $e) {
        // Mostra o erro geral diretamente
        die("ERRO GERAL: " . $e->getMessage());
    }
  ?>

  <div class="container">
    <div class="left-panel">
      <?php if ($tipo == "vendedor"): ?>
        <h2>Olá, novo vendedor!</h2>
        <p>Cadastre-se agora<br>e comece a vender hoje!</p>
        <button onclick="window.location.href='../../cadastro/cadastroVendedor.php'">CADASTRAR</button>
      <?php else: ?>
        <h2>Olá, novo usuário!</h2>
        <p>Cadastre-se agora<br>para aproveitar todos os recursos</p>
        <button onclick="window.location.href='../../cadastro/cadastroUsuario.php'">CADASTRAR</button>
      <?php endif; ?>
    </div>
    <div class="right-panel">
      <h2>Recuperar Senha</h2>
      <form action="" method="post">
        <input type="email" name="email" placeholder="Digite seu e-mail" required>
        <input type="submit" value="Enviar link de recuperação">
        <?php if ($tipo == "vendedor"): ?>
          <a href="../../login/loginVendedor.php" class="back-link">Voltar para o login</a>
        <?php else: ?>
          <a href="../../login/login.php" class="back-link">Voltar para o login</a>
        <?php endif; ?>
      </form>

      <?php if (!empty($mensagem)) : ?>
        <div class="message <?php echo $tipoMensagem; ?>">
          <?= htmlspecialchars($mensagem) ?>
        </div>
      <?php endif; ?>
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