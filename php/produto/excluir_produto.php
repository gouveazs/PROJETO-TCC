<?php
session_start();
include '../conexao.php'; 

$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;
if (!$nome_vendedor) {
    header('Location: ../login/loginVendedor.php');
    exit;
}

$idproduto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idproduto <= 0) {
    echo "Produto não encontrado.";
    exit;
}

// expurga as imagens primeiro
$stmt_img = $conn->prepare("DELETE FROM imagens WHERE idproduto = ?");
$stmt_img->execute([$idproduto]);

// depois expurga o produto
$stmt_prod = $conn->prepare("DELETE FROM produto WHERE idproduto = ?");
if ($stmt_prod->execute([$idproduto])) {
    echo "<img src='https://media.tenor.com/URShj_JWaS8AAAAM/explos%C3%A3o-meme.gif' alt='Produto deletado'/>";
    echo "<br>";
    echo "<a href='../painel-livreiro/anuncios.php'>Voltar à página de anúncios</a>";
} else {
    echo "<p style='color:red;'>Erro ao deletar produto.</p>";
}
?>
