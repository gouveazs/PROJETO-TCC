<?php
require '../conexao.php'; // AJUSTE O CAMINHO

session_start();

if (!isset($_SESSION['idusuario'])) {
    die("Você precisa estar logado.");
}

$idusuario = $_SESSION['idusuario'];
$idproduto = $_GET['idproduto'] ?? null;
$idpedido = $_GET['idpedido'] ?? null;
$idvendedor = $_GET['idvendedor'] ?? null;

if (!$idproduto || !$idpedido || !$idvendedor) {
    die("Dados inválidos.");
}

// VERIFICAR SE O POST FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nota = intval($_POST['nota']);
    $comentario = trim($_POST['comentario']);

    // INSERIR NA TABELA DE AVALIAÇÕES
    $stmt = $conn->prepare("
        INSERT INTO avaliacoes (nota, comentario, idusuario, idvendedor)
        VALUES (:nota, :comentario, :idusuario, :idvendedor)
    ");
    $stmt->bindValue(':nota', $nota, PDO::PARAM_INT);
    $stmt->bindValue(':comentario', $comentario);
    $stmt->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
    $stmt->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $stmt->execute();

    // Buscar nome do produto
    $nomeProdutoStmt = $conn->prepare("
        SELECT nome FROM produto WHERE idproduto = :idproduto
    ");
    $nomeProdutoStmt->bindValue(':idproduto', $idproduto, PDO::PARAM_INT);
    $nomeProdutoStmt->execute();
    $nomeProduto = $nomeProdutoStmt->fetchColumn();

    if (!$nomeProduto) {
        $nomeProduto = "Produto desconhecido";
    }

    // CRIAR NOTIFICAÇÃO PARA O VENDEDOR
    $mensagem = "O usuário fez uma avaliação do produto \"$nomeProduto\" (Pedido #$idpedido).";

    $notif = $conn->prepare("
        INSERT INTO notificacoes (mensagem, lida, idusuario, idvendedor, tipo)
        VALUES (:mensagem, 0, :idusuario, :idvendedor, 'avaliação')
    ");
    $notif->bindValue(':mensagem', $mensagem);
    $notif->bindValue(':idusuario', $idusuario, PDO::PARAM_INT);
    $notif->bindValue(':idvendedor', $idvendedor, PDO::PARAM_INT);
    $notif->execute();

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
.container {
    width: 400px;
    margin: 40px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #ccc;
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
    padding: 10px;
    border: none;
    width: 100%;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #218838;
}
</style>
</head>
<body>

<div class="container">
    <h3>Avaliar Produto</h3>

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
        <textarea name="comentario" rows="5" placeholder="Escreva sua opinião..." required></textarea>

        <button type="submit">Enviar Avaliação</button>
    </form>
</div>

</body>
</html>
