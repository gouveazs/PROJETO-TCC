<?php
  session_start();

  if (!isset($_SESSION['nome_usuario'])) {
      header('Location: ../../login/login.php');
      exit;
  }

  include '../conexao.php';

  $nome = $_SESSION['nome_usuario'];
  $foto_de_perfil = isset($_SESSION['foto_de_perfil']) ? $_SESSION['foto_de_perfil'] : null;

  $stmt = $conn->prepare("SELECT * FROM usuario WHERE nome = ?");
  $stmt->execute([$nome]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
      echo "Usuário não encontrado.";
      exit;
  }

  // Verificar qual aba está ativa (perfil ou compras)
  $aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'perfil';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $aba_ativa === 'compras' ? 'Minhas Compras' : 'Seu Perfil'; ?> - Entre Linhas</title>
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

    .container-principal {
      width: 100%;
      max-width: 900px;
      margin-top: 20px;
    }

    .navegacao-superior {
      display: flex;
      gap: 15px;
      background: #ffffff;
      padding: 20px 30px;
      border-radius: 12px 12px 0 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border-bottom: 1px solid #eaeaea;
    }

    .navegacao-superior a {
      text-decoration: none;
      color: #5a6b50;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 8px;
      transition: all 0.3s ease;
      background-color: transparent;
      border: 2px solid transparent;
      font-size: 15px;
      letter-spacing: 0.3px;
    }

    .navegacao-superior a:hover {
      background-color: #f0f4ee;
      border-color: #5a6b50;
      color: #5a6b50;
    }

    .navegacao-superior a.ativa {
      background-color: #5a6b50;
      color: white;
      border-color: #5a6b50;
      box-shadow: 0 2px 8px rgba(90, 107, 80, 0.3);
    }

    .perfil-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 0 0 12px 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      display: flex;
      gap: 40px;
      max-height: 80vh;
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
      display: flex;
      flex-direction: column;
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

    /* Estilos para a seção de compras */
    .compras-container {
      width: 100%;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .compras-container h3 {
      font-size: 18px;
      color: #5a6b50;
      margin-bottom: 20px;
      font-weight: 600;
      padding-bottom: 8px;
      border-bottom: 2px solid #eaeaea;
      flex-shrink: 0;
    }

    .compras-lista {
      flex: 1;
      overflow-y: auto;
      padding-right: 10px;
      max-height: 500px;
    }

    /* Custom scrollbar */
    .compras-lista::-webkit-scrollbar {
      width: 6px;
    }

    .compras-lista::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .compras-lista::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 10px;
    }

    .compras-lista::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    .compra-item {
      border: 1px solid #eaeaea;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      background-color: #f9f9f9;
    }

    .compra-item:last-child {
      margin-bottom: 0;
    }

    .compra-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eaeaea;
    }

    .compra-id {
      font-weight: bold;
      color: #5a6b50;
    }

    .compra-data {
      color: #666;
    }

    .compra-status {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
    }

    .status-entregue {
      background-color: #e6f4ea;
      color: #137333;
    }

    .status-pendente {
      background-color: #fef7e0;
      color: #b06000;
    }

    .status-cancelado {
      background-color: #fce8e6;
      color: #c5221f;
    }

    .produto-item {
      display: flex;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f0f0f0;
    }

    .produto-item:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .produto-imagem {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 15px;
    }

    .produto-info {
      flex: 1;
    }

    .produto-nome {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .produto-detalhes {
      color: #666;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .produto-preco {
      font-weight: bold;
      color: #5a6b50;
    }

    .compra-total {
      text-align: right;
      margin-top: 15px;
      font-weight: bold;
      font-size: 18px;
      color: #333;
    }

    .sem-compras {
      text-align: center;
      padding: 40px 0;
      color: #666;
    }

    .sem-compras p {
      margin-bottom: 20px;
    }

    .btn-comprar {
      display: inline-block;
      background-color: #5a6b50;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      transition: background-color 0.2s ease;
    }

    .btn-comprar:hover {
      background-color: #4a5a40;
    }

    @media (max-width: 768px) {
      .container-principal {
        margin-top: 10px;
      }
      
      .perfil-container {
        flex-direction: column;
        gap: 30px;
        padding: 30px;
        max-height: none;
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
      
      .navegacao-superior {
        padding: 15px 20px;
        justify-content: center;
      }

      .compra-header {
        flex-direction: column;
        gap: 10px;
      }

      .compras-lista {
        max-height: 400px;
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
      
      .navegacao-superior {
        flex-direction: column;
        gap: 10px;
        padding: 15px;
      }

      .navegacao-superior a {
        text-align: center;
      }

      .produto-item {
        flex-direction: column;
      }

      .produto-imagem {
        width: 100%;
        height: 150px;
        margin-right: 0;
        margin-bottom: 10px;
      }

      .compras-lista {
        max-height: 350px;
      }
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container-principal">
    <div class="navegacao-superior">
      <a href="?aba=perfil" class="<?php echo $aba_ativa === 'perfil' ? 'ativa' : ''; ?>">Meu Perfil</a>
      <a href="?aba=pedidos" class="<?php echo $aba_ativa === 'pedidos' ? 'ativa' : ''; ?>">Meus Pedidos</a>
    </div>
    
    <div class="perfil-container">
      <div class="perfil-lateral">
        <?php if ($foto_de_perfil): ?>
          <img src="data:image/jpeg;base64,<?= base64_encode($foto_de_perfil) ?>" class="perfil-foto">
        <?php else: ?>
          <img src="imgs/usuario.jpg" alt="Foto de Perfil" class="perfil-foto">
        <?php endif; ?>

        <h1><?php echo htmlspecialchars($usuario['nome']); ?></h1>
        <div class="user-role">Membro desde 2025</div>
        
        <div class="btn-container">
          <?php if ($aba_ativa === 'perfil'): ?>
            <!-- Botões que aparecem apenas na aba "Meu Perfil" -->
            <a href="editar_perfil.php" class="btn-normie">
              <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
              </svg>
              EDITAR PERFIL
            </a>

            <a href="deletar.php" onclick="return confirm('Tem certeza que deseja desativar sua conta?');" class="btn-sair">
              <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
              </svg>
              DESATIVAR CONTA
            </a>
          <?php endif; ?>

          <!-- Botão "VOLTAR AO INÍCIO" aparece em ambas as abas -->
          <a href="../../index.php" class="btn-voltar">
            ⬅ VOLTAR AO INÍCIO
          </a>
        </div>
      </div>

      <div class="perfil-principal">
        <?php if ($aba_ativa === 'perfil'): ?>
          <div class="info-container">
            <div class="info-group">
              <h3>INFORMAÇÕES PESSOAIS</h3>
              <div class="info-linha">
              <strong>Nome completo:</strong>
              <span><?php echo htmlspecialchars($usuario['nome_completo'] ?? ''); ?></span>
              </div>

              <div class="info-linha">
              <strong>Email:</strong>
              <span><?php echo htmlspecialchars($usuario['email']); ?></span>
              </div>

              <div class="info-linha">
              <strong>Senha:</strong>
              <input type="password" id="senhaInput" value="123456789" readonly>
              </div>

              <div class="info-linha">
              <strong>CPF:</strong>
              <input type="text" name="cpf" id="cpf" value="<?= htmlspecialchars($usuario['cpf'] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="info-group">
              <h3>ENDEREÇO</h3>
              <div class="info-linha">
              <strong>CEP</strong>
              <input type="text" name="cep" id="cep" value="<?= htmlspecialchars($usuario['cep'] ?? '') ?>" readonly class="empty-field">
              </div>

              <div class="info-linha">
                <strong>Estado</strong>
                <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($usuario['estado'] ?? '') ?>" readonly class="empty-field">
              </div>

              <div class="info-linha">
                <strong>Cidade</strong>
                <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($usuario['cidade'] ?? '') ?>" readonly class="empty-field">
              </div>

              <div class="info-linha">
                <strong>Bairro</strong>
                <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($usuario['bairro'] ?? '') ?>" readonly class="empty-field">
              </div>

              <div class="info-linha">
                <strong>Rua</strong>
                <input type="text" name="rua" id="rua" value="<?= htmlspecialchars($usuario['rua'] ?? '') ?>" readonly class="empty-field">
              </div>

              <div class="info-linha">
                <strong>Número</strong>
                <input type="text" name="numero" id="numero" value="<?= htmlspecialchars($usuario['numero'] ?? '') ?>" readonly class="empty-field">
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="compras-container">
            <h3>MEUS PEDIDOS</h3>

            <div class="compras-lista">
              <?php
                // Buscar pedidos do usuário logado
                $stmt = $conn->prepare("
                    SELECT 
                        p.idpedido,
                        p.data_pedido,
                        p.valor_total,
                        p.frete,
                        p.servico_frete,
                        p.prazo_entrega,
                        p.codigo_rastreio,
                        p.status,
                        p.status_envio
                    FROM pedido p
                    WHERE p.idusuario = :idusuario
                    ORDER BY p.idpedido DESC
                ");
                $stmt->bindValue(':idusuario', $usuario['idusuario'], PDO::PARAM_INT);
                $stmt->execute();
                $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($pedidos) {
                    foreach ($pedidos as $pedido) {
                        echo '<div class="compra-item">';
                        echo '<div class="compra-header">';
                        echo '<div class="compra-id">Pedido #' . $pedido['idpedido'] . '</div>';
                        echo '<div class="compra-data">' . date('d/m/Y', strtotime($pedido['data_pedido'])) . '</div>';

                        // Cor de status baseada no envio
                        $classeStatus = match($pedido['status_envio']) {
                            'entregue' => 'status-entregue',
                            'enviado' => 'status-pendente',
                            'aguardando envio' => 'status-pendente',
                            'cancelado' => 'status-cancelado',
                            default => 'status-pendente'
                        };

                        echo '<div class="compra-status ' . $classeStatus . '">' . strtoupper($pedido['status_envio']) . '</div>';
                        echo '</div>';

                        // Itens do pedido
                        $itensStmt = $conn->prepare("
                            SELECT 
                                i.quantidade,
                                pr.nome,
                                pr.preco,
                                (SELECT img.imagem FROM imagens img WHERE img.idproduto = pr.idproduto LIMIT 1) AS imagem
                            FROM item_pedido i
                            JOIN produto pr ON i.idproduto = pr.idproduto
                            WHERE i.idpedido = :idpedido
                        ");
                        $itensStmt->bindValue(':idpedido', $pedido['idpedido'], PDO::PARAM_INT);
                        $itensStmt->execute();
                        $itens = $itensStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($itens as $item) {
                            $imagemBase64 = $item['imagem']
                                ? 'data:image/jpeg;base64,' . base64_encode($item['imagem'])
                                : '../../imgs/capa.jpg';

                            echo '<div class="produto-item">';
                            echo '<img src="' . $imagemBase64 . '" alt="Produto" class="produto-imagem">';
                            echo '<div class="produto-info">';
                            echo '<div class="produto-nome">' . htmlspecialchars($item['nome']) . '</div>';
                            echo '<div class="produto-detalhes">Quantidade: ' . $item['quantidade'] . '</div>';
                            echo '<div class="produto-preco">R$ ' . number_format($item['preco'], 2, ',', '.') . '</div>';
                            echo '</div>';
                            echo '</div>';
                        }

                        // Informações do frete
                        echo '<div class="frete-detalhes">';
                        echo '<p><strong>Frete:</strong> R$ ' . number_format($pedido['frete'], 2, ',', '.') . '</p>';
                        echo '<p><strong>Serviço de entrega:</strong> ' . htmlspecialchars($pedido['servico_frete'] ?? 'N/A') . '</p>';
                        echo '<p><strong>Prazo de entrega:</strong> ' . ($pedido['prazo_entrega'] ? $pedido['prazo_entrega'] . ' dias úteis' : 'Não informado') . '</p>';
                        if (!empty($pedido['codigo_rastreio'])) {
                            echo '<p><strong>Código de rastreio:</strong> ' . htmlspecialchars($pedido['codigo_rastreio']) . '</p>';
                        }
                        echo '</div>';

                        // Total
                        echo '<div class="compra-total">Total: R$ ' . number_format($pedido['valor_total'], 2, ',', '.') . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Nenhum pedido encontrado.</p>';
                }
              ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
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