<?php
session_start();

$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;
$foto_de_perfil = $_SESSION['foto_de_perfil_vendedor'] ?? null;
$id_vendedor = $_SESSION['id_vendedor'] ?? null;

include '../conexao.php';

// Busca as conversas do vendedor
$stmt = $conn->prepare("SELECT * FROM conversa WHERE idvendedor = ?");
$stmt->execute([$id_vendedor]);
$conversas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- NOVO BLOCO ---
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de conversas - Painel do Livreiro</title>
</head>
<body>
    <a href="painel_livreiro.php">Voltar</a>
    <h1>Suas conversas:</h1>
    <?php foreach ($conversas as $c): 
        $idusuario = $c['idusuario'];
        $user = $usuarios[$idusuario] ?? null;
        $nome_usuario = $user['nome'] ?? 'Usuário desconhecido';
        $foto_usuario = $user['foto_de_perfil'] ?? null;
    ?>
        <div class="info" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; border-radius: 8px;">
            <!-- Mostra a foto -->
            <?php if ($foto_usuario): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($foto_usuario) ?>" 
                    alt="Foto de <?= htmlspecialchars($nome_usuario) ?>" 
                    width="50" height="50" style="border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <img src="../img/default-user.png" alt="Sem foto" width="50" height="50" style="border-radius: 50%; object-fit: cover;">
            <?php endif; ?>

            <!-- Mostra o nome -->
            <h3><?= htmlspecialchars($nome_usuario) ?></h3>

            <!-- Infos da conversa -->
            <p>ID da conversa: <?= htmlspecialchars($c['idconversa']) ?></p>
            <p>Data de criação: <?= htmlspecialchars($c['criado_em']) ?></p>
            <p>Status: <?= htmlspecialchars($c['status']) ?></p>

            <!-- Link para o chat -->
            <a href="../chat/chat.php?idconversa=<?= $c['idconversa'] ?>&idusuario=<?= $c['idusuario'] ?>&idvendedor=<?= $id_vendedor ?>&remetente_tipo=vendedor">
                Ir para a conversa
            </a>
            <hr>
        </div>
    <?php endforeach; ?>

</body>
</html>