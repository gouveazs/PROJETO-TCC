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
        // Guarde o ID e o nome do vendedor corretamente na sessão
        $_SESSION['id_vendedor'] = $usuario_db['idvendedor'];
        $_SESSION['nome_vendedor'] = $usuario_db['nome_completo'];

        // guarda sessao do nome e foto 
        $_SESSION['nome_usuario'] = $usuario_db['nome_completo'];
        $_SESSION['tipo'] = 'Vendedor'; 
        $_SESSION['foto_de_perfil'] = $usuario_db['foto_de_perfil'] ?? 'imgs/usuario.jpg';

        header('Location: ../index.php');
        exit();
    } else {
        header('Location: loginVendedor.php?error=1');
        exit();
    }
} catch (PDOException $e) {
    // Debug apenas: não exiba em produção!
    echo "Erro na consulta: " . $e->getMessage();
    die();
}
?>