<?php
session_start();

$nome_vendedor = isset($_SESSION['nome_vendedor']) ? $_SESSION['nome_vendedor'] : null;
$foto_de_perfil = isset($_SESSION['foto_de_perfil_vendedor']) ? $_SESSION['foto_de_perfil_vendedor'] : null;
$id_vendedor = isset($_SESSION['id_vendedor']) ? $_SESSION['id_vendedor'] : null;

include '../conexao.php';

$stmt = $conn->prepare("SELECT * FROM conversa WHERE idvendedor = ?");
$stmt->execute([$id_vendedor]);
$conversa = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de conversas - Painel do Livreiro</title>
</head>
<body>
    <h1>Suas conversas:</h1>
    <?php foreach($conversa as $c): ?>
        <div class="info">
            <h3>ID: <?= htmlspecialchars($c['idconversa']) ?></h3>
            <p class="price">Data de criação: <?= $c['criado_em']?></p>
            <p class="price">Status: <?= $c['status']?></p>
            <p class="price">ID Usuario: <?= $c['idusuario']?></p>
            <a href="../chat/chat.php?idconversa=<?= $c['idconversa'] ?>&remetente_tipo=vendedor" class="card-link">Ir para a conversa</a>
        </div>
    <?php endforeach; ?>
</body>
</html>