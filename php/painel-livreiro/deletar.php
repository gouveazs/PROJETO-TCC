<?php
session_start();
include '../conexao.php'; 

// Verifica se o vendedor está logado
$nome_vendedor = $_SESSION['nome_vendedor'] ?? null;

if (!$nome_vendedor) {
    header("Location: ../../login/loginVendedor.php");
    exit;
}

// Buscar o ID do vendedor
$stmt = $conn->prepare("SELECT idvendedor FROM vendedor WHERE nome_completo = ?");
$stmt->execute([$nome_vendedor]);
$vendedor = $stmt->fetch(PDO::FETCH_ASSOC);
$idvendedor = $vendedor['idvendedor'] ?? null;

if (!$idvendedor) {
    echo "<p style='color:red;'>Vendedor não encontrado.</p>";
    exit;
}

// Atualizar status para desativado
$stmt = $conn->prepare("UPDATE vendedor SET status = 'desativado' WHERE idvendedor = ?");
if ($stmt->execute([$idvendedor])) {
    // Encerra a sessão
    session_destroy();

    echo "<div style='text-align:center; margin-top:50px;'>";
    echo "<img src='https://media.tenor.com/URShj_JWaS8AAAAM/explos%C3%A3o-meme.gif' alt='Explosão' />";
    echo "<br><br>";
    echo "<a href='../../index.php' style='text-decoration:none; font-size:18px; color:#5a6b50;'>Voltar à página inicial</a>";
    echo "</div>";
} else {
    echo "<p style='color:red; text-align:center; margin-top:50px;'>Erro ao desativar a conta. Tente novamente.</p>";
}
?>
