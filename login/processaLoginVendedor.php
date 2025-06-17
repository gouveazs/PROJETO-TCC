<?php
include '../php/conexaoVendedor.php';

session_start(); // Coloque antes de qualquer output

$nome_completo = htmlspecialchars($_POST['nome_completo']);
$senha = htmlspecialchars($_POST['senha']);

try {
    $stmt = $conn->prepare("SELECT * FROM cadastro_vendedor WHERE nome_completo = ? AND senha = ?");
    $stmt->execute([$nome_completo, $senha]);
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_db) {
        $_SESSION['usuario_logado'] = $usuario_db['id'];
        $_SESSION['nome_vendedor'] = $usuario_db['nome_completo'];
        header('Location: ../index.php');
        exit();
    } else {
        // Use o caminho correto para o seu formulário de login de vendedor
        header('Location: loginVendedor.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    // Debug apenas: não exiba em produção!
    echo "Erro na consulta: " . $e->getMessage();
    die();
}
?>