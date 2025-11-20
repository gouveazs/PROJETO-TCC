<?php
session_start();

$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;
$foto_de_perfil = $_SESSION['foto_de_perfil_vendedor'] ?? null;
$id_vendedor = $_SESSION['id_vendedor'] ?? null;

// Verifica se o vendedor está logado
if (!$id_vendedor) {
    header("Location: login.php");
    exit();
}

include '../conexao.php';

// Busca as conversas do vendedor
$stmt = $conn->prepare("SELECT * FROM conversa WHERE idvendedor = ? ORDER BY criado_em DESC");
$stmt->execute([$id_vendedor]);
$conversas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca os dados dos usuários envolvidos em cada conversa
$usuarios = [];
foreach ($conversas as $c) {
    $idusuario = $c['idusuario'];
    if (!isset($usuarios[$idusuario])) {
        $stmt = $conn->prepare("SELECT nome, foto_de_perfil FROM usuario WHERE idusuario = ?");
        $stmt->execute([$idusuario]);
        $usuarios[$idusuario] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversas - Painel do Livreiro</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Playfair Display', serif;
            background-color: #F4F1EE;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #5a6b50;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-family: 'Playfair Display', serif;
            border: 1px solid #5a6b50;
        }

        .back-button:hover {
            background-color: #5a6b50;
            color: white;
        }

        h1 {
            color: #5a6b50;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            flex-grow: 1;
        }

        .conversas-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .conversa-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(90, 107, 80, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            display: grid;
            grid-template-columns: 70px 1fr auto;
            grid-template-rows: auto auto;
            gap: 15px;
            align-items: center;
            border-left: 4px solid #5a6b50;
        }

        .conversa-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(90, 107, 80, 0.15);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #5a6b50;
            grid-row: 1 / 3;
            grid-column: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 18px;
            color: #5a6b50;
            grid-row: 1;
            grid-column: 2;
            align-self: end;
        }

        .conversa-details {
            display: flex;
            gap: 15px;
            font-size: 14px;
            color: #6c757d;
            grid-row: 2;
            grid-column: 2;
            align-self: start;
        }

        .conversa-details span {
            background-color: #F4F1EE;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #e0dcd9;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-ativa {
            background-color: #e8f5e8;
            color: #2d5016;
            border: 1px solid #5a6b50;
        }

        .status-inativa {
            background-color: #f8d7da;
            color: #721c24;
        }

        .chat-button {
            background-color: #5a6b50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-align: center;
            grid-row: 1 / 3;
            grid-column: 3;
            height: fit-content;
        }

        .chat-button:hover {
            background-color: #4a5a40;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
            font-family: 'Playfair Display', serif;
        }

        .empty-state h3 {
            color: #5a6b50;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 24px;
        }

        .empty-state p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="painel_livreiro.php" class="back-button">
                ← Voltar ao Painel
            </a>
            <h1>Suas Conversas</h1>
            <div style="width: 120px;"></div> <!-- Espaço para alinhamento -->
        </div>

        <?php if (empty($conversas)): ?>
            <div class="empty-state">
                <h3>Nenhuma conversa encontrada</h3>
                <p>Quando clientes entrarem em contato, as conversas aparecerão aqui.</p>
            </div>
        <?php else: ?>
            <div class="conversas-grid">
                <?php foreach ($conversas as $c): 
                    $idusuario = $c['idusuario'];
                    $user = $usuarios[$idusuario] ?? null;
                    $nome_usuario = $user['nome'] ?? 'Usuário desconhecido';
                    $foto_usuario = $user['foto_de_perfil'] ?? null;
                    $status_class = $c['status'] === 'ativa' ? 'status-ativa' : 'status-inativa';
                ?>
                    <div class="conversa-card">
                        <?php if ($foto_usuario): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($foto_usuario) ?>" 
                                alt="Foto de <?= htmlspecialchars($nome_usuario) ?>" 
                                class="user-avatar">
                        <?php else: ?>
                            <img src="../img/default-user.png" alt="Sem foto" class="user-avatar">
                        <?php endif; ?>

                        <div class="user-name"><?= htmlspecialchars($nome_usuario) ?></div>
                        
                        <div class="conversa-details">
                            <span>ID: <?= htmlspecialchars($c['idconversa']) ?></span>
                            <span>Data: <?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></span>
                            <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($c['status']) ?></span>
                        </div>

                        <a href="../chat/chat.php?idconversa=<?= $c['idconversa'] ?>&idusuario=<?= $c['idusuario'] ?>&idvendedor=<?= $id_vendedor ?>&remetente_tipo=vendedor" 
                           class="chat-button">
                            Abrir Chat
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>