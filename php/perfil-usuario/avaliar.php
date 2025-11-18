<?php
require '../conexao.php';

session_start();

if (!isset($_SESSION['idusuario'])) {
    die("Você precisa estar logado.");
}
$idusuario = $_SESSION['idusuario'];
$idproduto = $_GET['idproduto'] ?? null;
$idpedido = $_GET['idpedido'] ?? null;
$idvendedor = $_GET['idvendedor'] ?? null;

if (!$idproduto || !$idpedido || !$idvendedor) {
    die("Dados inválidos. Volte e tente novamente.");
}

// Recupera dados do produto para exibir na tela
$infoStmt = $conn->prepare("
    SELECT nome, autor FROM produto WHERE idproduto = :idproduto
");
$infoStmt->bindValue(':idproduto', $idproduto, PDO::PARAM_INT);
$infoStmt->execute();
$produtoRow = $infoStmt->fetch(PDO::FETCH_ASSOC);

$nomeProduto = $produtoRow['nome'] ?? "Produto desconhecido";
$autorProduto = $produtoRow['autor'] ?? "";

// POST: salvar avaliação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = intval($_POST['nota']);
    $comentario = trim($_POST['comentario']);

    // Previne flood: impede mais de uma avaliação igual pro mesmo vendedor/pedido
    $check = $conn->prepare("
        SELECT COUNT(*) FROM avaliacoes 
        WHERE idusuario = :idusuario AND idvendedor = :idvendedor
    ");
    $check->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
    $check->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $check->execute();
    if ($check->fetchColumn() > 0) {
        echo "<script>alert('Você já avaliou este vendedor!'); window.location.href='ver_perfil.php?aba=Meus%20Pedidos';</script>";
        exit;
    }

    // Insere a avaliação
    $stmt = $conn->prepare("
        INSERT INTO avaliacoes (nota, comentario, idusuario, idvendedor)
        VALUES (:nota, :comentario, :idusuario, :idvendedor)
    ");
    $stmt->bindValue(':nota', $nota, PDO::PARAM_INT);
    $stmt->bindValue(':comentario', $comentario);
    $stmt->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
    $stmt->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $stmt->execute();

    // Define o ajuste para reputação conforme a nota dada
    $ajuste = 0;
    switch ($nota) {
        case 5: $ajuste = 8; break;
        case 4: $ajuste = 4; break;
        case 3: $ajuste = 2; break;
        case 2: $ajuste = -2; break;
        case 1: $ajuste = -8; break;
    }

    $stmt = $conn->prepare("
        UPDATE vendedor
        SET reputacao = GREATEST(0, reputacao + :ajuste)
        WHERE idvendedor = :idvendedor
    ");
    $stmt->bindValue(':ajuste', $ajuste, PDO::PARAM_INT);
    $stmt->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $stmt->execute();

    // Notificação para vendedor
    $mensagem = "O usuário fez uma avaliação: \"$nomeProduto\" (Pedido #$idpedido).";
    $notif = $conn->prepare("
        INSERT INTO notificacoes (mensagem, lida, idusuario, idvendedor, tipo)
        VALUES (:mensagem, 0, :idusuario, :idvendedor, 'avaliação')
    ");
    $notif->bindValue(':mensagem', $mensagem);
    $notif->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
    $notif->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $notif->execute();

    // Mensagem de sucesso
    echo "<script>alert('Avaliação enviada com sucesso!'); window.location.href='ver_perfil.php?aba=Meus%20Pedidos';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Fazer Avaliação</title>
<style>
body { background: #f4f4fc; }
.container {
    width: 410px;
    margin: 50px auto;
    padding: 22px 26px 18px 26px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #bbb;
    box-shadow: 0 8px 32px #0001;
}
input, textarea, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
}
button {
    background: #28a745;
    color: #fff;
    padding: 11px;
    border: none;
    width: 100%;
    border-radius: 7px;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: bold;
}
button:hover {
    background: #218838;
}
#produtoHead {
    background: #f9f9f9; padding: 10px; border-radius: 7px; border: 1px solid #eee;margin-bottom:18px;
    font-size: 1.06em; color: #444;
}
</style>
</head>
<body>

<div class="container">
    <div id="produtoHead">
        <b>Produto:</b> <?= htmlspecialchars($nomeProduto) ?>
        <?= $autorProduto ? ("<br><b>Autor:</b> ".htmlspecialchars($autorProduto)) : "" ?>
        <br><b>Pedido:</b> #<?= htmlspecialchars($idpedido) ?>
    </div>
    <h3>Avaliar Vendedor</h3>
    <form method="POST">
        <label>Nota:</label>
        <select name="nota" required>
            <option value="5">⭐⭐⭐⭐⭐ - Excelente</option>
            <option value="4">⭐⭐⭐⭐ - Muito bom</option>
            <option value="3">⭐⭐⭐ - Bom</option>
            <option value="2">⭐⭐ - Ruim</option>
            <option value="1">⭐ - Péssimo</option>
        </select>

        <label>Comentário:</label>
        <textarea name="comentario" rows="5" placeholder="Escreva sua opinião sobre o vendedor..." required></textarea>

        <button type="submit">Enviar Avaliação</button>
    </form>
</div>

</body>
</html>